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
}
