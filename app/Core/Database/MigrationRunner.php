<?php

namespace Tomvis\HIMPro\Core\Database;

use PDO;
use Exception;

class MigrationRunner
{
    protected PDO $pdo;
    protected string $migrationsPath;

    public function __construct(PDO $pdo, string $migrationsPath)
    {
        $this->pdo = $pdo;
        $this->migrationsPath = $migrationsPath;
        $this->ensureMigrationTable();
    }

    protected function ensureMigrationTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $this->pdo->exec($sql);
    }

    public function getExecuted(): array
    {
        $stmt = $this->pdo->query("SELECT migration FROM migrations");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getNextBatch(): int
    {
        $stmt = $this->pdo->query("SELECT MAX(batch) as max_batch FROM migrations");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ((int)($row['max_batch'] ?? 0)) + 1;
    }

    public function run(): array
    {
        $executed = $this->getExecuted();
        $files = glob($this->migrationsPath . '/*.php');
        sort($files);

        $batch = $this->getNextBatch();
        $ran = [];

        foreach ($files as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            if (in_array($name, $executed)) {
                continue;
            }

            $migration = require $file;
            if (is_object($migration) && method_exists($migration, 'up')) {
                $migration->up($this->pdo);
            } elseif (is_string($migration)) {
                $this->pdo->exec($migration);
            }

            $stmt = $this->pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
            $stmt->execute([$name, $batch]);

            $ran[] = $name;
        }

        return $ran;
    }
}
