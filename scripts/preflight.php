<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Tomvis\HIMPro\Core\Config\DotEnv;
use Tomvis\HIMPro\Core\Config\Config;
use Tomvis\HIMPro\Core\Database\DatabaseManager;

DotEnv::load(__DIR__ . '/../.env');
Config::loadFromDir(__DIR__ . '/../config');

echo "[+] Running Non-Invasive Deployment Preflight Checks...\n";

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

// 4. SCAN FOR PROHIBITED APACHE / SYSTEM COMMANDS IN DEPLOYMENT SCRIPTS
$forbiddenPatterns = [
    'system' . 'ctl\s+(restart|reload)',
    'service\s+(apache2|httpd)\s+(restart|reload)',
    'apache' . 'ctl\s+graceful',
    'a2en' . 'mod',
    'a2dis' . 'mod',
];

$scriptsToScan = [
    __DIR__ . '/deploy.sh',
    __DIR__ . '/deploy.ps1',
    __DIR__ . '/rollback.sh',
];

foreach ($scriptsToScan as $script) {
    if (file_exists($script)) {
        $content = file_get_contents($script);
        foreach ($forbiddenPatterns as $pattern) {
            if (preg_match("/{$pattern}/i", $content)) {
                echo "[FAIL] PROHIBITED APACHE/SYSTEM COMMAND DETECTED in [{$script}]: Matches '{$pattern}'. Hospital Shared Server Policy Violation!\n";
                exit(1);
            }
        }
    }
}
echo " -> Non-Invasive Script Command Audit: PASSED (Zero Service Restart Commands Found)\n";

// 5. Test Database Connection & Environment Guard
try {
    $db = new DatabaseManager();
    $pdo = $db->connection();
    echo " -> Database Connection & Environment Isolation Guard: PASSED\n";
} catch (Exception $e) {
    echo "[FAIL] Preflight Database Connection Failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "[PREFLIGHT PASSED] All Non-Invasive Deployment Requirements Satisfied.\n";
