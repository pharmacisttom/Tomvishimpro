<?php

namespace Tomvis\HIMPro\Core\Database;

use Tomvis\HIMPro\Core\Config\Config;
use PDO;
use Exception;
use PDOException;

class ConnectionFactory
{
    public static function create(string $connectionName, array $config): PDO
    {
        EnvironmentGuard::validateDatabaseConnection($connectionName, $config);

        $driver = $config['driver'] ?? 'mysql';
        $host = $config['host'] ?? '192.168.111.240';
        $port = (int)($config['port'] ?? 3306);
        $dbname = $config['database'] ?? 'tomvishimpro_dev';
        $user = $config['username'] ?? 'root';
        $pass = $config['password'] ?? '';
        $charset = $config['charset'] ?? 'utf8mb4';
        $options = $config['options'] ?? [];

        $dsn = sprintf('%s:host=%s;port=%d;dbname=%s;charset=%s', $driver, $host, $port, $dbname, $charset);

        $maxRetries = 1;
        $attempt = 0;
        $lastException = null;

        while ($attempt <= $maxRetries) {
            try {
                return new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                $lastException = $e;
                $attempt++;
            }
        }

        // If central database 192.168.111.240 is unreachable/access-denied during local dev/testing, fallback to local MySQL or SQLite in-memory
        $appEnv = Config::get('app.env', 'local');
        if (in_array($appEnv, ['local', 'testing'], true)) {
            if ($host !== '127.0.0.1') {
                try {
                    // Connect to local MySQL server
                    $localPdo = new PDO("mysql:host=127.0.0.1;port=3306;charset={$charset}", $user, $pass, $options);
                    $localPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
                    $localPdo->exec("USE `{$dbname}`;");
                    return $localPdo;
                } catch (PDOException $fallbackErr) {
                    // Try next fallback below
                }
            }

            try {
                $sqlitePdo = new PDO("sqlite::memory:", null, null, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
                return $sqlitePdo;
            } catch (PDOException $sqliteErr) {
                // Continue to throw primary exception
            }
        }

        throw new Exception("Database Connection Failure [{$connectionName} @ {$host}:{$port}]: " . $lastException->getMessage(), (int)$lastException->getCode(), $lastException);
    }
}
