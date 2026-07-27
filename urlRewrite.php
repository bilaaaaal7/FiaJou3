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

/**
 * Métadonnées SEO centralisées par route (title / description / keywords).
 *
 * Ces valeurs servent de valeurs PAR DÉFAUT : elles sont injectées dans
 * $pageTitle / $metaDescription / $metaKeywords avant le chargement du
 * contrôleur. Les vues qui définissent déjà $pageTitle (cas de la majorité
 * des vues existantes) conservent la priorité, puisqu'elles écrasent la
 * variable après coup — aucun comportement existant n'est modifié.
 *
 * Seules les routes publiques (accueil, connexion, inscription,
 * mot-de-passe-oublie) ont un intérêt SEO réel puisque toutes les autres
 * routes (admin/client/cuisinier/livreur) sont protégées par
 * exiger_role()/exiger_connexion() et non indexables. Elles reçoivent tout
 * de même un title cohérent pour l'onglet du navigateur.
 */
$seoDefaults = [
    'accueil' => [
        'title'       => APP_NAME . ' - Repas faits maison, livrés chez vous',
        'description' => 'Commandez des repas faits maison préparés par des cuisiniers locaux et faites-vous livrer rapidement avec ' . APP_NAME . '.',
        'keywords'    => 'repas maison, livraison de repas, cuisine locale, commande de repas, ' . APP_NAME,
    ],
    'connexion' => [
        'title'       => 'Connexion - ' . APP_NAME,
        'description' => 'Connectez-vous à votre compte ' . APP_NAME . ' pour commander vos repas faits maison.',
        'keywords'    => 'connexion, se connecter, compte ' . APP_NAME,
    ],
    'inscription' => [
        'title'       => 'Inscription - ' . APP_NAME,
        'description' => 'Créez votre compte ' . APP_NAME . ' et commencez à commander des repas faits maison livrés chez vous.',
        'keywords'    => 'inscription, créer un compte, ' . APP_NAME,
    ],
    'mot-de-passe-oublie' => [
        'title'       => 'Mot de passe oublié - ' . APP_NAME,
        'description' => 'Réinitialisez le mot de passe de votre compte ' . APP_NAME . '.',
        'keywords'    => 'mot de passe oublié, réinitialisation, ' . APP_NAME,
    ],

    // Espaces protégés : title générique (non indexables, mais utile pour
    // l'onglet du navigateur et pour garder une balise <title> cohérente).
    'admin' => [
        'title'       => 'Tableau de bord administrateur - ' . APP_NAME,
        'description' => "Espace d'administration " . APP_NAME . ' : gestion des commandes, utilisateurs, plats et livraisons.',
    ],
    'admin/categories'     => ['title' => 'Gestion des catégories - ' . APP_NAME],
    'admin/plats'          => ['title' => 'Gestion des plats - ' . APP_NAME],
    'admin/commandes'      => ['title' => 'Gestion des commandes - ' . APP_NAME],
    'admin/utilisateurs'   => ['title' => 'Gestion des utilisateurs - ' . APP_NAME],
    'admin/zones'          => ['title' => 'Zones de livraison - ' . APP_NAME],
    'admin/cuisiniers'     => ['title' => 'Gestion des cuisiniers - ' . APP_NAME],
    'admin/livreurs'       => ['title' => 'Gestion des livreurs - ' . APP_NAME],
    'admin/assignation'    => ['title' => 'Assignation des commandes - ' . APP_NAME],
    'admin/menu-semaine'   => ['title' => 'Menu de la semaine - ' . APP_NAME],

    'client'                  => ['title' => 'Menu - ' . APP_NAME],
    'client/produit'          => ['title' => 'Produit - ' . APP_NAME],
    'client/dashboard'        => ['title' => 'Tableau de bord - ' . APP_NAME],
    'client/panier'           => ['title' => 'Mon panier - ' . APP_NAME],
    'client/commander'        => ['title' => 'Commander - ' . APP_NAME],
    'client/mes-commandes'    => ['title' => 'Mes commandes - ' . APP_NAME],
    'client/detail-commande'  => ['title' => 'Détail de la commande - ' . APP_NAME],
    'client/profil'           => ['title' => 'Mon profil - ' . APP_NAME],
    'client/menu-semaine'     => ['title' => 'Menu de la semaine - ' . APP_NAME],
    'client/notifications'    => ['title' => 'Notifications - ' . APP_NAME],

    'cuisinier'            => ['title' => 'Espace cuisinier - ' . APP_NAME],
    'cuisinier/historique' => ['title' => 'Historique cuisinier - ' . APP_NAME],

    'livreur'              => ['title' => 'Espace livreur - ' . APP_NAME],
    'livreur/historique'   => ['title' => 'Historique livreur - ' . APP_NAME],
];

function dispatch(): void
{
    global $routes, $seoDefaults;

    $route = isset($_GET['route']) ? trim($_GET['route'], '/') : '';

    // Injecte les métadonnées SEO par défaut de la route avant de charger
    // le contrôleur. Les contrôleurs (et les vues qu'ils incluent) sont
    // require'd depuis l'intérieur de cette fonction : ils partagent donc
    // le scope local de dispatch(). En posant ici des variables locales
    // $pageTitle / $metaDescription / $metaKeywords, elles sont déjà
    // définies quand la vue s'exécute plus loin dans la chaîne de require.
    // Les vues existantes qui redéfinissent $pageTitle (cas de toutes les
    // vues actuelles) écrasent simplement cette valeur par défaut, comme
    // avant — aucun comportement existant n'est modifié.
    $pageTitle = null;
    $metaDescription = null;
    $metaKeywords = null;

    if ($route !== '' && isset($seoDefaults[$route])) {
        $meta = $seoDefaults[$route];
        $pageTitle       = $meta['title'] ?? APP_NAME;
        $metaDescription = $meta['description'] ?? null;
        $metaKeywords    = $meta['keywords'] ?? null;
    }

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
