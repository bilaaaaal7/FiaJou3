<?php
/**
 * Modele Plat
 * Donnees stockees directement dans le code (modele/data/plats.php),
 * remplace l'ancien acces a la table `plats` en base de donnees.
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/CategorieModele.php';

class PlatModele
{
    private static ?array $plats = null;

    private const DATA_FILE = __DIR__ . '/data/plats.php';

    private function charger(): void
    {
        if (self::$plats === null) {
            self::$plats = require self::DATA_FILE;
        }
    }

    /**
     * Reecrit le fichier data/plats.php avec le tableau courant.
     */
    private function sauvegarder(): void
    {
        $export = "<?php\n"
            . "/**\n"
            . " * Donnees statiques des plats.\n"
            . " * Remplace la table `plats` de la base de donnees.\n"
            . " * Fichier regenere automatiquement par PlatModele.\n"
            . " */\n\n"
            . "return " . var_export(self::$plats, true) . ";\n";

        file_put_contents(self::DATA_FILE, $export);
    }

    public function getTous(): array
    {
        $this->charger();
        return array_values(self::$plats);
    }

    /**
     * Menu client : plats + nom de la categorie, tries par categorie puis nom.
     */
    public function getMenu(): array
    {
        $this->charger();

        $categorieModele = new CategorieModele();
        $categories = [];
        foreach ($categorieModele->getToutes() as $categorie) {
            $categories[$categorie['id']] = $categorie['nom'];
        }

        $menu = [];
        foreach (self::$plats as $plat) {
            if (!isset($categories[$plat['category_id']])) {
                continue; // equivalent a l'INNER JOIN d'origine
            }

            $menu[] = [
                'id' => $plat['id'],
                'nom' => $plat['nom'],
                'description' => $plat['description'],
                'prix' => $plat['prix'],
                'image' => $plat['image'],
                'disponible' => $plat['disponible'],
                'categorie' => $categories[$plat['category_id']],
            ];
        }

        usort($menu, function ($a, $b) {
            return [$a['categorie'], $a['nom']] <=> [$b['categorie'], $b['nom']];
        });

        return $menu;
    }

    public function getParId(int $id): array|false
    {
        $this->charger();
        return self::$plats[$id] ?? false;
    }

    public function creer(array $donnees): void
    {
        $this->charger();

        $nouvelId = self::$plats ? max(array_keys(self::$plats)) + 1 : 1;

        self::$plats[$nouvelId] = [
            'id' => $nouvelId,
            'category_id' => (int) $donnees['category_id'],
            'nom' => $donnees['nom'],
            'description' => $donnees['description'],
            'prix' => (float) $donnees['prix'],
            'image' => $donnees['image'],
            'disponible' => (int) $donnees['disponible'],
        ];

        $this->sauvegarder();
    }

    public function mettreAJour(int $id, array $donnees): void
    {
        $this->charger();

        if (!isset(self::$plats[$id])) {
            return;
        }

        self::$plats[$id] = [
            'id' => $id,
            'category_id' => (int) $donnees['category_id'],
            'nom' => $donnees['nom'],
            'description' => $donnees['description'],
            'prix' => (float) $donnees['prix'],
            'image' => $donnees['image'],
            'disponible' => (int) $donnees['disponible'],
        ];

        $this->sauvegarder();
    }

    public function supprimer(int $id): bool
    {
        $this->charger();

        if (!isset(self::$plats[$id])) {
            return false;
        }

        // Meme comportement que l'ancienne contrainte de cle etrangere :
        // on refuse la suppression si le plat fait partie de commandes existantes.
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM order_items WHERE product_id = ?");
        $stmt->execute([$id]);

        if ((int) $stmt->fetchColumn() > 0) {
            return false;
        }

        unset(self::$plats[$id]);
        $this->sauvegarder();

        return true;
    }

    public function compter(): int
    {
        $this->charger();
        return count(self::$plats);
    }

    /**
     * Enregistre l'image uploadee d'un plat dans /uploads et retourne son nom
     * de fichier genere, ou '' si aucun fichier n'a ete envoye.
     * Delegue a UploadModele (nom genere, verif du type reel, taille max).
     */
    public function enregistrerImage(array $fichier): string
    {
        require_once __DIR__ . '/UploadModele.php';
        return UploadModele::enregistrer($fichier) ?? '';
    }
}
