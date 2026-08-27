<?php
/**
 * Modèle Abonnement
 * Accès à la table `subscriptions`.
 */

require_once __DIR__ . '/Database.php';

class AbonnementModele
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Crée un abonnement mensuel pour un utilisateur.
     */
    public function creer(int $userId): int
    {
        $dateDebut = date('Y-m-d');
        $dateFin = date('Y-m-d', strtotime('+1 month'));
        $prix = defined('PRIX_ABONNEMENT_MENSUEL') ? PRIX_ABONNEMENT_MENSUEL : 500.00;

        $stmt = $this->pdo->prepare(
            "INSERT INTO subscriptions (user_id, date_debut, date_fin, prix, statut, created_at)
             VALUES (?, ?, ?, ?, 'active', NOW())"
        );
        $stmt->execute([$userId, $dateDebut, $dateFin, $prix]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Récupère l'abonnement actif d'un utilisateur.
     */
    public function getActif(int $userId): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM subscriptions
             WHERE user_id = ? AND statut = 'active' AND date_fin >= CURDATE()
             ORDER BY date_fin DESC LIMIT 1"
        );
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    /**
     * Vérifie si un utilisateur a un abonnement actif.
     */
    public function estActif(int $userId): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM subscriptions
             WHERE user_id = ? AND statut = 'active' AND date_fin >= CURDATE()"
        );
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Désactive les abonnements expirés.
     */
    public function desactiverExpires(): void
    {
        $this->pdo->exec(
            "UPDATE subscriptions SET statut = 'expire'
             WHERE statut = 'active' AND date_fin < CURDATE()"
        );
    }

    /**
     * Liste des abonnements d'un utilisateur.
     */
    public function getParUtilisateur(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM subscriptions WHERE user_id = ? ORDER BY date_debut DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Annule un abonnement.
     */
    public function annuler(int $subscriptionId, int $userId): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE subscriptions SET statut = 'annule'
             WHERE id = ? AND user_id = ? AND statut = 'active'"
        );
        $stmt->execute([$subscriptionId, $userId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Nombre total d'abonnements actifs.
     */
    public function compterActifs(): int
    {
        return (int) $this->pdo->query(
            "SELECT COUNT(*) FROM subscriptions WHERE statut = 'active' AND date_fin >= CURDATE()"
        )->fetchColumn();
    }

    /**
     * Chiffre d'affaires des abonnements.
     */
    public function totalAbonnements(): float
    {
        return (float) $this->pdo->query(
            "SELECT COALESCE(SUM(prix), 0) FROM subscriptions WHERE statut IN ('active', 'expire')"
        )->fetchColumn();
    }
}
