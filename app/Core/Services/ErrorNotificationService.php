<?php

namespace Tomvis\HIMPro\Core\Services;

use Tomvis\HIMPro\Core\Exceptions\ErrorSanitizer;
use PDO;

class ErrorNotificationService
{
    protected ?PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    public function notifyNewIncident(int $groupId, string $title, string $message, string $severity): bool
    {
        $sanitizedTitle = ErrorSanitizer::sanitizeText($title);
        $sanitizedMessage = ErrorSanitizer::sanitizeText($message);

        // Throttle check: ensure no notification for same group within last 10 minutes
        if ($this->pdo) {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM error_notifications
                WHERE group_id = ? AND created_at > (NOW() - INTERVAL 10 MINUTE)
            ");
            $stmt->execute([$groupId]);
            if ((int)$stmt->fetchColumn() > 0) {
                return false; // Throttled
            }

            $insertStmt = $this->pdo->prepare("
                INSERT INTO error_notifications (group_id, title, message, is_read)
                VALUES (?, ?, ?, 0)
            ");
            $insertStmt->execute([
                $groupId,
                "[$severity] $sanitizedTitle",
                $sanitizedMessage
            ]);
        }

        // Webhook / Gateway Dispatch simulation
        $this->dispatchExternalGateways("[$severity] $sanitizedTitle", $sanitizedMessage);

        return true;
    }

    protected function dispatchExternalGateways(string $title, string $message): void
    {
        // Safe external gateway stub (LINE / Telegram / Email) with zero PHI
        // Ensures no exceptions crash the calling process
    }
}
