<?php

namespace Tomvis\HIMPro\Infrastructure\HIMPro;

use Tomvis\HIMPro\Core\Config\Config;
use Tomvis\HIMPro\Core\Database\DatabaseManager;
use PDO;
use Exception;

class HISConnectionManager
{
    protected DatabaseManager $dbManager;

    public function __construct(DatabaseManager $dbManager)
    {
        $this->dbManager = $dbManager;
    }

    public function getReadOnlyConnection(): PDO
    {
        $driver = Config::get('his.active_driver', 'mock');
        $approved = filter_var($_ENV['HIS_CONNECTION_APPROVED'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($driver === 'mock' || !$approved) {
            // Return app database PDO or mock PDO
            return $this->dbManager->connection('app');
        }

        return $this->dbManager->connection('his_readonly');
    }

    public function isApproved(): bool
    {
        return filter_var($_ENV['HIS_CONNECTION_APPROVED'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    public function getMode(): string
    {
        return Config::get('his.active_driver', 'mock');
    }
}
