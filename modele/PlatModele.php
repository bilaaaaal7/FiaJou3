<?php
/**
 * Modèle Plat
 * Accès à la table `plats`.
 */

require_once __DIR__ . '/Database.php';

class PlatModele
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getTous(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM plats");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Menu client : plats + nom de la catégorie, triés par catégorie puis nom.
     */
    public function getMenu(): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT plats.id, plats.nom, plats.description, plats.prix, plats.image, plats.disponible,
                    categories.nom AS categorie
             FROM plats
             INNER JOIN categories ON plats.category_id = categories.id
             ORDER BY categories.nom, plats.nom"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getParId(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM plats WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function creer(array $donnees): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO plats (category_id, nom, description, prix, image, disponible)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $donnees['category_id'],
            $donnees['nom'],
            $donnees['description'],
            $donnees['prix'],
            $donnees['image'],
            $donnees['disponible'],
        ]);
    }

    public function mettreAJour(int $id, array $donnees): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE plats
             SET category_id = ?, nom = ?, description = ?, prix = ?, image = ?, disponible = ?
             WHERE id = ?"
        );
        $stmt->execute([
            $donnees['category_id'],
            $donnees['nom'],
            $donnees['description'],
            $donnees['prix'],
            $donnees['image'],
            $donnees['disponible'],
            $id,
        ]);
    }

    public function supprimer(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM plats WHERE id = ?");
        try {
            $stmt->execute([$id]);
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                return false;
            }
            throw $e;
        }
    }

    public function compter(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM plats")->fetchColumn();
    }

    /**
     * Enregistre l'image uploadée d'un plat dans /uploads et retourne son nom de fichier.
     */
    public function enregistrerImage(array $fichier): string
    {
        $nomFichier = basename($fichier['name']);
        move_uploaded_file($fichier['tmp_name'], UPLOADS_PATH . '/' . $nomFichier);
        return $nomFichier;
    }
}
