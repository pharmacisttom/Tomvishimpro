<?php

namespace Tomvis\HIMPro\Core\Services;

use Tomvis\HIMPro\Core\Database\DatabaseManager;
use PDO;
use Exception;

class FrameworkIssueTrackerService
{
    protected ?DatabaseManager $dbManager;

    public static array $validStatuses = [
        'Detected', 'Triaged', 'Assigned', 'Investigating', 'Fix Proposed',
        'Testing', 'Ready for Review', 'Approved', 'Released', 'Monitoring', 'Closed'
    ];

    public function __construct(?DatabaseManager $dbManager = null)
    {
        $this->dbManager = $dbManager;
    }

    public function updateStatus(int $groupId, string $newStatus, ?string $notes = null): bool
    {
        if (!in_array($newStatus, self::$validStatuses, true)) {
            throw new Exception("Invalid issue status transition: {$newStatus}");
        }

        $pdo = $this->dbManager ? $this->dbManager->connection() : (new DatabaseManager())->connection();
        $stmt = $pdo->prepare("UPDATE error_groups SET status = ? WHERE id = ?");
        return $stmt->execute([$newStatus, $groupId]);
    }

    public function linkFixProposal(int $groupId, string $title, string $description, string $diffPatch, string $unitTestCode): int
    {
        $pdo = $this->dbManager ? $this->dbManager->connection() : (new DatabaseManager())->connection();

        $stmt = $pdo->prepare("
            INSERT INTO framework_fix_proposals (group_id, title, description, diff_patch, unit_test_code, status)
            VALUES (?, ?, ?, ?, ?, 'Proposed')
        ");
        $stmt->execute([$groupId, $title, $description, $diffPatch, $unitTestCode]);

        $proposalId = (int)$pdo->lastInsertId();
        $this->updateStatus($groupId, 'Fix Proposed');

        return $proposalId;
    }
}
