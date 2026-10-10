<?php

namespace Tomvis\HIMPro\Core\Exceptions;

class RootCauseAnalysisEngine
{
    /**
     * Analyze an error payload and produce root cause diagnosis.
     * Returns array with: ['layer', 'suspected_component', 'confidence_score', 'evidence_summary', 'suggested_action']
     */
    public static function analyze(array $eventPayload): array
    {
        $exceptionClass = $eventPayload['error_type'] ?? $eventPayload['exception_class'] ?? '';
        $message = $eventPayload['sanitized_message'] ?? '';
        $trace = $eventPayload['sanitized_trace'] ?? '';
        $sourceComponent = $eventPayload['source_component'] ?? '';

        // 1. Check Database Connection Failure / PDO
        if (str_contains($exceptionClass, 'PDOException') || str_contains($message, 'SQLSTATE') || str_contains($sourceComponent, 'ConnectionFactory')) {
            if (str_contains($message, 'Access denied') || str_contains($message, 'Connection refused')) {
                return [
                    'layer' => 'Database Connection Manager',
                    'suspected_component' => 'Tomvis\\HIMPro\\Core\\Database\\ConnectionFactory',
                    'confidence_score' => 0.95,
                    'evidence_summary' => 'Database host or credential handshake failure: ' . substr($message, 0, 150),
                    'suggested_action' => 'Verify DB_HOST, DB_USERNAME, DB_PASSWORD environment settings and MySQL service availability.',
                ];
            }
            if (str_contains($message, "Table") && str_contains($message, "doesn't exist")) {
                return [
                    'layer' => 'Database Schema',
                    'suspected_component' => 'Database Migration Engine',
                    'confidence_score' => 0.90,
                    'evidence_summary' => 'Missing database table referenced by query: ' . substr($message, 0, 150),
                    'suggested_action' => 'Run `php scripts/tomvis migrate` to deploy missing migration scripts.',
                ];
            }
        }

        // 2. Check HIS Adapter / Gateway
        if (str_contains($exceptionClass, 'HISGatewayException') || str_contains($sourceComponent, 'HisGateway') || str_contains($message, 'HIS Gateway')) {
            return [
                'layer' => 'HIS Adapter',
                'suspected_component' => 'Tomvis\\HIMPro\\Infrastructure\\HIS\\MockHISAdapter',
                'confidence_score' => 0.92,
                'evidence_summary' => 'HIS Gateway protocol or connection contract violation: ' . substr($message, 0, 150),
                'suggested_action' => 'Verify HIS_MODE configuration and mock adapter contract assertions.',
            ];
        }

        // 3. Check Authentication / RBAC / Authorization
        if (str_contains($exceptionClass, 'AuthException') || str_contains($sourceComponent, 'RbacMiddleware') || str_contains($message, 'Unauthorized') || str_contains($message, 'Forbidden')) {
            return [
                'layer' => 'Authentication/RBAC',
                'suspected_component' => 'Tomvis\\HIMPro\\Core\\Middleware\\RbacMiddleware',
                'confidence_score' => 0.88,
                'evidence_summary' => 'Access control rejection or missing RBAC permission: ' . substr($message, 0, 150),
                'suggested_action' => 'Check module route permissions in RBAC middleware and active user token scope.',
            ];
        }

        // 4. Check Framework Core Routing or Middleware
        if (str_contains($sourceComponent, 'Tomvis\\HIMPro\\Core\\Routing') || str_contains($sourceComponent, 'Tomvis\\HIMPro\\Core\\Middleware')) {
            return [
                'layer' => 'Framework Core',
                'suspected_component' => $sourceComponent,
                'confidence_score' => 0.85,
                'evidence_summary' => 'Core routing or pipeline middleware execution error: ' . substr($message, 0, 150),
                'suggested_action' => 'Inspect central router dispatch logic and middleware stack pipeline in app/Core/Routing.',
            ];
        }

        // 5. Check Application Module Controller / Repository
        if (str_contains($sourceComponent, 'Tomvis\\HIMPro\\Modules')) {
            return [
                'layer' => 'Module Controller',
                'suspected_component' => $sourceComponent,
                'confidence_score' => 0.80,
                'evidence_summary' => 'Module domain controller execution exception: ' . substr($message, 0, 150),
                'suggested_action' => 'Inspect module action handler in ' . $sourceComponent,
            ];
        }

        // 6. Insufficient Evidence -> Unknown Cause
        return [
            'layer' => 'Unknown Cause',
            'suspected_component' => $sourceComponent ?: 'System Core',
            'confidence_score' => 0.40,
            'evidence_summary' => 'Inconclusive stack trace evidence. Error signature requires manual triage: ' . substr($message, 0, 150),
            'suggested_action' => 'Create investigation task for engineering triage.',
        ];
    }
}
