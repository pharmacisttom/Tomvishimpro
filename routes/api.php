<?php

use Tomvis\HIMPro\Core\Routing\Router;
use Tomvis\HIMPro\Core\Request\Request;
use Tomvis\HIMPro\Core\Response\JsonResponse;
use Tomvis\HIMPro\Modules\Patient\PatientController;
use Tomvis\HIMPro\Modules\Sandbox\SandboxController;
use Tomvis\HIMPro\Modules\DrugAllergy\AllergyController;
use Tomvis\HIMPro\Modules\Medication\MedRecController;
use Tomvis\HIMPro\Modules\SecurityLab\SecurityLabController;
use Tomvis\HIMPro\Modules\Reporting\DashboardController;
use Tomvis\HIMPro\Modules\UserManagement\DeveloperDashboardController;
use Tomvis\HIMPro\Modules\UserManagement\DeveloperStudioController;
use Tomvis\HIMPro\Modules\PharmacyQueue\PharmacyQueueController;

/** @var Router $router */

$router->group(['prefix' => '/api/v1'], function (Router $r) {
    // Health check
    $r->get('/health', function (Request $req) {
        return JsonResponse::success([
            'framework' => 'TomvisHIMPro RAD Platform',
            'version' => '3.0.0',
            'status' => 'healthy',
            'timestamp' => date('Y-m-d H:i:s'),
        ], 'System operational.');
    });

    // Patient APIs
    $r->get('/patients', [PatientController::class, 'searchApi']);
    $r->get('/patients/{hn}', [PatientController::class, 'showApi']);

    // Reference Module: PDH Pharmacy Queue APIs
    $r->get('/pharmacy-queue/list', [PharmacyQueueController::class, 'listApi']);
    $r->post('/pharmacy-queue/generate', [PharmacyQueueController::class, 'generateQueue']);
    $r->post('/pharmacy-queue/call', [PharmacyQueueController::class, 'callQueue']);

    // Developer Studio 3.0 APIs & Benchmark
    $r->get('/developer/benchmark', [DeveloperStudioController::class, 'runBenchmark']);
    $r->post('/developer/generate-wizard', [DeveloperStudioController::class, 'generateModuleWizard']);

    // Sandbox & Scenarios
    $r->get('/sandbox/scenarios', function (Request $req) {
        return JsonResponse::success([
            ['code' => 'no_allergy', 'name' => 'Normal Patient'],
            ['code' => 'penicillin_allergy', 'name' => 'Penicillin Allergy'],
            ['code' => 'adr_history', 'name' => 'ADR History'],
            ['code' => 'warfarin_therapy', 'name' => 'Warfarin Therapy'],
            ['code' => 'ckd_stage4', 'name' => 'CKD Stage 4'],
        ], 'Scenarios list retrieved.');
    });
    $r->post('/sandbox/generate', [SandboxController::class, 'generate']);

    // Security & Scanning
    $r->get('/security/scans', [SecurityLabController::class, 'index']);
    $r->post('/security/scan', [SecurityLabController::class, 'triggerScan']);
    $r->get('/security/zap-config', [SecurityLabController::class, 'getZapConfig']);

    // Naranjo & MedRec APIs
    $r->post('/allergy/naranjo', [AllergyController::class, 'calculateNaranjo']);
    $r->get('/medrec/bpmh/{hn}', [MedRecController::class, 'bpmh']);
    $r->post('/medrec/reconcile', [MedRecController::class, 'reconcile']);

    // Dashboard Metrics
    $r->get('/dashboard/metrics', [DashboardController::class, 'metricsApi']);

    // Developer Infrastructure APIs
    $r->get('/developer/test-db', [DeveloperDashboardController::class, 'testDbConnection']);
    $r->get('/developer/test-his', [DeveloperDashboardController::class, 'testHisConnection']);
});
