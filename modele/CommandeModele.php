<?php
/**
 * Modèle Commande
 * Accès aux tables `orders` et `order_items`.
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../assets/inc/langue.php';
require_once __DIR__ . '/HistoriqueModele.php';
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
     * Résout le nom de zone dans la langue active pour un jeu de résultats.
     * Remplace `zone_nom` par la traduction correspondante (en/ar) ou la
     * valeur française (base) si la traduction est absente.
     */
    private static function localiserZones(array $lignes): array
    {
        $langue = langue_actuelle();
        if ($langue === 'fr') {
            return $lignes;
        }
        foreach ($lignes as &$ligne) {
            $cleLocale = 'zone_nom_' . $langue;
            if (!empty($ligne[$cleLocale])) {
                $ligne['zone_nom'] = $ligne[$cleLocale];
            }
        }
        unset($ligne);
        return $lignes;
    }

    /**
     * Résout le nom de société dans la langue active pour un jeu de résultats.
     */
    private static function localiserSocietes(array $lignes): array
    {
        $langue = langue_actuelle();
        if ($langue === 'fr') {
            return $lignes;
        }
        foreach ($lignes as &$ligne) {
            $cleLocale = 'societe_nom_' . $langue;
            if (!empty($ligne[$cleLocale])) {
                $ligne['societe_nom'] = $ligne[$cleLocale];
            }
        }
        unset($ligne);
        return $lignes;
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
                    dz.nom AS zone_nom, dz.nom_en AS zone_nom_en, dz.nom_ar AS zone_nom_ar,
                    COALESCE(orders.societe_nom, s.nom) AS societe_nom, s.nom_en AS societe_nom_en, s.nom_ar AS societe_nom_ar
             FROM orders
             INNER JOIN users ON orders.user_id = users.id
             INNER JOIN profiles ON users.id = profiles.user_id
             LEFT JOIN delivery_zones dz ON orders.zone_id = dz.id
             LEFT JOIN societes s ON orders.societe_id = s.id
             ORDER BY orders.id DESC"
        );
        $stmt->execute();
        return self::localiserZones(self::localiserSocietes($stmt->fetchAll()));
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
                    dz.nom AS zone_nom, dz.nom_en AS zone_nom_en, dz.nom_ar AS zone_nom_ar, dz.prix_livraison
             FROM orders
             INNER JOIN users ON orders.user_id = users.id
             INNER JOIN profiles ON users.id = profiles.user_id
             LEFT JOIN delivery_zones dz ON orders.zone_id = dz.id
             WHERE orders.statut = ?
             ORDER BY orders.date_livraison, orders.heure_livraison"
        );
        $stmt->execute([$statut]);
        return self::localiserZones($stmt->fetchAll());
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
                    dz.nom AS zone_nom, dz.nom_en AS zone_nom_en, dz.nom_ar AS zone_nom_ar, dz.prix_livraison
             FROM orders
             INNER JOIN users ON orders.user_id = users.id
             INNER JOIN profiles ON users.id = profiles.user_id
             LEFT JOIN delivery_zones dz ON orders.zone_id = dz.id
             WHERE orders.assigned_driver_id = ?
             ORDER BY orders.date_livraison, orders.heure_livraison"
        );
        $stmt->execute([$driverId]);
        return self::localiserZones($stmt->fetchAll());
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
                    dz.nom AS zone_nom, dz.nom_en AS zone_nom_en, dz.nom_ar AS zone_nom_ar, dz.prix_livraison
             FROM orders
             INNER JOIN users ON orders.user_id = users.id
             INNER JOIN profiles ON users.id = profiles.user_id
             LEFT JOIN delivery_zones dz ON orders.zone_id = dz.id
             WHERE orders.assigned_driver_id = ? AND orders.statut = ?
             ORDER BY orders.heure_livraison"
        );
        $stmt->execute([$driverId, $statut]);
        return self::localiserZones($stmt->fetchAll());
    }

    /**
     * Historique des commandes d'un client (mes_commandes).
     */
    public function getParUtilisateur(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT orders.*, dz.nom AS zone_nom, dz.nom_en AS zone_nom_en, dz.nom_ar AS zone_nom_ar,
                    COALESCE(orders.societe_nom, s.nom) AS societe_nom, s.nom_en AS societe_nom_en, s.nom_ar AS societe_nom_ar
             FROM orders
             LEFT JOIN delivery_zones dz ON orders.zone_id = dz.id
             LEFT JOIN societes s ON orders.societe_id = s.id
             WHERE orders.user_id = ?
             ORDER BY orders.id DESC"
        );
        $stmt->execute([$userId]);
        return self::localiserZones(self::localiserSocietes($stmt->fetchAll()));
    }

    public function getParId(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT orders.*, dz.nom AS zone_nom, dz.nom_en AS zone_nom_en, dz.nom_ar AS zone_nom_ar, dz.prix_livraison,
                    COALESCE(orders.societe_nom, s.nom) AS societe_nom, s.nom_en AS societe_nom_en, s.nom_ar AS societe_nom_ar,
                    profiles.prenom, profiles.nom, users.email, profiles.telephone,
                    profiles.adresse, profiles.ville
             FROM orders
             INNER JOIN users ON orders.user_id = users.id
             INNER JOIN profiles ON users.id = profiles.user_id
             LEFT JOIN delivery_zones dz ON orders.zone_id = dz.id
             LEFT JOIN societes s ON orders.societe_id = s.id
             WHERE orders.id = ?"
        );
        $stmt->execute([$id]);
        $ligne = $stmt->fetch();
        if ($ligne) {
            $ligne = self::localiserZones([$ligne])[0];
            $langue = langue_actuelle();
            if ($langue !== 'fr') {
                $cleLocale = 'societe_nom_' . $langue;
                if (!empty($ligne[$cleLocale])) {
                    $ligne['societe_nom'] = $ligne[$cleLocale];
                }
            }
        }
        return $ligne;
    }

    public function getItems(int $orderId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $items = $stmt->fetchAll();

        $platModele = new PlatModele();
        $categories = $this->indexCategoriesParId();

        foreach ($items as &$item) {
            $plat = $platModele->getParId((int) $item['product_id']);
            $item['plat_nom'] = $plat ? localiser($plat, 'nom') : null;
            $item['image'] = $plat['image'] ?? null;
            $item['categorie'] = $plat ? ($categories[$plat['category_id']] ?? null) : null;
        }
        unset($item);

        return $items;
    }

    /**
     * Tableau [category_id => nom] pour enrichir les résultats des jointures
     * anciennement faites en SQL sur `plats` / `categories`.
     * Les noms sont rendus dans la langue active (base française en repli).
     */
    private function indexCategoriesParId(): array
    {
        require_once __DIR__ . '/CategorieModele.php';
        $categorieModele = new CategorieModele();

        $index = [];
        foreach ($categorieModele->getToutes() as $categorie) {
            $index[$categorie['id']] = localiser($categorie, 'nom');
        }

        return $index;
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

    /**
     * Une commande est accessible par un cuisinier si elle ne lui est pas
     * encore assignée (file commune) ou si elle lui est assignée.
     */
    public function estAccessibleParCuisinier(array $commande, int $cookId): bool
    {
        return empty($commande['assigned_cook_id']) || (int) $commande['assigned_cook_id'] === $cookId;
    }

    /**
     * Une commande est accessible par un livreur si elle lui est assignée.
     */
    public function estAccessibleParLivreur(array $commande, int $driverId): bool
    {
        return (int) $commande['assigned_driver_id'] === $driverId;
    }

    /**
     * Change le statut d'une commande avec contrôle d'accès et validation de
     * la transition selon le rôle de l'utilisateur connecté.
     *
     * Retourne ['succes' => bool, 'erreur' => string, 'commande' => array|[]].
     */
    public function changerStatutParRole(int $orderId, string $nouveauStatut, string $role, int $userId, string $commentaire = ''): array
    {
        $commande = $this->getParId($orderId);
        if (!$commande) {
            return ['succes' => false, 'erreur' => 'Commande introuvable.', 'commande' => []];
        }

        if ($role === ROLE_CUISINIER && !$this->estAccessibleParCuisinier($commande, $userId)) {
            return ['succes' => false, 'erreur' => "Vous n'avez pas accès à cette commande.", 'commande' => $commande];
        }
        if ($role === ROLE_LIVREUR && !$this->estAccessibleParLivreur($commande, $userId)) {
            return ['succes' => false, 'erreur' => "Vous n'avez pas accès à cette commande.", 'commande' => $commande];
        }

        $transitionsAutorisees = [
            ROLE_CUISINIER => [
                'en_attente'     => ['en_preparation'],
                'confirmee'      => ['en_preparation'],
                'en_preparation' => ['prete'],
            ],
            ROLE_LIVREUR => [
                'prete'        => ['en_livraison'],
                'en_livraison' => ['livree'],
            ],
        ];

        $statutActuel = $commande['statut'];
        $transitionAutorisee = isset($transitionsAutorisees[$role][$statutActuel])
            && in_array($nouveauStatut, $transitionsAutorisees[$role][$statutActuel], true);

        if (!$transitionAutorisee) {
            return ['succes' => false, 'erreur' => 'Transition de statut non autorisée.', 'commande' => $commande];
        }

        // Un cuisinier qui démarre une commande non assignée la prend en charge.
        if ($role === ROLE_CUISINIER && empty($commande['assigned_cook_id'])) {
            $this->affecterCuisinier($orderId, $userId);
            $commande['assigned_cook_id'] = $userId;
        }

        $this->mettreAJourStatut($orderId, $nouveauStatut);
        $commande['statut'] = $nouveauStatut;

        $historiqueModele = new HistoriqueModele();
        $historiqueModele->ajouter($orderId, $statutActuel, $nouveauStatut, $commentaire ?: null, $userId);

        return ['succes' => true, 'commande' => $commande];
    }

    public function getCuisiniersDisponibles(): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT users.id, profiles.prenom, profiles.nom
             FROM users
             INNER JOIN profiles ON users.id = profiles.user_id
             WHERE profiles.role = ? AND users.actif = 1
             ORDER BY profiles.nom"
        );
        $stmt->execute([ROLE_CUISINIER]);
        return $stmt->fetchAll();
    }

    public function getLivreursDisponibles(): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT users.id, profiles.prenom, profiles.nom
             FROM users
             INNER JOIN profiles ON users.id = profiles.user_id
             WHERE profiles.role = ? AND users.actif = 1
             ORDER BY profiles.nom"
        );
        $stmt->execute([ROLE_LIVREUR]);
        return $stmt->fetchAll();
    }

    /**
     * Crée une commande + ses lignes (order_items) à partir du panier en session.
     *
     * @param string|null $societeNom Nom de société en texte libre (l'identifiant
     *                                `societeId` reste conservé pour les commandes
     *                                liées à une société prédéfinie historique).
     */
    public function creerDepuisPanier(int $userId, int $zoneId, string $heureLivraison, string $commentaire, array $panier, int $priority = 0, ?string $pause = null, bool $couvreSemaine = false, int $societeId = 0, ?string $societeNom = null): int
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

        // La remise « semaine complète » est calculée sur le sous-total des
        // plats uniquement (jamais sur les frais de livraison), ce qui correspond
        // exactement à la remise affichée au client dans le récapitulatif de
        // commande (controleur/CommanderControleur.php). On l'applique donc avant
        // d'ajouter les frais de livraison au total.
        $remise = 0;
        if ($couvreSemaine && defined('REMISE_SEMAINE_POURCENT') && REMISE_SEMAINE_POURCENT > 0) {
            $remise = round($total * REMISE_SEMAINE_POURCENT / 100, 2);
            $total -= $remise;
        }

        $zoneModele = new ZoneModele();
        $zone = $zoneModele->getParId($zoneId);
        if ($zone) {
            $total += $zone['prix_livraison'];
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO orders (user_id, zone_id, societe_id, societe_nom, date_commande, date_livraison, heure_livraison, total, remise, statut, commentaire, priority, pause)
             VALUES (?, ?, ?, ?, NOW(), NULL, ?, ?, ?, 'en_attente', ?, ?, ?)"
        );
        $stmt->execute([
            $userId,
            $zoneId,
            $societeId > 0 ? $societeId : null,
            $societeNom !== null && trim($societeNom) !== '' ? trim($societeNom) : null,
            $heureLivraison,
            $total,
            $remise,
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
            "SELECT orders.*, profiles.prenom, profiles.nom, dz.nom AS zone_nom, dz.nom_en AS zone_nom_en, dz.nom_ar AS zone_nom_ar
             FROM orders
             INNER JOIN users ON orders.user_id = users.id
             INNER JOIN profiles ON users.id = profiles.user_id
             LEFT JOIN delivery_zones dz ON orders.zone_id = dz.id
             WHERE DATE(orders.created_at) = CURDATE()
             ORDER BY orders.id DESC"
        );
        return self::localiserZones($stmt->fetchAll());
    }

    /**
     * Statistiques des N derniers jours : nombre de commandes et chiffre
     * d'affaires (hors commandes annulées) par jour, y compris les jours sans
     * commande.
     *
     * @return array [['date' => 'Y-m-d', 'label' => 'j/M', 'nb' => int, 'ca' => float], ...]
     */
    public function statistiquesParJour(int $jours = 7): array
    {
        $dateDebut = date('Y-m-d', strtotime('-' . ($jours - 1) . ' days'));

        $stmt = $this->pdo->prepare(
            "SELECT DATE(created_at) AS jour,
                    COUNT(*) AS nb,
                    COALESCE(SUM(CASE WHEN statut <> 'annulee' THEN total ELSE 0 END), 0) AS ca
             FROM orders
             WHERE created_at >= ?
             GROUP BY DATE(created_at)"
        );
        $stmt->execute([$dateDebut . ' 00:00:00']);
        $parJour = [];
        foreach ($stmt->fetchAll() as $ligne) {
            $parJour[$ligne['jour']] = $ligne;
        }

        $resultat = [];
        for ($i = $jours - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $ligne = $parJour[$date] ?? ['nb' => 0, 'ca' => 0];
            $resultat[] = [
                'date'  => $date,
                'label' => date('j/M', strtotime($date)),
                'nb'    => (int) $ligne['nb'],
                'ca'    => (float) $ligne['ca'],
            ];
        }
        return $resultat;
    }

    /**
     * Produits les plus commandés.
     */
    public function produitsPlusCommandes(int $limit = 5): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT order_items.product_id,
                    SUM(order_items.quantite) AS total_qte,
                    SUM(order_items.quantite * order_items.prix) AS total_ca
             FROM order_items
             INNER JOIN orders ON order_items.order_id = orders.id
             WHERE orders.statut NOT IN ('annulee')
             GROUP BY order_items.product_id"
        );
        $stmt->execute();
        $lignes = $stmt->fetchAll();

        $platModele = new PlatModele();
        $resultat = [];

        foreach ($lignes as $ligne) {
            $plat = $platModele->getParId((int) $ligne['product_id']);
            $resultat[] = [
                'nom' => $plat ? localiser($plat, 'nom') : null,
                'total_qte' => $ligne['total_qte'],
                'total_ca' => $ligne['total_ca'],
            ];
        }

        usort($resultat, fn ($a, $b) => $b['total_qte'] <=> $a['total_qte']);

        return array_slice($resultat, 0, $limit);
    }

    /**
     * Quantités à produire pour aujourd'hui (par produit).
     */
    public function quantitesAProduire(): array
    {
        $stmt = $this->pdo->query(
            "SELECT order_items.product_id, SUM(order_items.quantite) AS total_quantite
             FROM order_items
             INNER JOIN orders ON order_items.order_id = orders.id
             WHERE orders.date_livraison = CURDATE()
               AND orders.statut IN ('en_attente', 'confirmee', 'en_preparation')
             GROUP BY order_items.product_id"
        );
        $lignes = $stmt->fetchAll();

        $platModele = new PlatModele();
        $categories = $this->indexCategoriesParId();
        $resultat = [];

        foreach ($lignes as $ligne) {
            $plat = $platModele->getParId((int) $ligne['product_id']);
            $resultat[] = [
                'id' => $plat['id'] ?? $ligne['product_id'],
                'nom' => $plat ? localiser($plat, 'nom') : null,
                'image' => $plat['image'] ?? null,
                'categorie' => $plat ? ($categories[$plat['category_id']] ?? null) : null,
                'total_quantite' => $ligne['total_quantite'],
            ];
        }

        usort($resultat, fn ($a, $b) => [$a['categorie'], $a['nom']] <=> [$b['categorie'], $b['nom']]);

        return $resultat;
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
                    dz.nom AS zone_nom, dz.nom_en AS zone_nom_en, dz.nom_ar AS zone_nom_ar
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
        return self::localiserZones($stmt->fetchAll());
    }

    /**
     * Prochaine livraison d'un client.
     */
    public function prochaineLivraison(int $userId): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT orders.*, dz.nom AS zone_nom, dz.nom_en AS zone_nom_en, dz.nom_ar AS zone_nom_ar
             FROM orders
             LEFT JOIN delivery_zones dz ON orders.zone_id = dz.id
             WHERE orders.user_id = ?
               AND orders.date_livraison >= CURDATE()
               AND orders.statut NOT IN ('livree', 'annulee')
             ORDER BY orders.date_livraison ASC, orders.heure_livraison ASC
             LIMIT 1"
        );
        $stmt->execute([$userId]);
        $ligne = $stmt->fetch();
        if ($ligne) {
            $ligne = self::localiserZones([$ligne])[0];
        }
        return $ligne;
    }

    /**
     * Prochaines livraisons (pour le dashboard admin).
     */
    public function prochainesLivraisons(int $limit = 8): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT orders.id, orders.date_livraison, orders.heure_livraison,
                    orders.total, orders.statut, orders.priority, orders.pause,
                    profiles.prenom, profiles.nom, profiles.telephone,
                    dz.nom AS zone_nom, dz.nom_en AS zone_nom_en, dz.nom_ar AS zone_nom_ar
             FROM orders
             INNER JOIN users ON orders.user_id = users.id
             INNER JOIN profiles ON users.id = profiles.user_id
             LEFT JOIN delivery_zones dz ON orders.zone_id = dz.id
             WHERE orders.date_livraison >= CURDATE()
               AND orders.statut NOT IN ('livree', 'annulee')
             ORDER BY orders.date_livraison ASC, orders.heure_livraison ASC
             LIMIT " . (int) $limit
        );
        $stmt->execute();
        return self::localiserZones($stmt->fetchAll());
    }

    /**
     * Commandes en retard : livraison prévue aujourd'hui ou avant, toujours
     * en préparation ou prêtes après l'heure souhaitée (pour le dashboard admin).
     */
    public function commandesEnRetard(): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT orders.id, orders.date_livraison, orders.heure_livraison,
                    orders.statut, orders.priority,
                    profiles.prenom, profiles.nom
             FROM orders
             INNER JOIN users ON orders.user_id = users.id
             INNER JOIN profiles ON users.id = profiles.user_id
             WHERE orders.statut IN ('en_preparation', 'prete')
               AND (orders.date_livraison < CURDATE()
                    OR (orders.date_livraison = CURDATE() AND orders.heure_livraison < CURTIME()))
             ORDER BY orders.date_livraison ASC, orders.heure_livraison ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
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
