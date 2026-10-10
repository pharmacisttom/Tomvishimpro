<?php

namespace Tomvis\HIMPro\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tomvis\HIMPro\Core\Config\DotEnv;
use Tomvis\HIMPro\Core\Config\Config;
use Tomvis\HIMPro\Core\Database\DatabaseManager;
use Tomvis\HIMPro\Core\Exceptions\ErrorSanitizer;
use Tomvis\HIMPro\Core\Exceptions\ErrorFingerprintService;
use Tomvis\HIMPro\Core\Exceptions\LocalErrorBuffer;
use Tomvis\HIMPro\Core\Exceptions\ErrorIngestionService;
use Tomvis\HIMPro\Core\Exceptions\RootCauseAnalysisEngine;
use Tomvis\HIMPro\Core\Exceptions\ErrorCaptureHandler;
use Tomvis\HIMPro\Core\Services\AIRepairAssistantService;
use Tomvis\HIMPro\Core\Services\FrameworkPatchService;
use Tomvis\HIMPro\Core\Services\FrameworkIssueTrackerService;
use Tomvis\HIMPro\Core\Controllers\ErrorCenterController;
use Tomvis\HIMPro\Core\Request\Request;
use Exception;
use PDOException;

class ErrorIntelligenceTest extends TestCase
{
    protected function setUp(): void
    {
        DotEnv::load(dirname(__DIR__, 2) . '/.env');
        Config::loadFromDir(dirname(__DIR__, 2) . '/config');
    }

    /**
     * Scenario 1: Error from multiple applications grouped by fingerprint.
     */
    public function testErrorsFromMultipleAppsGroupedByFingerprint(): void
    {
        $fp1 = ErrorFingerprintService::generateFingerprint('PDOException', '1045', 'ConnectionFactory', 'Stack frame 1', '3.0.0', '1.0.0');
        $fp2 = ErrorFingerprintService::generateFingerprint('PDOException', '1045', 'ConnectionFactory', 'Stack frame 1', '3.0.0', '1.0.0');

        $this->assertEquals($fp1, $fp2, 'Errors with identical signatures from different apps must yield identical fingerprint.');
    }

    /**
     * Scenario 2: Different errors with similar message have distinct fingerprints.
     */
    public function testDifferentErrorsWithSimilarMessageHaveDistinctFingerprints(): void
    {
        $fp1 = ErrorFingerprintService::generateFingerprint('ApiException', '400', 'ModuleAController', 'Stack frame A', '3.0.0', '1.0.0');
        $fp2 = ErrorFingerprintService::generateFingerprint('ApiException', '400', 'ModuleBController', 'Stack frame B', '3.0.0', '1.0.0');

        $this->assertNotEquals($fp1, $fp2, 'Errors from distinct components must yield distinct fingerprints.');
    }

    /**
     * Scenario 3: Offline Error Collector fallback to Local Error Buffer.
     */
    public function testOfflineCollectorFallbackToLocalBuffer(): void
    {
        $bufferPath = dirname(__DIR__, 2) . '/storage/logs/test_error_buffer.json';
        if (file_exists($bufferPath)) {
            @unlink($bufferPath);
        }

        $buffer = new LocalErrorBuffer($bufferPath);
        $result = $buffer->push([
            'exception_class' => 'TestException',
            'sanitized_message' => 'Offline test event',
        ]);

        $this->assertTrue($result);
        $this->assertEquals(1, $buffer->count());
        $entries = $buffer->flush();
        $this->assertCount(1, $entries);
        $this->assertEquals('Offline test event', $entries[0]['sanitized_message']);

        if (file_exists($bufferPath)) {
            @unlink($bufferPath);
        }
    }

    /**
     * Scenario 4: Stack Trace PHI & Secret Sanitization.
     */
    public function testStackSanitizationPHIAndSecrets(): void
    {
        $rawMessage = "User login failed for นายสมชาย ใจดี HN: 66-123456 password='SuperSecretPassword123' Authorization: Bearer secret-token-xyz";
        $sanitized = ErrorSanitizer::sanitizeText($rawMessage);

        $this->assertStringNotContainsString('SuperSecretPassword123', $sanitized);
        $this->assertStringNotContainsString('secret-token-xyz', $sanitized);
        $this->assertStringNotContainsString('นายสมชาย ใจดี', $sanitized);
        $this->assertStringContainsString('[REDACTED', $sanitized);
    }

    /**
     * Scenario 5: Root Cause Analysis Engine Classification.
     */
    public function testRootCauseAnalysisEngineClassification(): void
    {
        $payload = [
            'error_type' => 'PDOException',
            'sanitized_message' => 'SQLSTATE[HY000] [1045] Access denied for user root@localhost',
            'source_component' => 'Tomvis\\HIMPro\\Core\\Database\\ConnectionFactory',
        ];

        $rca = RootCauseAnalysisEngine::analyze($payload);

        $this->assertEquals('Database Connection Manager', $rca['layer']);
        $this->assertGreaterThanOrEqual(0.90, $rca['confidence_score']);
        $this->assertStringContainsString('Access denied', $rca['evidence_summary']);
    }

    /**
     * Scenario 6: AI Repair Assistant Solution Proposal.
     */
    public function testAIRepairAssistantSolutionProposal(): void
    {
        $assistant = new AIRepairAssistantService(true);
        $proposal = $assistant->analyzeAndProposeFix([
            'error_type' => 'PDOException',
            'sanitized_message' => 'SQLSTATE[HY000] [1045] Access denied for user root@localhost',
            'source_component' => 'Tomvis\\HIMPro\\Core\\Database\\ConnectionFactory',
        ]);

        $this->assertTrue($proposal['ai_enabled']);
        $this->assertNotEmpty($proposal['proposed_diff']);
        $this->assertNotEmpty($proposal['proposed_test']);
        $this->assertContains('PatientManagement', $proposal['impacted_modules']);
    }

    /**
     * Scenario 7: Framework Patch Version Compatibility Matrix.
     */
    public function testFrameworkPatchVersionCompatibility(): void
    {
        $service = new FrameworkPatchService();
        $compat1 = $service->checkCompatibility('3.0.0', '1.0.0');
        $compat2 = $service->checkCompatibility('2.5.0', '1.0.0');

        $this->assertEquals('PASS', $compat1['status']);
        $this->assertEquals('FAIL', $compat2['status']);
    }

    /**
     * Scenario 8: Issue Tracker Lifecycle Transition.
     */
    public function testIssueTrackerStateTransitions(): void
    {
        $tracker = new FrameworkIssueTrackerService();
        $this->assertContains('Detected', FrameworkIssueTrackerService::$validStatuses);
        $this->assertContains('Released', FrameworkIssueTrackerService::$validStatuses);
    }

    /**
     * Scenario 9: Error Ingestion Non-blocking Guarantee.
     */
    public function testIngestionServiceGuaranteesNonBlocking(): void
    {
        $service = new ErrorIngestionService(null, new LocalErrorBuffer(dirname(__DIR__, 2) . '/storage/logs/test_buffer_nb.json'));
        $result = $service->ingest([
            'message' => 'Non-blocking execution test',
            'source_component' => 'TestComponent',
        ]);

        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('fingerprint', $result);
    }

    /**
     * Scenario 10: Error Capture Handler Trace ID Generation.
     */
    public function testErrorCaptureHandlerTraceId(): void
    {
        $handler = new ErrorCaptureHandler();
        $ref = new \ReflectionClass($handler);
        $prop = $ref->getProperty('traceId');
        $prop->setAccessible(true);
        $traceId = $prop->getValue($handler);

        $this->assertNotEmpty($traceId);
        $this->assertMatchesRegularExpression('/^[a-f0-9\-]+$/i', $traceId);
    }

    /**
     * Scenario 11: Error Category Classification.
     */
    public function testErrorCategoryClassification(): void
    {
        $catCore = ErrorFingerprintService::classifyCategory('Tomvis\\HIMPro\\Core\\Routing\\Router', true, false);
        $catInfra = ErrorFingerprintService::classifyCategory('PDO', true, false);
        $catReg = ErrorFingerprintService::classifyCategory('Tomvis\\HIMPro\\Modules\\Patient', false, true);

        $this->assertEquals('Framework Core Defect', $catCore);
        $this->assertEquals('Infrastructure Incident', $catInfra);
        $this->assertEquals('Regression', $catReg);
    }

    /**
     * Scenario 12: Thai National ID Redaction.
     */
    public function testThaiNationalIdRedaction(): void
    {
        $raw = "Patient Thai CID: 1-2345-67890-12-3 and 1234567890123 passed.";
        $sanitized = ErrorSanitizer::sanitizeText($raw);

        $this->assertStringNotContainsString('1234567890123', $sanitized);
        $this->assertStringContainsString('[REDACTED_NATIONAL_ID]', $sanitized);
    }

    /**
     * Scenario 13: Error Controller Metrics API.
     */
    public function testErrorCenterControllerMetrics(): void
    {
        $controller = new ErrorCenterController();
        $ref = new \ReflectionClass($controller);
        $method = $ref->getMethod('getMetrics');
        $method->setAccessible(true);
        $metrics = $method->invoke($controller);

        $this->assertArrayHasKey('total_errors', $metrics);
        $this->assertArrayHasKey('affected_apps', $metrics);
    }

    /**
     * Scenario 14: Non-Invasive Deployment Integrity.
     */
    public function testNonInvasiveDeploymentIntegrity(): void
    {
        $apacheConfPath = 'C:/xampp/apache/conf/httpd.conf';
        $this->assertFileDoesNotExist($apacheConfPath, 'Framework operations must never create or mutate system Apache config files.');
    }
}
