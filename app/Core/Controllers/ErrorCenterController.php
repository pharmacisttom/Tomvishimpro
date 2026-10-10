<?php

namespace Tomvis\HIMPro\Core\Controllers;

use Tomvis\HIMPro\Core\Request\Request;
use Tomvis\HIMPro\Core\Response\Response;
use Tomvis\HIMPro\Core\Response\JsonResponse;
use Tomvis\HIMPro\Core\Database\DatabaseManager;
use Tomvis\HIMPro\Core\Exceptions\ErrorIngestionService;
use Tomvis\HIMPro\Core\Exceptions\ErrorSanitizer;
use Tomvis\HIMPro\Core\Services\FrameworkIssueTrackerService;
use Tomvis\HIMPro\Core\Services\AIRepairAssistantService;
use Tomvis\HIMPro\Core\Services\FrameworkPatchService;
use PDO;
use Exception;

class ErrorCenterController
{
    protected ?DatabaseManager $dbManager;

    public function __construct(?DatabaseManager $dbManager = null)
    {
        $this->dbManager = $dbManager;
    }

    public function index(Request $request): Response
    {
        $this->authorize($request, 'error.view');

        $filterApp = $request->get('app_id', '');
        $filterSeverity = $request->get('severity', '');
        $filterStatus = $request->get('status', '');

        $metrics = $this->getMetrics();
        $incidents = $this->getIncidents($filterApp, $filterSeverity, $filterStatus);

        ob_start();
        $data = [
            'metrics' => $metrics,
            'incidents' => $incidents,
            'filterApp' => $filterApp,
            'filterSeverity' => $filterSeverity,
            'filterStatus' => $filterStatus,
        ];
        extract($data);
        include dirname(__DIR__, 2) . '/resources/views/admin/error_center.php';
        $html = ob_get_clean();

        return new Response($html);
    }

    public function incidentDetail(Request $request): Response
    {
        $this->authorize($request, 'error.view');
        $id = (int)$request->get('id', 0);

        $pdo = $this->getPdo();
        $stmt = $pdo->prepare("SELECT * FROM error_groups WHERE id = ?");
        $stmt->execute([$id]);
        $group = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$group) {
            return new Response("Incident #{$id} not found.", 404);
        }

        // Fetch events
        $eventStmt = $pdo->prepare("SELECT * FROM error_events WHERE group_id = ? ORDER BY id DESC LIMIT 20");
        $eventStmt->execute([$id]);
        $events = $eventStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch RCA
        $rcaStmt = $pdo->prepare("SELECT * FROM error_root_causes WHERE group_id = ? ORDER BY id DESC LIMIT 1");
        $rcaStmt->execute([$id]);
        $rca = $rcaStmt->fetch(PDO::FETCH_ASSOC);

        // Fetch Fix Proposals
        $propStmt = $pdo->prepare("SELECT * FROM framework_fix_proposals WHERE group_id = ? ORDER BY id DESC");
        $propStmt->execute([$id]);
        $proposals = $propStmt->fetchAll(PDO::FETCH_ASSOC);

        // Run AI Repair Assistant preview if no proposals exist yet
        $aiAssistant = new AIRepairAssistantService(true);
        $aiAnalysis = $aiAssistant->analyzeAndProposeFix($events[0] ?? [
            'error_type' => $group['exception_class'],
            'sanitized_message' => $group['exception_class'] . ' in ' . $group['source_component'],
            'sanitized_trace' => 'Trace at ' . $group['source_component'],
            'source_component' => $group['source_component'],
        ]);

        ob_start();
        extract([
            'group' => $group,
            'events' => $events,
            'rca' => $rca,
            'proposals' => $proposals,
            'aiAnalysis' => $aiAnalysis,
        ]);
        include dirname(__DIR__, 2) . '/resources/views/admin/error_center_detail.php';
        $html = ob_get_clean();

        return new Response($html);
    }

    public function metricsApi(Request $request): JsonResponse
    {
        $this->authorize($request, 'error.view');
        return JsonResponse::success($this->getMetrics(), 'Error Center metrics retrieved.');
    }

    public function triageApi(Request $request): JsonResponse
    {
        $this->authorize($request, 'error.triage');

        $groupId = (int)$request->input('group_id', 0);
        $newStatus = (string)$request->input('status', 'Triaged');

        $tracker = new FrameworkIssueTrackerService($this->dbManager);
        $tracker->updateStatus($groupId, $newStatus);

        return JsonResponse::success([
            'group_id' => $groupId,
            'new_status' => $newStatus,
        ], "Incident #{$groupId} status updated to {$newStatus}.");
    }

    public function proposeFixApi(Request $request): JsonResponse
    {
        $this->authorize($request, 'framework.fix.propose');

        $groupId = (int)$request->input('group_id', 0);
        $title = (string)$request->input('title', 'Proposed Core Patch');
        $description = (string)$request->input('description', 'Automated patch proposal');
        $diffPatch = (string)$request->input('diff_patch', '');
        $unitTestCode = (string)$request->input('unit_test_code', '');

        $tracker = new FrameworkIssueTrackerService($this->dbManager);
        $proposalId = $tracker->linkFixProposal($groupId, $title, $description, $diffPatch, $unitTestCode);

        return JsonResponse::success([
            'proposal_id' => $proposalId,
            'group_id' => $groupId,
            'title' => $title,
        ], "Framework fix proposal created successfully.");
    }

    public function approveReleaseApi(Request $request): JsonResponse
    {
        $this->authorize($request, 'framework.release.approve');

        $patchVersion = (string)$request->input('patch_version', '3.0.1');
        $releaseNotes = (string)$request->input('release_notes', 'Hotfix release');

        $patchService = new FrameworkPatchService($this->dbManager);
        $releaseId = $patchService->createPatchRelease($patchVersion, $releaseNotes);
        $patchService->approveRelease($releaseId);
        $patchService->releasePatch($releaseId);

        return JsonResponse::success([
            'release_id' => $releaseId,
            'patch_version' => $patchVersion,
            'status' => 'Released',
        ], "Framework patch {$patchVersion} approved and released safely.");
    }

    public function ingestJsErrorApi(Request $request): JsonResponse
    {
        $rawMsg = (string)$request->input('message', 'Client JS Error');
        $rawUrl = (string)$request->input('url', 'window');
        $rawTrace = (string)$request->input('stack', '');

        $ingest = new ErrorIngestionService($this->dbManager);
        $result = $ingest->ingest([
            'exception_class' => 'ClientJSError',
            'normalized_code' => '500',
            'source_component' => ErrorSanitizer::sanitizeText($rawUrl),
            'sanitized_message' => ErrorSanitizer::sanitizeText($rawMsg),
            'sanitized_trace' => ErrorSanitizer::sanitizeText($rawTrace),
            'app_id' => 'ClientFrontend',
            'module_id' => 'JSApp',
            'severity' => 'Low',
        ]);

        return JsonResponse::success($result, 'Client JS error ingested safely.');
    }

    protected function getMetrics(): array
    {
        try {
            $pdo = $this->getPdo();
            $totalErrors = (int)$pdo->query("SELECT SUM(occurrence_count) FROM error_groups")->fetchColumn();
            $newIssues = (int)$pdo->query("SELECT COUNT(*) FROM error_groups WHERE status = 'Detected'")->fetchColumn();
            $criticalIncidents = (int)$pdo->query("SELECT COUNT(*) FROM error_groups WHERE severity = 'Critical'")->fetchColumn();
            $recurringIssues = (int)$pdo->query("SELECT COUNT(*) FROM error_groups WHERE category = 'Recurring Issue'")->fetchColumn();
            $affectedApps = (int)$pdo->query("SELECT COUNT(DISTINCT app_id) FROM error_affected_applications")->fetchColumn();
            $coreDefects = (int)$pdo->query("SELECT COUNT(*) FROM error_groups WHERE category = 'Framework Core Defect' AND status != 'Closed'")->fetchColumn();

            return [
                'total_errors' => max($totalErrors, 0),
                'new_issues' => $newIssues,
                'critical_incidents' => $criticalIncidents,
                'recurring_issues' => $recurringIssues,
                'affected_apps' => max($affectedApps, 1),
                'core_defects' => $coreDefects,
            ];
        } catch (Exception $e) {
            return [
                'total_errors' => 0,
                'new_issues' => 0,
                'critical_incidents' => 0,
                'recurring_issues' => 0,
                'affected_apps' => 1,
                'core_defects' => 0,
            ];
        }
    }

    protected function getIncidents(string $app = '', string $severity = '', string $status = ''): array
    {
        try {
            $pdo = $this->getPdo();
            $sql = "SELECT * FROM error_groups WHERE 1=1";
            $params = [];

            if (!empty($severity)) {
                $sql .= " AND severity = ?";
                $params[] = $severity;
            }
            if (!empty($status)) {
                $sql .= " AND status = ?";
                $params[] = $status;
            }

            $sql .= " ORDER BY last_seen_at DESC LIMIT 50";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    protected function authorize(Request $request, string $permission): void
    {
        // Enforce RBAC permission checks
        $user = $request->user();
        if ($user && method_exists($user, 'hasPermission')) {
            if (!$user->hasPermission($permission)) {
                throw new Exception("Unauthorized: Missing required permission '{$permission}'");
            }
        }
    }

    protected function getPdo(): PDO
    {
        if ($this->dbManager) {
            return $this->dbManager->connection();
        }
        return (new DatabaseManager())->connection();
    }
}
