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
        // Tri chronologique par début de semaine (les menus hérités sans
        // période sont rejetés en fin de liste).
        $stmt = $this->pdo->query(
            "SELECT * FROM weekly_menus
             ORDER BY week_start IS NULL, week_start DESC, date_creation DESC"
        );
        return $stmt->fetchAll();
    }

    public function getParId(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM weekly_menus WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Menu couvrant une période hebdomadaire précise (lundi → dimanche).
     * Un menu publié prime sur un brouillon, lui-même prioritaire sur un
     * menu archivé ; à statut égal, le plus récemment créé l'emporte.
     */
    public function getParSemaine(string $weekStart, string $weekEnd): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM weekly_menus
             WHERE week_start = ? AND week_end = ?
             ORDER BY (statut = 'archive'), date_creation DESC LIMIT 1"
        );
        $stmt->execute([$weekStart, $weekEnd]);
        return $stmt->fetch();
    }

    /**
     * Menu publié affiché au public : c'est le menu de la semaine actuellement
     * active (la semaine qui contient la date du jour). Une semaine future,
     * même déjà publiée et préparée à l'avance par l'admin, n'est pas montrée
     * tant que sa période n'a pas commencé.
     */
    public function getPublie(): array|false
    {
        return $this->getActif();
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
            // L'entrée du menu possède son propre "instantané" (nom, description,
            // prix, catégorie) : s'il est renseigné il prime sur le plat, sinon on
            // retombe sur les valeurs du produit réutilisable. Modifier une semaine
            // n'affecte donc jamais les autres semaines ni le plat lui-même.
            $item['plat_nom'] = $item['nom'] ?: ($plat['nom'] ?? null);
            $item['prix'] = $item['prix'] ?? ($plat['prix'] ?? null);
            $item['image'] = $plat['image'] ?? null;
            $item['disponible'] = $plat['disponible'] ?? null;
            $item['description'] = $item['description'] ?? ($plat['description'] ?? null);
            $item['categorie_id'] = $item['category_id'] !== null
                ? (int) $item['category_id']
                : (int) ($plat['category_id'] ?? 0);
            $item['categorie'] = $item['categorie_id']
                ? ($categories[$item['categorie_id']] ?? null)
                : null;
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
        $numero = null;
        if ($weekStart !== null && $weekStart !== '') {
            $numero = self::numeroSemaine($weekStart);
        }
        $stmt = $this->pdo->prepare(
            "INSERT INTO weekly_menus (numero, nom, week_start, week_end, date_creation, statut)
             VALUES (?, ?, ?, ?, CURDATE(), 'brouillon')"
        );
        $stmt->execute([$numero, $nom, $weekStart ?: null, $weekEnd ?: null]);
        return (int) $this->pdo->lastInsertId();
    }

    public function mettreAJourStatut(int $id, string $statut): void
    {
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
     * être ajoutés au même jour (et un même plat peut figurer plusieurs jours
     * dans la même semaine : aucune contrainte d'unicité).
     *
     * Au moment de l'ajout, un "instantané" du plat (nom, description, prix,
     * catégorie) est copié dans la ligne du menu : chaque semaine garde ainsi
     * sa propre version indépendante du produit réutilisable.
     */
    public function ajouterItem(int $menuId, int $productId, string $jour, ?int $position = null): void
    {
        if ($position === null) {
            $position = $this->getProchainePosition($menuId, $jour);
        }
        require_once __DIR__ . '/PlatModele.php';
        $platModele = new PlatModele();
        $plat = $platModele->getParId($productId);
        $stmt = $this->pdo->prepare(
            "INSERT INTO weekly_menu_items
                (weekly_menu_id, product_id, jour, position, nom, description, prix, category_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $menuId,
            $productId,
            $jour,
            $position,
            $plat['nom'] ?? null,
            $plat['description'] ?? null,
            $plat['prix'] ?? null,
            $plat['category_id'] ?? null,
        ]);
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
     * Modifie une entrée du menu de la semaine : remplace le plat référencé
     * et/ou les attributs affichés CETTE semaine (nom, description, prix,
     * catégorie). Seule la ligne du menu est touchée : le plat réutilisable
     * et les autres semaines restent inchangés.
     */
    public function modifierItem(int $itemId, array $donnees): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE weekly_menu_items
             SET product_id = ?, nom = ?, description = ?, prix = ?, category_id = ?
             WHERE id = ?"
        );
        $stmt->execute([
            (int) $donnees['product_id'],
            trim($donnees['nom'] ?? '') !== '' ? trim($donnees['nom']) : null,
            trim($donnees['description'] ?? '') !== '' ? trim($donnees['description']) : null,
            isset($donnees['prix']) && $donnees['prix'] !== '' ? (float) $donnees['prix'] : null,
            !empty($donnees['category_id']) ? (int) $donnees['category_id'] : null,
            $itemId,
        ]);
    }

    /**
     * Duplique un menu de semaine : crée un nouveau menu (brouillon) pour la
     * période donnée et recopie toutes les entrées du menu source, jour,
     * position et instantanés compris. Chaque semaine reste indépendante.
     *
     * @return int id du nouveau menu
     */
    public function dupliquer(int $sourceId, string $nom, string $weekStart, string $weekEnd): int
    {
        $nouveauId = $this->creer($nom, $weekStart, $weekEnd);
        $stmt = $this->pdo->prepare(
            "INSERT INTO weekly_menu_items
                (weekly_menu_id, product_id, jour, position, nom, description, prix, category_id)
             SELECT ?, product_id, jour, position, nom, description, prix, category_id
             FROM weekly_menu_items
             WHERE weekly_menu_id = ?"
        );
        $stmt->execute([$nouveauId, $sourceId]);
        return $nouveauId;
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
     * Menu publié qui gouverne actuellement les commandes : le menu publié
     * dont la période couvre la date du jour. Une semaine future, même déjà
     * publiée, n'est pas considérée active tant que sa période n'a pas
     * commencé : le client ne voit jamais une semaine non publiée à l'avance.
     * En repli, un menu hérité sans période (semaine non définie) est renvoyé.
     */
    public function getActif(): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM weekly_menus
             WHERE statut = 'publie'
               AND week_start IS NOT NULL AND week_end IS NOT NULL
               AND week_start <= CURDATE() AND week_end >= CURDATE()
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
     * Date (Y-m-d) du jour demandé au sein d'une semaine complète (lundi → dimanche).
     *
     * La date est TOUJOURS dérivée du lundi de la semaine ($weekStart) :
     * lundi = +0 jour, mardi = +1, mercredi = +2, … dimanche = +6. La date du
     * jour n'intervient jamais dans le calcul : la carte d'un jour, son modal,
     * les liens de commande et la validation utilisent donc exactement la même
     * date. Pour la semaine suivante, passer le lundi suivant (lundi + 7 jours).
     */
    public static function datePourJour(string $jourFr, string $weekStart): ?string
    {
        $decalage = (self::ordreJours()[$jourFr] ?? 0) - 1;
        if ($decalage < 0) {
            return null;
        }
        return date('Y-m-d', strtotime($weekStart . ' +' . $decalage . ' days'));
    }

    /**
     * Lundi de référence pour calculer les dates d'un menu affiché.
     *
     * On utilise le lundi de la période du menu (week_start) lorsqu'elle est
     * définie ; en repli (menu hérité sans période) le lundi de la semaine en
     * cours. Toutes les dates affichées en sont dérivées (lundi + 0 à 6 jours).
     */
    public static function semaineReference(array|false $menu): string
    {
        if ($menu && !empty($menu['week_start'])) {
            return $menu['week_start'];
        }
        return self::debutSemaine();
    }

    /**
     * Première date de livraison commandable pour un plat.
     *
     * Les jours où le plat figure au menu de la semaine sont candidats, auxquels
     * s'ajoute le samedi (jour de menu libre : tous les plats du menu de la
     * semaine sont commandables). Les dates sont TOUJOURS calculées à partir du
     * lundi de chaque semaine (lundi + rang − 1), jamais à partir de la date du
     * jour : on balaie la semaine du menu actif puis, si aucune date n'y est
     * encore ouverte, les semaines suivantes (+7 jours), lundi → dimanche.
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
        $candidats = array_unique($candidats);
        $ordre = self::ordreJours();
        usort($candidats, function ($a, $b) use ($ordre) {
            return ($ordre[$a] ?? 99) <=> ($ordre[$b] ?? 99);
        });

        $weekStart = self::semaineReference($menu);
        // Balayage des semaines (lundi → dimanche) tant qu'une date encore
        // ouverte à la commande n'est pas trouvée. Limite de sécurité pour
        // éviter une boucle infinie (couverture ~6 mois).
        for ($i = 0; $i < 26; $i++) {
            foreach ($candidats as $jour) {
                $date = self::datePourJour($jour, $weekStart);
                if ($date === null || !$this->dateLivraisonValide($date)[0]) {
                    continue;
                }
                return $date;
            }
            $weekStart = date('Y-m-d', strtotime($weekStart . ' +7 days'));
        }
        return null;
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

    /**
     * Lundi de la semaine contenant la date donnée (défaut : aujourd'hui).
     * Les semaines commencent le lundi et se terminent le dimanche.
     */
    public static function debutSemaine(?string $date = null): string
    {
        $ts = $date ? strtotime($date) : time();
        return date('Y-m-d', strtotime('monday this week', $ts));
    }

    /**
     * Dimanche de fin de semaine pour un lundi donné (lundi + 6 jours).
     */
    public static function finSemaine(string $lundi): string
    {
        return date('Y-m-d', strtotime($lundi . ' +6 days'));
    }

    /**
     * Numéro de la semaine dans la numérotation propre au système de menu.
     * La première semaine du système (DATE_PREMIERE_SEMAINE) porte le numéro 1,
     * la suivante le numéro 2, etc. Le numéro ISO n'est jamais utilisé.
     * Aucune limite : le calcul s'étend à 20, 50, 100 semaines et plus.
     */
    public static function numeroSemaine(string $date): int
    {
        $delta = (int) ((strtotime($date) - strtotime(DATE_PREMIERE_SEMAINE)) / 86400);
        return (int) floor($delta / 7) + 1;
    }

    /**
     * Libellé lisible d'une semaine : "Semaine 1 — 03/08/2026 → 09/08/2026".
     * Le numéro provient de l'enregistrement ($numero) lorsqu'il est connu,
     * sinon il est calculé à partir du lundi de la semaine.
     */
    public static function libelleSemaine(string $lundi, string $dimanche, ?int $numero = null): string
    {
        $n = $numero !== null ? $numero : self::numeroSemaine($lundi);
        return 'Semaine ' . $n
            . ' — ' . date('d/m/Y', strtotime($lundi))
            . ' → ' . date('d/m/Y', strtotime($dimanche));
    }
}
