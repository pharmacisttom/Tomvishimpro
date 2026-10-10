<?php

use Tomvis\HIMPro\Core\Routing\Router;
use Tomvis\HIMPro\Modules\Authentication\AuthController;
use Tomvis\HIMPro\Modules\Reporting\DashboardController;
use Tomvis\HIMPro\Modules\Patient\PatientController;
use Tomvis\HIMPro\Modules\Sandbox\SandboxController;
use Tomvis\HIMPro\Modules\DrugAllergy\AllergyController;
use Tomvis\HIMPro\Modules\Medication\MedRecController;
use Tomvis\HIMPro\Modules\SecurityLab\SecurityLabController;
use Tomvis\HIMPro\Modules\UserManagement\DeveloperDashboardController;
use Tomvis\HIMPro\Modules\UserManagement\DeveloperStudioController;
use Tomvis\HIMPro\Modules\PharmacyQueue\PharmacyQueueController;

/** @var Router $router */

$router->get('/', [DashboardController::class, 'index']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

$router->group(['middleware' => ['auth']], function (Router $r) {
    // Clinical Applications
    $r->get('/dashboard', [DashboardController::class, 'index']);
    $r->get('/patients/search', [PatientController::class, 'searchView']);
    $r->get('/allergy', [AllergyController::class, 'index']);
    $r->get('/medrec', [MedRecController::class, 'index']);

    // Reference Module: PDH Pharmacy Queue
    $r->get('/pharmacy-queue', [PharmacyQueueController::class, 'index']);
    $r->get('/pharmacy-queue/tv-display', [PharmacyQueueController::class, 'tvDisplay']);

    // Developer Studio 3.0 RAD Suite
    $r->get('/sandbox', [SandboxController::class, 'index']);
    $r->get('/security', [SecurityLabController::class, 'index']);
    $r->get('/admin/developer', [DeveloperDashboardController::class, 'index']);

    $r->get('/developer', [DeveloperStudioController::class, 'index']);
    $r->get('/developer/api-explorer', [DeveloperStudioController::class, 'apiExplorer']);
    $r->get('/developer/reports', [DeveloperStudioController::class, 'reports']);
    $r->get('/developer/tests', [DeveloperStudioController::class, 'tests']);
    $r->get('/developer/deployment', [DeveloperStudioController::class, 'deployment']);
    $r->get('/developer/modules', [DeveloperStudioController::class, 'modules']);
    $r->get('/admin/monitoring', [DeveloperStudioController::class, 'monitoring']);
});
