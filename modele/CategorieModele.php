<?php
/**
 * Modele Categorie
 * Donnees stockees directement dans le code (modele/data/categories.php),
 * remplace l'ancien acces a la table `categories` en base de donnees.
 */

class CategorieModele
{
    private static ?array $categories = null;

    private const DATA_FILE = __DIR__ . '/data/categories.php';

    private function charger(): void
    {
        if (self::$categories === null) {
            self::$categories = require self::DATA_FILE;
        }
    }

    /**
     * Reecrit le fichier data/categories.php avec le tableau courant.
     */
    private function sauvegarder(): void
    {
        $export = "<?php\n"
            . "/**\n"
            . " * Donnees statiques des categories.\n"
            . " * Remplace la table `categories` de la base de donnees.\n"
            . " * Fichier regenere automatiquement par CategorieModele.\n"
            . " */\n\n"
            . "return " . var_export(self::$categories, true) . ";\n";

        file_put_contents(self::DATA_FILE, $export);
    }

    public function getToutes(): array
    {
        $this->charger();
        return array_values(self::$categories);
    }

    public function getParId(int $id): array|false
    {
        $this->charger();
        return self::$categories[$id] ?? false;
    }

    public function creer(string $nom, string $description, string $image): void
    {
        $this->charger();

        $nouvelId = self::$categories ? max(array_keys(self::$categories)) + 1 : 1;

        self::$categories[$nouvelId] = [
            'id' => $nouvelId,
            'nom' => $nom,
            'description' => $description,
            'image' => $image,
        ];

        $this->sauvegarder();
    }

    public function mettreAJour(int $id, string $nom, string $description, string $image): void
    {
        $this->charger();

        if (!isset(self::$categories[$id])) {
            return;
        }

        self::$categories[$id] = [
            'id' => $id,
            'nom' => $nom,
            'description' => $description,
            'image' => $image,
        ];

        $this->sauvegarder();
    }

    public function supprimer(int $id): bool
    {
        $this->charger();

        if (!isset(self::$categories[$id])) {
            return false;
        }

        // Meme comportement que l'ancienne contrainte de cle etrangere :
        // on refuse la suppression si des plats appartiennent encore a cette categorie.
        require_once __DIR__ . '/PlatModele.php';
        $platModele = new PlatModele();
        foreach ($platModele->getTous() as $plat) {
            if ((int) $plat['category_id'] === $id) {
                return false;
            }
        }

        unset(self::$categories[$id]);
        $this->sauvegarder();

        return true;
    }

    public function compter(): int
    {
        $this->charger();
        return count(self::$categories);
    }
}
