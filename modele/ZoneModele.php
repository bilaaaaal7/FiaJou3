<?php
/**
 * Modèle Zone
 * Accès à la table `delivery_zones`.
 */

require_once __DIR__ . '/Database.php';

class ZoneModele
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getToutes(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM delivery_zones ORDER BY nom");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getParId(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM delivery_zones WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function creer(string $nom, float $prixLivraison): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO delivery_zones (nom, prix_livraison) VALUES (?, ?)"
        );
        $stmt->execute([$nom, $prixLivraison]);
    }

    public function mettreAJour(int $id, string $nom, float $prixLivraison): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE delivery_zones SET nom = ?, prix_livraison = ? WHERE id = ?"
        );
        $stmt->execute([$nom, $prixLivraison, $id]);
    }

    public function supprimer(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM delivery_zones WHERE id = ?");
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
        return (int) $this->pdo->query("SELECT COUNT(*) FROM delivery_zones")->fetchColumn();
    }
}
