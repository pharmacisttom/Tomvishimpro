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

        $maxRetries = 2;
        $attempt = 0;
        $lastException = null;

        while ($attempt <= $maxRetries) {
            try {
                return new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                $lastException = $e;
                $attempt++;
                if ($attempt <= $maxRetries) {
                    usleep(100000); // 100ms delay before retry
                }
            }
        }

        // If central database 192.168.111.240 is unreachable during local development offline, fallback to local MySQL if in local env
        $appEnv = Config::get('app.env', 'local');
        if ($appEnv === 'local' && $host !== '127.0.0.1') {
            try {
                $fallbackDsn = sprintf('%s:host=127.0.0.1;port=3306;dbname=%s;charset=%s', $driver, $dbname, $charset);
                return new PDO($fallbackDsn, 'root', '', $options);
            } catch (PDOException $fallbackErr) {
                // If local fallback also fails, throw primary exception
            }
        }

        throw new Exception("Database Connection Failure [{$connectionName} @ {$host}:{$port}]: " . $lastException->getMessage(), (int)$lastException->getCode(), $lastException);
    }
}
