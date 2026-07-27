<?php
/**
 * Routeur de l'application (urlRewrite.php)
 */

$routes = [
    // Page d'accueil publique
    'accueil'              => 'controleur/AccueilControleur.php',

    // Authentification
    'connexion'            => 'controleur/auth/LoginControleur.php',
    'inscription'          => 'controleur/auth/RegisterControleur.php',
    'deconnexion'          => 'controleur/auth/LogoutControleur.php',
    'mot-de-passe-oublie'  => 'controleur/auth/MotDePasseOublieControleur.php',

    // Espace administrateur
    'admin'                => 'controleur/admin/DashboardControleur.php',
    'admin/categories'     => 'controleur/admin/CategorieControleur.php',
    'admin/plats'          => 'controleur/admin/PlatControleur.php',
    'admin/commandes'      => 'controleur/admin/CommandeControleur.php',
    'admin/utilisateurs'   => 'controleur/admin/UtilisateurControleur.php',
    'admin/zones'          => 'controleur/admin/ZoneControleur.php',
    'admin/cuisiniers'     => 'controleur/admin/CuisinierControleur.php',
    'admin/livreurs'       => 'controleur/admin/LivreurControleur.php',
    'admin/assignation'    => 'controleur/admin/AssignationControleur.php',
    'admin/menu-semaine'   => 'controleur/admin/MenuSemaineControleur.php',

    // Espace client
    'client'               => 'controleur/client/MenuControleur.php',
    'client/produit'       => 'controleur/client/ProduitControleur.php',
    'client/dashboard'     => 'controleur/client/DashboardControleur.php',
    'client/panier'        => 'controleur/client/PanierControleur.php',
    'client/commander'     => 'controleur/client/CommanderControleur.php',
    'client/mes-commandes' => 'controleur/client/MesCommandesControleur.php',
    'client/detail-commande' => 'controleur/client/DetailCommandeControleur.php',
    'client/profil'        => 'controleur/client/ProfilControleur.php',
    'client/menu-semaine'  => 'controleur/client/MenuSemaineControleur.php',
    'client/notifications' => 'controleur/client/NotificationsControleur.php',

    // Espace cuisinier
    'cuisinier'            => 'controleur/cuisinier/DashboardControleur.php',
    'cuisinier/historique' => 'controleur/cuisinier/HistoriqueControleur.php',

    // Espace livreur
    'livreur'              => 'controleur/livreur/DashboardControleur.php',
    'livreur/historique'   => 'controleur/livreur/HistoriqueControleur.php',
];

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
