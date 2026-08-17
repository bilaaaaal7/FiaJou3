<?php
/**
 * Modele Categorie
 * Acces a la table `categories` de la base de donnees (PDO).
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../assets/inc/langue.php';

class CategorieModele
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getToutes(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getParId(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function creer(string $nom, string $description, ?string $image = null, ?string $nomEn = null, ?string $nomAr = null, ?string $descriptionEn = null, ?string $descriptionAr = null): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO categories (nom, nom_en, nom_ar, description, description_en, description_ar, image)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $nom,
            self::ouNull($nomEn),
            self::ouNull($nomAr),
            $description,
            self::ouNull($descriptionEn),
            self::ouNull($descriptionAr),
            $image,
        ]);
    }

    public function mettreAJour(int $id, string $nom, string $description, ?string $image = null, ?string $nomEn = null, ?string $nomAr = null, ?string $descriptionEn = null, ?string $descriptionAr = null): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE categories SET nom = ?, nom_en = ?, nom_ar = ?,
                    description = ?, description_en = ?, description_ar = ?, image = ?
             WHERE id = ?"
        );
        $stmt->execute([
            $nom,
            self::ouNull($nomEn),
            self::ouNull($nomAr),
            $description,
            self::ouNull($descriptionEn),
            self::ouNull($descriptionAr),
            $image,
            $id,
        ]);
    }

    /**
     * NULL si la chaîne est vide : une traduction effacée retombe sur la base.
     */
    private static function ouNull(?string $valeur): ?string
    {
        return ($valeur === null || trim($valeur) === '') ? null : trim($valeur);
    }

    public function supprimer(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            // Contrainte de cle etrangere : la categorie contient encore des plats.
            if ($e->getCode() === '23000') {
                return false;
            }
            throw $e;
        }
    }

    public function compter(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    }
}
