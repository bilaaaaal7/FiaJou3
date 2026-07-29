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
            return [$jourA, $a['categorie']] <=> [$jourB, $b['categorie']];
        });

        return $items;
    }

    public function creer(string $nom): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO weekly_menus (nom, date_creation, statut) VALUES (?, CURDATE(), 'brouillon')"
        );
        $stmt->execute([$nom]);
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

    public function ajouterItem(int $menuId, int $productId, string $jour): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO weekly_menu_items (weekly_menu_id, product_id, jour) VALUES (?, ?, ?)"
        );
        $stmt->execute([$menuId, $productId, $jour]);
    }

    public function supprimerItem(int $itemId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM weekly_menu_items WHERE id = ?");
        $stmt->execute([$itemId]);
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
}
