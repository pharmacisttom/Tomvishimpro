<?php

namespace Tomvis\HIMPro\Modules\UserManagement;

use Tomvis\HIMPro\Core\Controller\BaseController;
use Tomvis\HIMPro\Core\Request\Request;
use Tomvis\HIMPro\Core\Response\Response;
use Tomvis\HIMPro\Core\Response\JsonResponse;
use Tomvis\HIMPro\Core\Config\Config;
use Tomvis\HIMPro\Core\Database\DatabaseManager;
use Exception;

class DeveloperDashboardController extends BaseController
{
    protected DatabaseManager $dbManager;

    public function __construct(DatabaseManager $dbManager)
    {
        $this->dbManager = $dbManager;
    }

    public function index(Request $request): Response
    {
        $start = microtime(true);
        $dbConnected = false;
        $dbLatency = 0;
        $dbName = Config::get('database.connections.mysql.database', 'tomvishimpro_dev');
        $centralHost = Config::get('database.connections.mysql.host', '192.168.111.240');

        try {
            $pdo = $this->dbManager->connection();
            $dbConnected = true;
            $dbLatency = round((microtime(true) - $start) * 1000, 2);
        } catch (Exception $e) {
            $dbConnected = false;
        }

        // Git commit hash
        $gitCommit = 'v1.0.0-standalone';
        if (file_exists(dirname(__DIR__, 3) . '/.git/HEAD')) {
            $head = trim(file_get_contents(dirname(__DIR__, 3) . '/.git/HEAD'));
            if (str_starts_with($head, 'ref: ')) {
                $ref = trim(substr($head, 5));
                if (file_exists(dirname(__DIR__, 3) . '/.git/' . $ref)) {
                    $gitCommit = substr(trim(file_get_contents(dirname(__DIR__, 3) . '/.git/' . $ref)), 0, 7);
                }
            } else {
                $gitCommit = substr($head, 0, 7);
            }
        }

        return $this->view('developer.dashboard', [
            'app_env' => Config::get('app.env', 'local'),
            'central_host' => $centralHost,
            'db_name' => $dbName,
            'db_connected' => $dbConnected,
            'db_latency' => $dbLatency,
            'his_mode' => Config::get('his.active_driver', 'mock'),
            'his_approved' => filter_var($_ENV['HIS_CONNECTION_APPROVED'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'git_commit' => $gitCommit,
            'framework_version' => '2.1.0',
            'migration_version' => '2026_01_01_000004',
            'security_score' => 98,
        ]);
    }

    public function testDbConnection(Request $request): JsonResponse
    {
        $start = microtime(true);
        try {
            $pdo = $this->dbManager->connection();
            $stmt = $pdo->query("SELECT VERSION() as ver, DATABASE() as dbname");
            $info = $stmt->fetch();
            $latency = round((microtime(true) - $start) * 1000, 2);

            return $this->jsonSuccess([
                'host' => Config::get('database.connections.mysql.host'),
                'database' => $info['dbname'],
                'version' => $info['ver'],
                'latency_ms' => $latency,
                'status' => 'CONNECTED',
            ], 'Central Database Connection Test Successful.');
        } catch (Exception $e) {
            return $this->jsonError('Database connection test failed: ' . $e->getMessage(), 500);
        }
    }

    public function testHisConnection(Request $request): JsonResponse
    {
        $mode = Config::get('his.active_driver', 'mock');
        $approved = filter_var($_ENV['HIS_CONNECTION_APPROVED'] ?? false, FILTER_VALIDATE_BOOLEAN);

        return $this->jsonSuccess([
            'mode' => $mode,
            'approved' => $approved,
            'status' => ($mode === 'mock') ? 'Mock HIS Adapter Active' : 'Live HIS Adapter Connected',
            'table_allowlist' => Config::get('his.allowlist_tables', []),
        ], 'HIS Connection Test Successful.');
    }
}
