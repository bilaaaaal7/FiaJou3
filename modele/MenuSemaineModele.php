<?php
/**
 * Modèle Menu de la semaine
 * Accès aux tables `weekly_menus` et `weekly_menu_items`.
 */

require_once __DIR__ . '/Database.php';

class MenuSemaineModele
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getTous(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM weekly_menus ORDER BY date_creation DESC");
        return $stmt->fetchAll();
    }

    public function getParId(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM weekly_menus WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getPublie(): array|false
    {
        $stmt = $this->pdo->query(
            "SELECT * FROM weekly_menus WHERE statut = 'publie' ORDER BY date_creation DESC LIMIT 1"
        );
        return $stmt->fetch();
    }

    public function getItems(int $menuId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM weekly_menu_items WHERE weekly_menu_id = ?");
        $stmt->execute([$menuId]);
        $items = $stmt->fetchAll();

        require_once __DIR__ . '/PlatModele.php';
        require_once __DIR__ . '/CategorieModele.php';
        $platModele = new PlatModele();
        $categorieModele = new CategorieModele();

        $categories = [];
        foreach ($categorieModele->getToutes() as $categorie) {
            $categories[$categorie['id']] = $categorie['nom'];
        }

        foreach ($items as &$item) {
            $plat = $platModele->getParId((int) $item['product_id']);
            $item['plat_nom'] = $plat['nom'] ?? null;
            $item['prix'] = $plat['prix'] ?? null;
            $item['image'] = $plat['image'] ?? null;
            $item['disponible'] = $plat['disponible'] ?? null;
            $item['categorie'] = $plat ? ($categories[$plat['category_id']] ?? null) : null;
        }
        unset($item);

        $ordreJours = ['lundi' => 1, 'mardi' => 2, 'mercredi' => 3, 'jeudi' => 4,
                        'vendredi' => 5, 'samedi' => 6, 'dimanche' => 7];

        usort($items, function ($a, $b) use ($ordreJours) {
            $jourA = $ordreJours[$a['jour']] ?? 99;
            $jourB = $ordreJours[$b['jour']] ?? 99;
            $positionA = (int) ($a['position'] ?? 0);
            $positionB = (int) ($b['position'] ?? 0);
            return [$jourA, $positionA, $a['categorie']] <=> [$jourB, $positionB, $b['categorie']];
        });

        return $items;
    }

    public function creer(string $nom, ?string $weekStart = null, ?string $weekEnd = null): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO weekly_menus (nom, week_start, week_end, date_creation, statut)
             VALUES (?, ?, ?, CURDATE(), 'brouillon')"
        );
        $stmt->execute([$nom, $weekStart ?: null, $weekEnd ?: null]);
        return (int) $this->pdo->lastInsertId();
    }

    public function mettreAJourStatut(int $id, string $statut): void
    {
        if ($statut === 'publie') {
            $stmt = $this->pdo->prepare("UPDATE weekly_menus SET statut = 'brouillon' WHERE statut = 'publie'");
            $stmt->execute();
        }
        $stmt = $this->pdo->prepare("UPDATE weekly_menus SET statut = ? WHERE id = ?");
        $stmt->execute([$statut, $id]);
    }

    public function supprimer(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM weekly_menu_items WHERE weekly_menu_id = ?");
        $stmt->execute([$id]);
        $stmt = $this->pdo->prepare("DELETE FROM weekly_menus WHERE id = ?");
        $stmt->execute([$id]);
    }

    /**
     * Ajoute un plat au menu pour un jour donné. Si aucune position n'est
     * fournie, le plat est ajouté à la fin du jour. Plusieurs plats peuvent
     * être ajoutés au même jour.
     */
    public function ajouterItem(int $menuId, int $productId, string $jour, ?int $position = null): void
    {
        if ($position === null) {
            $position = $this->getProchainePosition($menuId, $jour);
        }
        $stmt = $this->pdo->prepare(
            "INSERT INTO weekly_menu_items (weekly_menu_id, product_id, jour, position) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$menuId, $productId, $jour, $position]);
    }

    /**
     * Position à utiliser pour ajouter un plat à la fin d'un jour.
     */
    public function getProchainePosition(int $menuId, string $jour): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM weekly_menu_items WHERE weekly_menu_id = ? AND jour = ?"
        );
        $stmt->execute([$menuId, $jour]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Ligne brute d'un item du menu (ou false si introuvable).
     */
    public function getItemParId(int $itemId): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM weekly_menu_items WHERE id = ?");
        $stmt->execute([$itemId]);
        return $stmt->fetch();
    }

    /**
     * Identifiants ordonnés des plats d'un jour (position puis id).
     */
    public function getIdsPourJour(int $menuId, string $jour): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id FROM weekly_menu_items
             WHERE weekly_menu_id = ? AND jour = ?
             ORDER BY position ASC, id ASC"
        );
        $stmt->execute([$menuId, $jour]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Déplace un plat d'un cran dans l'ordre de son jour (monter/descendre).
     * $decalage vaut -1 (monter) ou +1 (descendre).
     */
    public function deplacerItem(int $itemId, int $decalage): void
    {
        $item = $this->getItemParId($itemId);
        if (!$item) {
            return;
        }
        $ids = $this->getIdsPourJour((int) $item['weekly_menu_id'], (string) $item['jour']);
        $index = array_search($itemId, $ids, true);
        $nouvelIndex = $index + $decalage;
        if ($index === false || $nouvelIndex < 0 || $nouvelIndex >= count($ids)) {
            return;
        }
        array_splice($ids, $index, 1);
        array_splice($ids, $nouvelIndex, 0, [$itemId]);

        $stmt = $this->pdo->prepare("UPDATE weekly_menu_items SET position = ? WHERE id = ?");
        foreach ($ids as $i => $id) {
            $stmt->execute([$i, (int) $id]);
        }
    }

    public function supprimerItem(int $itemId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM weekly_menu_items WHERE id = ?");
        $stmt->execute([$itemId]);
    }

    /**
     * Un menu non archivé chevauche-t-il la période donnée ?
     */
    public function checkerChevauchement(string $start, string $end): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM weekly_menus
             WHERE statut <> 'archive'
               AND week_start IS NOT NULL AND week_end IS NOT NULL
               AND week_start <= ? AND week_end >= ?"
        );
        $stmt->execute([$end, $start]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function jourOccupe(int $menuId, string $jour): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM weekly_menu_items WHERE weekly_menu_id = ? AND jour = ?"
        );
        $stmt->execute([$menuId, $jour]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function platPresent(int $menuId, int $productId): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM weekly_menu_items WHERE weekly_menu_id = ? AND product_id = ?"
        );
        $stmt->execute([$menuId, $productId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function getItemsParJour(int $menuId): array
    {
        $items = $this->getItems($menuId);
        $parJour = [];
        $jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
        foreach ($jours as $j) {
            $parJour[$j] = [];
        }
        foreach ($items as $item) {
            $parJour[$item['jour']][] = $item;
        }
        return $parJour;
    }

    /**
     * Menu publié qui gouverne actuellement les commandes :
     * le menu publié dont la semaine n'est pas encore terminée, sinon le dernier
     * publié pour les menus hérités sans période (semaine non définie).
     */
    public function getActif(): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM weekly_menus
             WHERE statut = 'publie' AND week_end IS NOT NULL AND week_end >= CURDATE()
             ORDER BY week_start DESC LIMIT 1"
        );
        $stmt->execute();
        $menu = $stmt->fetch();
        if ($menu) {
            return $menu;
        }
        $stmt = $this->pdo->query(
            "SELECT * FROM weekly_menus
             WHERE statut = 'publie' AND week_start IS NULL AND week_end IS NULL
             ORDER BY date_creation DESC LIMIT 1"
        );
        return $stmt->fetch();
    }

    /**
     * Menu publié couvrant une date de livraison donnée, ou le dernier publié
     * sans période pour les menus hérités. Retourne false si aucun menu ne couvre.
     */
    public function getPourDate(string $date): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM weekly_menus
             WHERE statut = 'publie'
               AND week_start IS NOT NULL AND week_end IS NOT NULL
               AND week_start <= ? AND week_end >= ?
             ORDER BY week_start DESC LIMIT 1"
        );
        $stmt->execute([$date, $date]);
        $menu = $stmt->fetch();
        if ($menu) {
            return $menu;
        }
        $stmt = $this->pdo->query(
            "SELECT * FROM weekly_menus
             WHERE statut = 'publie' AND week_start IS NULL AND week_end IS NULL
             ORDER BY date_creation DESC LIMIT 1"
        );
        return $stmt->fetch();
    }

    /**
     * Plats commandables pour une date de livraison (jour du menu publié couvrant cette date).
     *
     * Le samedi (jour de "menu libre") n'a pas de plat spécifique : tous les
     * plats présents dans le menu hebdomadaire sont alors commandables.
     */
    public function getPlatsPourDate(string $date): array
    {
        $menu = $this->getPourDate($date);
        if (!$menu) {
            return [];
        }
        $jour = self::jourFrPourDate($date);
        if ($jour === null) {
            return [];
        }

        if ($jour === JOUR_MENU_LIBRE) {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM weekly_menu_items WHERE weekly_menu_id = ?"
            );
            $stmt->execute([$menu['id']]);
        } else {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM weekly_menu_items WHERE weekly_menu_id = ? AND jour = ?"
            );
            $stmt->execute([$menu['id'], $jour]);
        }
        $items = $stmt->fetchAll();
        if (empty($items)) {
            return [];
        }
        require_once __DIR__ . '/PlatModele.php';
        $platModele = new PlatModele();
        $plats = [];
        $vus = [];
        foreach ($items as $item) {
            $plat = $platModele->getParId((int) $item['product_id']);
            if ($plat && $plat['disponible'] && !isset($vus[$plat['id']])) {
                $plats[] = $plat;
                $vus[$plat['id']] = true;
            }
        }
        return $plats;
    }

    /**
     * Le plat fait-il partie du menu actif (n'importe quel jour) ?
     */
    public function estPlatAuMenu(int $platId): bool
    {
        $menu = $this->getActif();
        if (!$menu) {
            return false;
        }
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM weekly_menu_items WHERE weekly_menu_id = ? AND product_id = ?"
        );
        $stmt->execute([$menu['id'], $platId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Prochaine date (strictement dans le futur) pour un jour de la semaine.
     */
    public function prochaineDatePourJour(string $jourFr): ?string
    {
        $ordre = self::ordreJours();
        $cible = $ordre[$jourFr] ?? null;
        if ($cible === null) {
            return null;
        }
        $aujourdHui = (int) date('N');
        $diff = $cible - $aujourdHui;
        if ($diff <= 0) {
            $diff += 7;
        }
        return date('Y-m-d', strtotime('+' . $diff . ' days'));
    }

    /**
     * Première date de livraison commandable pour un plat.
     *
     * Les jours où le plat figure au menu du week sont candidats, auxquels
     * s'ajoute le samedi (jour de menu libre : tous les plats du menu de la
     * semaine sont commandables). La date la plus proche encore ouverte à la
     * commande est renvoyée.
     */
    public function getDateCommandePourPlat(int $platId): ?string
    {
        $menu = $this->getActif();
        if (!$menu) {
            return null;
        }
        $stmt = $this->pdo->prepare(
            "SELECT jour FROM weekly_menu_items WHERE weekly_menu_id = ? AND product_id = ?"
        );
        $stmt->execute([$menu['id'], $platId]);
        $jours = $stmt->fetchAll();
        if (empty($jours)) {
            return null;
        }

        $candidats = [];
        foreach ($jours as $row) {
            $candidats[] = (string) $row['jour'];
        }
        // Samedi : menu libre, tous les plats de la semaine sont commandables.
        $candidats[] = JOUR_MENU_LIBRE;

        $meilleureDate = null;
        foreach (array_unique($candidats) as $jour) {
            $date = $this->prochaineDatePourJour($jour);
            if ($date === null || !$this->dateLivraisonValide($date)[0]) {
                continue;
            }
            if ($meilleureDate === null || $date < $meilleureDate) {
                $meilleureDate = $date;
            }
        }
        return $meilleureDate;
    }

    /**
     * Valide une date de livraison selon le cahier des charges :
     * - jour livré (7j/7 : le samedi est un jour de menu libre, le dimanche
     *   dispose de son propre menu),
     * - couverte par un menu publié avec un plat commandable ce jour-là,
     * - commande pour D à passer avant D-1 21h00.
     *
     * @return array [bool ok, string message]
     */
    public function dateLivraisonValide(string $date): array
    {
        if (empty($date)) {
            return [false, 'La date de livraison est obligatoire.'];
        }
        if ($date < date('Y-m-d')) {
            return [false, 'La date de livraison ne peut pas être dans le passé.'];
        }
        $jour = self::jourFrPourDate($date);
        if ($jour === null) {
            return [false, 'La date de livraison est invalide.'];
        }
        $plats = $this->getPlatsPourDate($date);
        if (empty($plats)) {
            return [false, 'Aucun plat commandable pour cette date : le menu n\'est pas encore publié ou complet.'];
        }
        $limite = date('Y-m-d H:i', strtotime($date . ' -1 day ' . HEURE_LIMITE_COMMANDE));
        if (date('Y-m-d H:i') >= $limite) {
            return [false, 'Les commandes pour cette date sont clôturées (limite ' . HEURE_LIMITE_COMMANDE . ' la veille).'];
        }
        return [true, ''];
    }

    /**
     * Le jour donné est-il un jour de "menu libre" (samedi) ?
     */
    public static function estJourMenuLibre(string $jourFr): bool
    {
        return $jourFr === JOUR_MENU_LIBRE;
    }

    /**
     * Nom du jour en français à partir d'une date 'Y-m-d', ou null si invalide.
     */
    public static function jourFrPourDate(string $date): ?string
    {
        $en = strtolower(date('l', strtotime($date)));
        $map = [
            'monday' => 'lundi', 'tuesday' => 'mardi', 'wednesday' => 'mercredi',
            'thursday' => 'jeudi', 'friday' => 'vendredi',
            'saturday' => 'samedi', 'sunday' => 'dimanche',
        ];
        return $map[$en] ?? null;
    }

    public static function ordreJours(): array
    {
        return [
            'lundi' => 1, 'mardi' => 2, 'mercredi' => 3, 'jeudi' => 4,
            'vendredi' => 5, 'samedi' => 6, 'dimanche' => 7,
        ];
    }
}
