<?php
/**
 * Modèle Notification
 * Accès à la table `notifications`.
 */

require_once __DIR__ . '/Database.php';

class NotificationModele
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function creer(int $userId, string $titre, string $message): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO notifications (user_id, titre, message, est_lu, date_notification)
             VALUES (?, ?, ?, 0, NOW())"
        );
        $stmt->execute([$userId, $titre, $message]);
    }

    public function getParUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY date_notification DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function compterNonLues(int $userId): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM notifications WHERE user_id = ? AND est_lu = 0"
        );
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function marquerLu(int $id, int $userId): void
    {
        $stmt = $this->pdo->prepare("UPDATE notifications SET est_lu = 1 WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
    }

    public function marquerToutLu(int $userId): void
    {
        $stmt = $this->pdo->prepare("UPDATE notifications SET est_lu = 1 WHERE user_id = ?");
        $stmt->execute([$userId]);
    }
}
