<?php

namespace Tomvis\HIMPro\Core\Database;

use Tomvis\HIMPro\Core\Config\Config;
use Exception;

class EnvironmentGuard
{
    public static function validateDatabaseConnection(string $connectionName, array $config): void
    {
        $appEnv = Config::get('app.env', 'local');
        $dbName = $config['database'] ?? '';
        $hisMode = Config::get('his.active_driver', 'mock');
        $hisApproved = filter_var($_ENV['HIS_CONNECTION_APPROVED'] ?? false, FILTER_VALIDATE_BOOLEAN);

        // 1. Local env must NOT touch Production DB 'tomvishimpro'
        if ($appEnv === 'local' && $dbName === 'tomvishimpro') {
            throw new Exception("CRITICAL ENVIRONMENT VIOLATION: Local environment ('{$appEnv}') is FORBIDDEN from connecting directly to Production Database ['{$dbName}'].");
        }

        // 2. Production env must NOT touch Dev DB 'tomvishimpro_dev'
        if ($appEnv === 'production' && $dbName === 'tomvishimpro_dev') {
            throw new Exception("CRITICAL ENVIRONMENT VIOLATION: Production environment ('{$appEnv}') cannot use Development Database ['{$dbName}'].");
        }

        // 3. Fail closed if Production lacks secure APP_SECRET or debug is enabled
        if ($appEnv === 'production') {
            $secret = Config::get('app.secret', '');
            if (empty($secret) || str_contains($secret, 'default-secret-key') || str_contains($secret, 'CHANGE_THIS')) {
                throw new Exception("CRITICAL SECURITY VIOLATION: Production environment requires a valid secure APP_SECRET.");
            }
            if (Config::get('app.debug', false) === true) {
                throw new Exception("CRITICAL SECURITY VIOLATION: APP_DEBUG must be false in Production environment.");
            }
        }

        // 4. HIS Mode Guard
        if ($connectionName === 'his_readonly' || $connectionName === 'himpro') {
            if ($hisMode === 'mock' && $hisApproved !== true && ($config['driver'] ?? '') !== 'mock') {
                throw new Exception("CRITICAL HIS GATEWAY GUARD: HIS_MODE is set to 'mock'. Live HIS production database connection is NOT approved.");
            }
        }
    }
}
