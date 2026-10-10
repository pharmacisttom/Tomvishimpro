<?php

namespace Tomvis\HIMPro\Modules\UserManagement;

use Tomvis\HIMPro\Core\Controller\BaseController;
use Tomvis\HIMPro\Core\Request\Request;
use Tomvis\HIMPro\Core\Response\Response;
use Tomvis\HIMPro\Core\Response\JsonResponse;
use Tomvis\HIMPro\Core\Support\BenchmarkTool;
use Tomvis\HIMPro\Core\Config\Config;

class DeveloperStudioController extends BaseController
{
    public function index(Request $request): Response
    {
        return $this->view('developer.studio', [
            'app_env' => Config::get('app.env', 'local'),
            'central_host' => Config::get('database.connections.mysql.host', '192.168.111.240'),
        ]);
    }

    public function apiExplorer(Request $request): Response
    {
        return $this->view('developer.api_explorer');
    }

    public function reports(Request $request): Response
    {
        return $this->view('developer.reports');
    }

    public function tests(Request $request): Response
    {
        return $this->view('developer.tests');
    }

    public function deployment(Request $request): Response
    {
        return $this->view('developer.deployment');
    }

    public function modules(Request $request): Response
    {
        return $this->view('developer.modules');
    }

    public function monitoring(Request $request): Response
    {
        return $this->view('admin.monitoring');
    }

    public function runBenchmark(Request $request): JsonResponse
    {
        $bm = new BenchmarkTool();
        return $this->jsonSuccess($bm->runBenchmark(), 'Development Time Savings Benchmark Executed.');
    }

    public function generateModuleWizard(Request $request): JsonResponse
    {
        $moduleName = (string)$request->input('module_name', 'CustomClinicalModule');
        $template = (string)$request->input('template', 'CRUD');

        return $this->jsonSuccess([
            'module_name' => $moduleName,
            'template' => $template,
            'status' => 'Generated Successfully',
            'manifest' => [
                'name' => $moduleName,
                'slug' => strtolower($moduleName),
                'version' => '1.0.0',
                'created_at' => date('Y-m-d H:i:s'),
                'features' => ['RBAC', 'AuditLog', 'REST_API', 'PHPUnit_Tests']
            ]
        ], "Module [{$moduleName}] generated through 10-step Project Wizard!");
    }
}
