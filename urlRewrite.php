<?php
/**
 * Routeur de l'application (urlRewrite.php)
 *
 * Ce fichier ne contient que la table de routage et la fonction de
 * dispatch. Il est inclus par index.php, qui est le seul point d'entrée
 * HTTP de l'application (voir .htaccess).
 *
 * Chaque route pointe vers un fichier du dossier controleur/, responsable
 * de la logique métier, qui inclut ensuite la vue correspondante.
 */

$routes = [
    // Authentification
    'connexion'            => 'controleur/auth/LoginControleur.php',
    'inscription'          => 'controleur/auth/RegisterControleur.php',
    'deconnexion'          => 'controleur/auth/LogoutControleur.php',

    // Espace administrateur
    'admin'                => 'controleur/admin/DashboardControleur.php',
    'admin/categories'     => 'controleur/admin/CategorieControleur.php',
    'admin/plats'          => 'controleur/admin/PlatControleur.php',
    'admin/commandes'      => 'controleur/admin/CommandeControleur.php',
    'admin/utilisateurs'   => 'controleur/admin/UtilisateurControleur.php',

    // Espace client
    'client'               => 'controleur/client/MenuControleur.php',
    'client/panier'        => 'controleur/client/PanierControleur.php',
    'client/commander'     => 'controleur/client/CommanderControleur.php',
    'client/mes-commandes' => 'controleur/client/MesCommandesControleur.php',

    // Espace cuisinier
    'cuisinier'            => 'controleur/cuisinier/DashboardControleur.php',

    // Espace livreur
    'livreur'              => 'controleur/livreur/DashboardControleur.php',
];

/**
 * Analyse la route demandée (paramètre GET `route`, alimenté par le
 * réécriture d'URL définie dans .htaccess) et charge le contrôleur
 * correspondant. Si la route est vide, charge default.php (page d'accueil).
 * Si la route est inconnue, affiche une page 404.
 */
function dispatch(): void
{
    global $routes;

    $route = isset($_GET['route']) ? trim($_GET['route'], '/') : '';

    if ($route === '') {
        require ROOT_PATH . '/default.php';
        return;
    }

    if (isset($routes[$route])) {
        require ROOT_PATH . '/' . $routes[$route];
        return;
    }

    http_response_code(404);
    require ROOT_PATH . '/vue/errors/404.php';
}
