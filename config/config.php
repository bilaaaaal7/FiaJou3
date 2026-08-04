<?php
/**
 * Configuration générale de l'application FiaJou3.
 */

// Dossier racine du projet sur le disque
define('ROOT_PATH', dirname(__DIR__));

// URL de base de l'application (utilisée pour générer des liens absolus).
// Adapter si le projet est déployé dans un sous-dossier.
if (!defined('BASE_URL')) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Dossier dans lequel se trouve index.php (permet de fonctionner
    // que le projet soit à la racine du serveur ou dans un sous-dossier)
    $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    define('BASE_URL', $scheme . '://' . $host . $scriptDir);
}

// Nom de l'application
define('APP_NAME', 'FiaJou3');

// Dossier de destination des images de plats uploadées
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('UPLOADS_URL', BASE_URL . '/uploads');

// Rôles disponibles dans l'application (contrôle d'accès basé sur les rôles)
define('ROLE_ADMIN', 'admin');
define('ROLE_CLIENT', 'client');
define('ROLE_CUISINIER', 'cook');
define('ROLE_LIVREUR', 'driver');

// Heure limite de commande pour une livraison le lendemain (cahier des charges).
define('HEURE_LIMITE_COMMANDE', '21:00');

// Jours composant le menu hebdomadaire : un plat spécifique est configuré
// pour chacun de ces jours par l'administrateur (règle actuelle : un seul plat
// par jour). Le samedi n'en fait pas partie : c'est un jour de "menu libre".
define('JOURS_MENU', ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'dimanche']);

// Samedi : jour de "menu libre". Aucun plat spécifique n'est configuré ;
// tous les plats présents dans le menu hebdomadaire sont commandables.
define('JOUR_MENU_LIBRE', 'samedi');

// Jours de livraison autorisés, dans l'ordre (7j/7 : le samedi est un jour
// de menu libre et le dimanche dispose de son propre menu).
define('JOURS_LIVRAISON', ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche']);

// Statuts possibles d'une commande (doivent correspondre à l'ENUM de la BDD)
define('STATUTS_COMMANDE', [
    'en_attente'    => 'En attente',
    'confirmee'     => 'Confirmée',
    'en_preparation'=> 'En préparation',
    'prete'         => 'Prête',
    'en_livraison'  => 'En livraison',
    'livree'        => 'Livrée',
    'annulee'       => 'Annulée',
]);
