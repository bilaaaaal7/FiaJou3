<?php
/**
 * Modèle Historique des statuts de commande
 * Accès à la table `order_status_history`.
 */

require_once __DIR__ . '/Database.php';

class HistoriqueModele
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function ajouter(int $orderId, ?string $ancienStatut, string $nouveauStatut, ?string $commentaire, ?int $userId = null): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO order_status_history (order_id, user_id, ancien_statut, nouveau_statut, commentaire, date_modification)
             VALUES (?, ?, ?, ?, ?, NOW())"
        );
        $stmt->execute([$orderId, $userId, $ancienStatut, $nouveauStatut, $commentaire]);
    }

    public function getParOrder(int $orderId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT osh.*, profiles.prenom, profiles.nom
             FROM order_status_history osh
             LEFT JOIN profiles ON osh.user_id = profiles.user_id
             WHERE osh.order_id = ?
             ORDER BY osh.date_modification DESC"
        );
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    /**
     * Journal d'activité d'un utilisateur (cuisinier/livreur) :
     * les modifications de statut qu'il a lui-même effectuées.
     */
    public function getParUser(int $userId, int $limit = 100): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT osh.id, osh.order_id, osh.ancien_statut, osh.nouveau_statut,
                    osh.commentaire, osh.date_modification,
                    orders.date_livraison, orders.heure_livraison, orders.total,
                    profiles.prenom AS client_prenom, profiles.nom AS client_nom
             FROM order_status_history osh
             INNER JOIN orders ON osh.order_id = orders.id
             INNER JOIN users ON orders.user_id = users.id
             INNER JOIN profiles ON users.id = profiles.user_id
             WHERE osh.user_id = ?
             ORDER BY osh.date_modification DESC
             LIMIT " . (int) $limit
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getDernierStatut(int $orderId): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM order_status_history
             WHERE order_id = ?
             ORDER BY date_modification DESC
             LIMIT 1"
        );
        $stmt->execute([$orderId]);
        return $stmt->fetch();
    }
}
