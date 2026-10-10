<?php

namespace Tomvis\HIMPro\Core\Exceptions;

use Tomvis\HIMPro\Core\Database\DatabaseManager;
use Tomvis\HIMPro\Core\Services\ErrorNotificationService;
use PDO;
use Exception;

class ErrorIngestionService
{
    protected ?DatabaseManager $dbManager;
    protected LocalErrorBuffer $localBuffer;

    public function __construct(?DatabaseManager $dbManager = null, ?LocalErrorBuffer $localBuffer = null)
    {
        $this->dbManager = $dbManager;
        $this->localBuffer = $localBuffer ?: new LocalErrorBuffer();
    }

    public function ingest(array $rawPayload): array
    {
        // 1. Sanitize payload thoroughly
        $sanitizedMessage = ErrorSanitizer::sanitizeText($rawPayload['sanitized_message'] ?? $rawPayload['message'] ?? 'Unknown Error');
        $sanitizedTrace = ErrorSanitizer::sanitizeText($rawPayload['sanitized_trace'] ?? $rawPayload['trace'] ?? '');
        $exceptionClass = $rawPayload['exception_class'] ?? $rawPayload['error_type'] ?? 'Exception';
        $errorCode = (string)($rawPayload['normalized_code'] ?? $rawPayload['code'] ?? '0');
        $sourceComponent = $rawPayload['source_component'] ?? 'UnknownComponent';
        $frameworkVersion = $rawPayload['framework_version'] ?? '3.0.0';
        $moduleVersion = $rawPayload['module_version'] ?? '1.0.0';
        $appId = $rawPayload['app_id'] ?? 'TomvisHIMPro';
        $moduleId = $rawPayload['module_id'] ?? 'Core';
        $environment = $rawPayload['environment'] ?? 'local';
        $traceId = $rawPayload['trace_id'] ?? uniqid('trace_', true);
        $routeTemplate = $rawPayload['route_template'] ?? '/';
        $severity = $rawPayload['severity'] ?? 'Medium';

        // 2. Generate fingerprint
        $fingerprint = ErrorFingerprintService::generateFingerprint(
            $exceptionClass,
            $errorCode,
            $sourceComponent,
            $sanitizedTrace,
            $frameworkVersion,
            $moduleVersion
        );

        $eventData = [
            'fingerprint' => $fingerprint,
            'exception_class' => $exceptionClass,
            'normalized_code' => $errorCode,
            'source_component' => $sourceComponent,
            'framework_version' => $frameworkVersion,
            'module_version' => $moduleVersion,
            'app_id' => $appId,
            'module_id' => $moduleId,
            'environment' => $environment,
            'trace_id' => $traceId,
            'sanitized_message' => $sanitizedMessage,
            'sanitized_trace' => $sanitizedTrace,
            'route_template' => $routeTemplate,
            'severity' => $severity,
        ];

        // 3. Try database ingestion, fallback to Local Buffer if offline/error
        try {
            $pdo = $this->getPDO();
            if (!$pdo) {
                $this->localBuffer->push($eventData);
                return ['status' => 'buffered', 'reason' => 'Database connection unavailable', 'fingerprint' => $fingerprint];
            }

            // Check if group exists
            $stmt = $pdo->prepare("SELECT id, status, occurrence_count, affected_apps_count FROM error_groups WHERE fingerprint = ?");
            $stmt->execute([$fingerprint]);
            $group = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($group) {
                $groupId = (int)$group['id'];
                $newCount = (int)$group['occurrence_count'] + 1;
                $category = ErrorFingerprintService::classifyCategory($sourceComponent, false, $group['status'] === 'Closed');

                $updateStmt = $pdo->prepare("
                    UPDATE error_groups
                    SET last_seen_at = NOW(), occurrence_count = ?, category = ?
                    WHERE id = ?
                ");
                $updateStmt->execute([$newCount, $category, $groupId]);
            } else {
                $category = ErrorFingerprintService::classifyCategory($sourceComponent, true, false);

                $insertGroupStmt = $pdo->prepare("
                    INSERT INTO error_groups
                    (fingerprint, exception_class, normalized_code, source_component, error_type, severity, category, status, occurrence_count, affected_apps_count)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'Detected', 1, 1)
                ");
                $errorTypeEnum = str_contains($exceptionClass, 'PDO') ? 'PDOException' : 'Exception';
                $insertGroupStmt->execute([
                    $fingerprint,
                    $exceptionClass,
                    $errorCode,
                    $sourceComponent,
                    $errorTypeEnum,
                    $severity,
                    $category
                ]);
                $groupId = (int)$pdo->lastInsertId();
            }

            // Insert Event
            $insertEventStmt = $pdo->prepare("
                INSERT INTO error_events
                (group_id, trace_id, app_id, module_id, framework_version, module_version, environment, sanitized_message, sanitized_trace, route_template)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $insertEventStmt->execute([
                $groupId,
                $traceId,
                $appId,
                $moduleId,
                $frameworkVersion,
                $moduleVersion,
                $environment,
                $sanitizedMessage,
                $sanitizedTrace,
                $routeTemplate
            ]);

            // Track Affected Application
            $appStmt = $pdo->prepare("
                INSERT INTO error_affected_applications (group_id, app_id, environment, first_detected, last_detected)
                VALUES (?, ?, ?, NOW(), NOW())
                ON DUPLICATE KEY UPDATE last_detected = NOW()
            ");
            $appStmt->execute([$groupId, $appId, $environment]);

            // Update affected_apps_count
            $countAppStmt = $pdo->prepare("SELECT COUNT(DISTINCT app_id) as cnt FROM error_affected_applications WHERE group_id = ?");
            $countAppStmt->execute([$groupId]);
            $appsCount = (int)$countAppStmt->fetchColumn();
            $pdo->prepare("UPDATE error_groups SET affected_apps_count = ? WHERE id = ?")->execute([$appsCount, $groupId]);

            // Run Root Cause Analysis Engine
            $rcaResult = RootCauseAnalysisEngine::analyze($eventData);
            $rcaStmt = $pdo->prepare("
                INSERT INTO error_root_causes (group_id, layer, suspected_component, confidence_score, evidence_summary)
                VALUES (?, ?, ?, ?, ?)
            ");
            $rcaStmt->execute([
                $groupId,
                $rcaResult['layer'],
                $rcaResult['suspected_component'],
                $rcaResult['confidence_score'],
                $rcaResult['evidence_summary']
            ]);

            // Trigger Notification if High/Critical severity
            if ($severity === 'Critical' || $severity === 'High') {
                $notifService = new ErrorNotificationService($pdo);
                $notifService->notifyNewIncident($groupId, $exceptionClass, $sanitizedMessage, $severity);
            }

            return [
                'status' => 'ingested',
                'group_id' => $groupId,
                'fingerprint' => $fingerprint,
                'category' => $category,
                'rca' => $rcaResult
            ];

        } catch (Exception $e) {
            // Guarantee failure isolation: push to local buffer, return safe status
            $this->localBuffer->push($eventData);
            return [
                'status' => 'buffered',
                'reason' => 'Ingestion failure: ' . $e->getMessage(),
                'fingerprint' => $fingerprint
            ];
        }
    }

    protected function getPDO(): ?PDO
    {
        try {
            if ($this->dbManager === null) {
                $this->dbManager = new DatabaseManager();
            }
            return $this->dbManager->connection();
        } catch (Exception $e) {
            return null;
        }
    }
}
