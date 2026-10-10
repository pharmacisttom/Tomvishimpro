<?php

namespace Tomvis\HIMPro\Core\Services;

use Tomvis\HIMPro\Core\Database\DatabaseManager;
use PDO;
use Exception;

class FrameworkPatchService
{
    protected ?DatabaseManager $dbManager;

    public function __construct(?DatabaseManager $dbManager = null)
    {
        $this->dbManager = $dbManager;
    }

    public function createPatchRelease(string $patchVersion, string $releaseNotes): int
    {
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare("
            INSERT INTO framework_patch_releases (patch_version, release_notes, status, created_at)
            VALUES (?, ?, 'Draft', NOW())
        ");
        $stmt->execute([$patchVersion, $releaseNotes]);
        return (int)$pdo->lastInsertId();
    }

    public function approveRelease(int $releaseId): bool
    {
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare("
            UPDATE framework_patch_releases
            SET status = 'Approved'
            WHERE id = ?
        ");
        return $stmt->execute([$releaseId]);
    }

    public function releasePatch(int $releaseId): bool
    {
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare("
            UPDATE framework_patch_releases
            SET status = 'Released', released_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$releaseId]);
    }

    public function checkCompatibility(string $targetFrameworkVersion, string $moduleVersion): array
    {
        $isCompatible = version_compare($targetFrameworkVersion, '3.0.0', '>=');
        return [
            'compatible' => $isCompatible,
            'target_framework_version' => $targetFrameworkVersion,
            'module_version' => $moduleVersion,
            'status' => $isCompatible ? 'PASS' : 'FAIL',
            'notes' => $isCompatible ? 'Version matrix compatible.' : 'Module requires upgrade to TomvisHIMPro Core 3.0+.',
        ];
    }

    protected function getPdo(): PDO
    {
        if ($this->dbManager) {
            return $this->dbManager->connection();
        }
        return (new DatabaseManager())->connection();
    }
}
