<?php
/**
 * Modele Categorie
 * Acces a la table `categories` de la base de donnees (PDO).
 */

require_once __DIR__ . '/Database.php';

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

    public function creer(string $nom, string $description, ?string $image = null): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO categories (nom, description, image) VALUES (?, ?, ?)"
        );
        $stmt->execute([$nom, $description, $image]);
    }

    public function mettreAJour(int $id, string $nom, string $description, ?string $image = null): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE categories SET nom = ?, description = ?, image = ? WHERE id = ?"
        );
        $stmt->execute([$nom, $description, $image, $id]);
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
