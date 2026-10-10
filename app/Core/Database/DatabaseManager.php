<?php

namespace Tomvis\HIMPro\Core\Database;

use Tomvis\HIMPro\Core\Config\Config;
use PDO;
use Exception;

class DatabaseManager
{
    protected array $connections = [];

    public function connection(?string $name = null): PDO
    {
        $name = $name ?: Config::get('database.default', 'mysql');

        if (isset($this->connections[$name])) {
            return $this->connections[$name];
        }

        $config = Config::get("database.connections.{$name}");
        if (!$config) {
            throw new Exception("Database connection config [{$name}] not found.");
        }

        $pdo = ConnectionFactory::create($name, $config);

        $this->connections[$name] = $pdo;
        return $pdo;
    }

    public function table(string $table, ?string $connection = null): QueryBuilder
    {
        return new QueryBuilder($this->connection($connection), $table);
    }
}
