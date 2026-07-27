<?php
/**
 * Modèle Commande
 * Accès aux tables `orders` et `order_items`.
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/PlatModele.php';
require_once __DIR__ . '/ZoneModele.php';

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
            "SELECT orders.id, orders.date_commande, orders.created_at, orders.date_livraison, orders.heure_livraison,
                    orders.total, orders.statut, orders.commentaire, orders.priority, orders.pause,
                    orders.assigned_cook_id, orders.assigned_driver_id,
                    users.email, profiles.prenom, profiles.nom,
                    dz.nom AS zone_nom
             FROM orders
             INNER JOIN users ON orders.user_id = users.id
             INNER JOIN profiles ON users.id = profiles.user_id
             LEFT JOIN delivery_zones dz ON orders.zone_id = dz.id
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
                    orders.total, orders.statut, orders.commentaire, orders.priority, orders.pause,
                    orders.assigned_cook_id, orders.assigned_driver_id,
                    profiles.prenom, profiles.nom, users.email, profiles.telephone,
                    profiles.adresse, profiles.ville,
                    dz.nom AS zone_nom, dz.prix_livraison
             FROM orders
             INNER JOIN users ON orders.user_id = users.id
             INNER JOIN profiles ON users.id = profiles.user_id
             LEFT JOIN delivery_zones dz ON orders.zone_id = dz.id
             WHERE orders.statut = ?
             ORDER BY orders.date_livraison, orders.heure_livraison"
        );
        $stmt->execute([$statut]);
        return $stmt->fetchAll();
    }

    /**
     * Commandes assignées à un cuisinier.
     */
    public function getParCuisinier(int $cookId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT orders.id, orders.date_commande, orders.date_livraison, orders.heure_livraison,
                    orders.total, orders.statut, orders.commentaire, orders.priority,
                    profiles.prenom, profiles.nom, users.email
             FROM orders
             INNER JOIN users ON orders.user_id = users.id
             INNER JOIN profiles ON users.id = profiles.user_id
             WHERE orders.assigned_cook_id = ?
             ORDER BY orders.date_livraison, orders.heure_livraison"
        );
        $stmt->execute([$cookId]);
        return $stmt->fetchAll();
    }

    /**
     * Commandes assignées à un livreur.
     */
    public function getParLivreur(int $driverId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT orders.id, orders.date_commande, orders.date_livraison, orders.heure_livraison,
                    orders.total, orders.statut, orders.commentaire, orders.priority, orders.pause,
                    orders.assigned_cook_id, orders.assigned_driver_id,
                    profiles.prenom, profiles.nom, users.email, profiles.telephone,
                    profiles.adresse, profiles.ville,
                    dz.nom AS zone_nom, dz.prix_livraison
             FROM orders
             INNER JOIN users ON orders.user_id = users.id
             INNER JOIN profiles ON users.id = profiles.user_id
             LEFT JOIN delivery_zones dz ON orders.zone_id = dz.id
             WHERE orders.assigned_driver_id = ?
             ORDER BY orders.date_livraison, orders.heure_livraison"
        );
        $stmt->execute([$driverId]);
        return $stmt->fetchAll();
    }

    /**
     * Commandes d'un livreur par statut.
     */
    public function getParLivreurEtStatut(int $driverId, string $statut): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT orders.id, orders.date_commande, orders.date_livraison, orders.heure_livraison,
                    orders.total, orders.statut, orders.commentaire, orders.priority, orders.pause,
                    profiles.prenom, profiles.nom, users.email, profiles.telephone,
                    profiles.adresse, profiles.ville,
                    dz.nom AS zone_nom, dz.prix_livraison
             FROM orders
             INNER JOIN users ON orders.user_id = users.id
             INNER JOIN profiles ON users.id = profiles.user_id
             LEFT JOIN delivery_zones dz ON orders.zone_id = dz.id
             WHERE orders.assigned_driver_id = ? AND orders.statut = ?
             ORDER BY orders.heure_livraison"
        );
        $stmt->execute([$driverId, $statut]);
        return $stmt->fetchAll();
    }

    /**
     * Historique des commandes d'un client (mes_commandes).
     */
    public function getParUtilisateur(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT orders.*, dz.nom AS zone_nom
             FROM orders
             LEFT JOIN delivery_zones dz ON orders.zone_id = dz.id
             WHERE orders.user_id = ?
             ORDER BY orders.id DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getParId(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT orders.*, dz.nom AS zone_nom, dz.prix_livraison,
                    profiles.prenom, profiles.nom, users.email, profiles.telephone,
                    profiles.adresse, profiles.ville
             FROM orders
             INNER JOIN users ON orders.user_id = users.id
             INNER JOIN profiles ON users.id = profiles.user_id
             LEFT JOIN delivery_zones dz ON orders.zone_id = dz.id
             WHERE orders.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getItems(int $orderId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT order_items.*, plats.nom AS plat_nom, plats.image, categories.nom AS categorie
             FROM order_items
             INNER JOIN plats ON order_items.product_id = plats.id
             INNER JOIN categories ON plats.category_id = categories.id
             WHERE order_items.order_id = ?"
        );
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    public function mettreAJourStatut(int $id, string $statut): void
    {
        $stmt = $this->pdo->prepare("UPDATE orders SET statut = ? WHERE id = ?");
        $stmt->execute([$statut, $id]);
    }

    public function affecterCuisinier(int $orderId, int $cookId): void
    {
        $stmt = $this->pdo->prepare("UPDATE orders SET assigned_cook_id = ? WHERE id = ?");
        $stmt->execute([$cookId, $orderId]);
    }

    public function affecterLivreur(int $orderId, int $driverId): void
    {
        $stmt = $this->pdo->prepare("UPDATE orders SET assigned_driver_id = ? WHERE id = ?");
        $stmt->execute([$driverId, $orderId]);
    }

    public function getCuisiniersDisponibles(): array
    {
        $stmt = $this->pdo->query(
            "SELECT users.id, profiles.prenom, profiles.nom
             FROM users
             INNER JOIN profiles ON users.id = profiles.user_id
             WHERE profiles.role = 'cook' AND users.actif = 1
             ORDER BY profiles.nom"
        );
        return $stmt->fetchAll();
    }

    public function getLivreursDisponibles(): array
    {
        $stmt = $this->pdo->query(
            "SELECT users.id, profiles.prenom, profiles.nom
             FROM users
             INNER JOIN profiles ON users.id = profiles.user_id
             WHERE profiles.role = 'driver' AND users.actif = 1
             ORDER BY profiles.nom"
        );
        return $stmt->fetchAll();
    }

    /**
     * Crée une commande + ses lignes (order_items) à partir du panier en session.
     */
    public function creerDepuisPanier(int $userId, int $zoneId, string $dateLivraison, string $heureLivraison, string $commentaire, array $panier, int $priority = 0, ?string $pause = null): int
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

        $zoneModele = new ZoneModele();
        $zone = $zoneModele->getParId($zoneId);
        if ($zone) {
            $total += $zone['prix_livraison'];
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO orders (user_id, zone_id, date_commande, date_livraison, heure_livraison, total, statut, commentaire, priority, pause)
             VALUES (?, ?, NOW(), ?, ?, ?, 'en_attente', ?, ?, ?)"
        );
        $stmt->execute([
            $userId,
            $zoneId,
            $dateLivraison,
            $heureLivraison,
            $total,
            $commentaire,
            $priority,
            $pause,
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

    /**
     * Nombre total de commandes.
     */
    public function compter(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    }

    /**
     * Nombre de commandes par statut.
     */
    public function compterParStatut(string $statut): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM orders WHERE statut = ?");
        $stmt->execute([$statut]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Total du chiffre d'affaires.
     */
    public function totalChiffreAffaires(): float
    {
        return (float) $this->pdo->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE statut IN ('en_preparation','prete','en_livraison','livree')")->fetchColumn();
    }

    /**
     * Commandes du jour.
     */
    public function commandesDuJour(): array
    {
        $stmt = $this->pdo->query(
            "SELECT orders.*, profiles.prenom, profiles.nom, dz.nom AS zone_nom
             FROM orders
             INNER JOIN users ON orders.user_id = users.id
             INNER JOIN profiles ON users.id = profiles.user_id
             LEFT JOIN delivery_zones dz ON orders.zone_id = dz.id
             WHERE DATE(orders.created_at) = CURDATE()
             ORDER BY orders.id DESC"
        );
        return $stmt->fetchAll();
    }

    /**
     * Produits les plus commandés.
     */
    public function produitsPlusCommandes(int $limit = 5): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT plats.nom, SUM(order_items.quantite) AS total_qte, SUM(order_items.quantite * order_items.prix) AS total_ca
             FROM order_items
             INNER JOIN plats ON order_items.product_id = plats.id
             INNER JOIN orders ON order_items.order_id = orders.id
             WHERE orders.statut NOT IN ('annulee')
             GROUP BY plats.id, plats.nom
             ORDER BY total_qte DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Quantités à produire pour aujourd'hui (par produit).
     */
    public function quantitesAProduire(): array
    {
        $stmt = $this->pdo->query(
            "SELECT plats.id, plats.nom, plats.image, categories.nom AS categorie,
                    SUM(order_items.quantite) AS total_quantite
             FROM order_items
             INNER JOIN plats ON order_items.product_id = plats.id
             INNER JOIN categories ON plats.category_id = categories.id
             INNER JOIN orders ON order_items.order_id = orders.id
             WHERE orders.date_livraison = CURDATE()
               AND orders.statut IN ('en_attente', 'confirmee', 'en_preparation')
             GROUP BY plats.id, plats.nom, plats.image, categories.nom
             ORDER BY categories.nom, plats.nom"
        );
        return $stmt->fetchAll();
    }

    /**
     * Commandes à préparer aujourd'hui pour un cuisinier donné.
     */
    public function commandesAPreparerAujourdHui(?int $cookId = null): array
    {
        $sql = "SELECT orders.id, orders.date_livraison, orders.heure_livraison,
                       orders.total, orders.statut, orders.commentaire,
                       orders.assigned_cook_id, orders.assigned_driver_id,
                       profiles.prenom, profiles.nom, users.email
                FROM orders
                INNER JOIN users ON orders.user_id = users.id
                INNER JOIN profiles ON users.id = profiles.user_id
                WHERE orders.date_livraison = CURDATE()
                  AND orders.statut IN ('en_attente', 'confirmee', 'en_preparation')";
        $params = [];
        if ($cookId) {
            $sql .= " AND (orders.assigned_cook_id = ? OR orders.assigned_cook_id IS NULL)";
            $params[] = $cookId;
        }
        $sql .= " ORDER BY orders.heure_livraison";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Commandes livrées aujourd'hui pour un livreur.
     */
    public function commandesLivreesAujourdHui(int $driverId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT orders.id, orders.date_livraison, orders.heure_livraison,
                    orders.total, orders.statut, orders.commentaire, orders.pause,
                    profiles.prenom, profiles.nom, users.email, profiles.telephone,
                    profiles.adresse, profiles.ville,
                    dz.nom AS zone_nom
             FROM orders
             INNER JOIN users ON orders.user_id = users.id
             INNER JOIN profiles ON users.id = profiles.user_id
             LEFT JOIN delivery_zones dz ON orders.zone_id = dz.id
             WHERE orders.assigned_driver_id = ?
               AND orders.date_livraison = CURDATE()
               AND orders.statut = 'livree'
             ORDER BY orders.heure_livraison"
        );
        $stmt->execute([$driverId]);
        return $stmt->fetchAll();
    }

    /**
     * Prochaine livraison d'un client.
     */
    public function prochaineLivraison(int $userId): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT orders.*, dz.nom AS zone_nom
             FROM orders
             LEFT JOIN delivery_zones dz ON orders.zone_id = dz.id
             WHERE orders.user_id = ?
               AND orders.date_livraison >= CURDATE()
               AND orders.statut NOT IN ('livree', 'annulee')
             ORDER BY orders.date_livraison ASC, orders.heure_livraison ASC
             LIMIT 1"
        );
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    /**
     * Statistiques pour le dashboard client.
     */
    public function statsClient(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) AS total_commandes,
                    COALESCE(SUM(total), 0) AS total_depense
             FROM orders
             WHERE user_id = ? AND statut NOT IN ('annulee')"
        );
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
}
