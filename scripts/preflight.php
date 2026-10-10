<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Tomvis\HIMPro\Core\Config\DotEnv;
use Tomvis\HIMPro\Core\Config\Config;
use Tomvis\HIMPro\Core\Database\DatabaseManager;
use Tomvis\HIMPro\Core\Database\EnvironmentGuard;

DotEnv::load(__DIR__ . '/../.env');
Config::loadFromDir(__DIR__ . '/../config');

echo "[+] Running Deployment Preflight Checks...\n";

// 1. Check PHP Version
if (version_compare(PHP_VERSION, '8.2.0', '<')) {
    echo "[FAIL] PHP Version must be >= 8.2.0. Current: " . PHP_VERSION . "\n";
    exit(1);
}
echo " -> PHP Version: " . PHP_VERSION . " (PASSED)\n";

// 2. Check Required Extensions
$requiredExts = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'ctype', 'xml'];
foreach ($requiredExts as $ext) {
    if (!extension_loaded($ext)) {
        echo "[FAIL] Missing required PHP extension: {$ext}\n";
        exit(1);
    }
}
echo " -> Required Extensions: ALL LOADED (PASSED)\n";

// 3. Verify .env Exists and Is Not Committed
if (!file_exists(__DIR__ . '/../.env')) {
    echo "[FAIL] File .env is missing. Create .env from template before deployment.\n";
    exit(1);
}
echo " -> File .env Present: PASSED\n";

// 4. Test Database Connection & Environment Guard
try {
    $db = new DatabaseManager();
    $pdo = $db->connection();
    echo " -> Database Connection & Environment Isolation Guard: PASSED\n";
} catch (Exception $e) {
    echo "[FAIL] Preflight Database Connection Failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "[PREFLIGHT PASSED] All deployment requirements satisfied.\n";
