<?php
/**
 * Modèle Database
 * Fournit une connexion PDO unique (singleton) à toute l'application.
 * Remplace l'ancien config/db.php (connexion directe en haut de chaque page).
 */

require_once __DIR__ . '/../config/database.php';

class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            try {
                self::$instance = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
                    DB_USER,
                    DB_PASS
                );
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Erreur de connexion : " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
