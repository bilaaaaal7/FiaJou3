<?php
/**
 * Modèle Commande
 * Accès aux tables `orders` et `order_items`.
 */

require_once __DIR__ . '/Database.php';

class CommandeModele
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Liste complète des commandes (vue admin), avec infos client.
     */
    public function getToutesAvecClient(): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT orders.id, orders.date_commande, orders.date_livraison, orders.heure_livraison,
                    orders.total, orders.statut, orders.commentaire,
                    users.email, profiles.prenom, profiles.nom
             FROM orders
             INNER JOIN users ON orders.user_id = users.id
             INNER JOIN profiles ON users.id = profiles.user_id
             ORDER BY orders.id DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Commandes d'un statut donné (utilisé par cuisinier/livreur).
     */
    public function getParStatut(string $statut): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT orders.id, orders.date_commande, orders.date_livraison, orders.heure_livraison,
                    orders.total, orders.statut, orders.commentaire,
                    profiles.prenom, profiles.nom
             FROM orders
             INNER JOIN users ON orders.user_id = users.id
             INNER JOIN profiles ON users.id = profiles.user_id
             WHERE orders.statut = ?
             ORDER BY orders.date_livraison, orders.heure_livraison"
        );
        $stmt->execute([$statut]);
        return $stmt->fetchAll();
    }

    /**
     * Historique des commandes d'un client (mes_commandes).
     */
    public function getParUtilisateur(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getParId(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function mettreAJourStatut(int $id, string $statut): void
    {
        $stmt = $this->pdo->prepare("UPDATE orders SET statut = ? WHERE id = ?");
        $stmt->execute([$statut, $id]);
    }

    /**
     * Crée une commande + ses lignes (order_items) à partir du panier en session.
     * $panier est un tableau [plat_id => quantite].
     * Retourne l'id de la commande créée.
     */
    public function creerDepuisPanier(int $userId, int $zoneId, string $dateLivraison, string $heureLivraison, string $commentaire, array $panier): int
    {
        $platModele = new PlatModele();
        $total = 0;
        $prixParPlat = [];

        foreach ($panier as $platId => $quantite) {
            $plat = $platModele->getParId((int) $platId);
            if ($plat) {
                $prixParPlat[$platId] = $plat['prix'];
                $total += $plat['prix'] * $quantite;
            }
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO orders (user_id, zone_id, date_commande, date_livraison, heure_livraison, total, statut, commentaire)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $userId,
            $zoneId,
            date('Y-m-d'),
            $dateLivraison,
            $heureLivraison,
            $total,
            'En attente',
            $commentaire,
        ]);

        $orderId = (int) $this->pdo->lastInsertId();

        $stmtItem = $this->pdo->prepare(
            "INSERT INTO order_items (order_id, product_id, quantite, prix) VALUES (?, ?, ?, ?)"
        );
        foreach ($panier as $platId => $quantite) {
            if (isset($prixParPlat[$platId])) {
                $stmtItem->execute([$orderId, $platId, $quantite, $prixParPlat[$platId]]);
            }
        }

        return $orderId;
    }

    public function compter(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    }
}
