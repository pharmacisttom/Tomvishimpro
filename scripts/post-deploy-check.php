<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Tomvis\HIMPro\Core\Config\DotEnv;
use Tomvis\HIMPro\Core\Config\Config;
use Tomvis\HIMPro\Core\Database\DatabaseManager;

DotEnv::load(__DIR__ . '/../.env');
Config::loadFromDir(__DIR__ . '/../config');

echo "[+] Running Post-Deployment Health Check...\n";

try {
    $db = new DatabaseManager();
    $pdo = $db->connection();

    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $usersCount = $stmt->fetchColumn();

    echo " -> System Database Functional. Active Users Count: {$usersCount} (PASSED)\n";
    echo "[POST-DEPLOY CHECK PASSED] Application is healthy and ready for traffic.\n";
} catch (Exception $e) {
    echo "[FAIL] Post-Deployment Check Failed: " . $e->getMessage() . "\n";
    exit(1);
}
