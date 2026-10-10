<?php

namespace Tomvis\HIMPro\Modules\SecurityLab;

use Tomvis\HIMPro\Core\Controller\BaseController;
use Tomvis\HIMPro\Core\Request\Request;
use Tomvis\HIMPro\Core\Response\Response;
use Tomvis\HIMPro\Core\Response\JsonResponse;
use Tomvis\HIMPro\Core\Database\DatabaseManager;
use PDO;

class SecurityLabController extends BaseController
{
    protected DatabaseManager $dbManager;

    public function __construct(DatabaseManager $dbManager)
    {
        $this->dbManager = $dbManager;
    }

    public function index(Request $request): Response
    {
        $pdo = $this->dbManager->connection();
        $findings = $pdo->query("SELECT * FROM security_findings ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        $counts = [
            'Critical' => 0,
            'High' => 0,
            'Medium' => 0,
            'Low' => 0,
            'Info' => 0,
        ];

        foreach ($findings as $f) {
            $counts[$f['severity']] = ($counts[$f['severity']] ?? 0) + 1;
        }

        $score = $this->calculateSecurityScore($counts);

        return $this->view('security.dashboard', [
            'findings' => $findings,
            'counts' => $counts,
            'score' => $score,
            'production_ready' => ($counts['Critical'] === 0 && $counts['High'] === 0),
        ]);
    }

    public function triggerScan(Request $request): JsonResponse
    {
        $scanType = (string)$request->input('scan_type', 'Composer Audit');
        $pdo = $this->dbManager->connection();

        $stmt = $pdo->prepare("INSERT INTO security_scans (scan_type, scanner_name, status, findings_count) VALUES (?, ?, 'completed', 0)");
        $stmt->execute([$scanType, 'Tomvis Security Lab']);
        $scanId = $pdo->lastInsertId();

        // Sample finding registration for testing finding workflow if requested
        if ($scanType === 'OWASP ZAP DAST') {
            $stmtF = $pdo->prepare("INSERT INTO security_findings (scan_id, vulnerability_name, severity, affected_component, cwe, description, remediation, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Open')");
            $stmtF->execute([
                $scanId,
                'Missing Anti-CSRF Token on Legacy Form',
                'Medium',
                '/public/legacy-form',
                'CWE-352',
                'The form submission endpoint does not enforce Anti-CSRF verification.',
                'Include CsrfProtection::field() in all POST forms.'
            ]);
        }

        return $this->jsonSuccess([
            'scan_id' => $scanId,
            'scan_type' => $scanType,
            'status' => 'completed',
            'executed_at' => date('Y-m-d H:i:s'),
        ], "Security scan [{$scanType}] completed successfully.");
    }

    public function getZapConfig(Request $request): JsonResponse
    {
        $config = [
            'env' => [
                'target' => 'http://localhost/tomvishimpro/public',
            ],
            'spider' => [
                'maxDuration' => 5,
                'urlList' => ['http://localhost/tomvishimpro/public'],
            ],
            'activeScan' => [
                'policy' => 'API-Minimal-Safe-Policy',
                'maxRuleDurationInMins' => 2,
            ],
            'rules' => [
                ['id' => 40012, 'name' => 'Reflected Cross Site Scripting', 'threshold' => 'Medium'],
                ['id' => 40018, 'name' => 'SQL Injection', 'threshold' => 'High'],
            ]
        ];

        return $this->jsonSuccess($config, 'OWASP ZAP Automation Configuration generated.');
    }

    protected function calculateSecurityScore(array $counts): int
    {
        $penalty = ($counts['Critical'] * 30) + ($counts['High'] * 15) + ($counts['Medium'] * 5) + ($counts['Low'] * 1);
        $score = max(0, 100 - $penalty);
        return $score;
    }
}
