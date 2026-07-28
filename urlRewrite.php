<?php
/**
 * Routeur de l'application (urlRewrite.php)
 *
 * Centralise :
 *   1. les URLs / routes de l'application ;
 *   2. les métadonnées SEO de chaque route, stockées dans $_SESSION
 *      ($_SESSION['pr_title'], $_SESSION['meta_description'], $_SESSION['meta_keywords']) ;
 *   3. le chargement du contrôleur correspondant à la route.
 *
 * Repris à l'identique du fonctionnement historique du projet FiaJou3
 * (métadonnées posées directement en session avant le require du
 * contrôleur), avec une seule adaptation volontaire :
 *
 *   -> reset_meta() ne réinitialise QUE les 3 clés SEO, pas toute la
 *      session. Un session_destroy() complet sur chaque route viderait
 *      $_SESSION['user_id'] / $_SESSION['role'] (authentification) et
 *      $_SESSION['panier'] (panier client stocké en session dans
 *      modele/PanierModele.php) à chaque navigation, ce qui déconnecterait
 *      l'utilisateur et viderait son panier en permanence. On garde donc
 *      l'esprit "reset avant d'écrire les nouvelles métadonnées" sans
 *      casser l'authentification ni le panier.
 *
 * Le contrôle d'accès par rôle (exiger_role / exiger_connexion) continue
 * de se faire comme avant, en tête de chaque contrôleur — rien n'est
 * modifié à ce niveau.
 */

/**
 * Réinitialise uniquement les métadonnées SEO de la page en cours,
 * sans toucher au reste de la session (auth, panier, etc.).
 */
function reset_meta(): void
{
    unset($_SESSION['pr_title'], $_SESSION['meta_description'], $_SESSION['meta_keywords']);
}

function dispatch(): void
{
    $route = isset($_GET['route']) ? trim($_GET['route'], '/') : '';

    switch ($route) {

        /* =========================
           RACINE
           Redirige selon la session (voir default.php) - pas de <head>
           rendu directement ici, donc pas de métadonnées nécessaires.
        ========================= */
        case '':
            require ROOT_PATH . '/default.php';
            return;

        /* =========================
           PAGES PUBLIQUES
        ========================= */

        case 'accueil':
            reset_meta();
            $_SESSION['pr_title'] = APP_NAME . ' - Repas faits maison, livrés chez vous';
            $_SESSION['meta_description'] = 'Commandez des repas faits maison préparés par des cuisiniers locaux et faites-vous livrer rapidement avec ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'repas maison, livraison de repas, cuisine locale, commande de repas, ' . APP_NAME;
            require ROOT_PATH . '/controleur/AccueilControleur.php';
            return;

        case 'connexion':
            reset_meta();
            $_SESSION['pr_title'] = 'Connexion - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Connectez-vous à votre compte ' . APP_NAME . ' pour commander vos repas faits maison.';
            $_SESSION['meta_keywords'] = 'connexion, se connecter, compte ' . APP_NAME;
            require ROOT_PATH . '/controleur/auth/LoginControleur.php';
            return;

        case 'inscription':
            reset_meta();
            $_SESSION['pr_title'] = 'Inscription - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Créez votre compte ' . APP_NAME . ' et commencez à commander des repas faits maison livrés chez vous.';
            $_SESSION['meta_keywords'] = 'inscription, créer un compte, ' . APP_NAME;
            require ROOT_PATH . '/controleur/auth/RegisterControleur.php';
            return;

        case 'mot-de-passe-oublie':
            reset_meta();
            $_SESSION['pr_title'] = 'Mot de passe oublié - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Réinitialisez le mot de passe de votre compte ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'mot de passe oublié, réinitialisation, ' . APP_NAME;
            require ROOT_PATH . '/controleur/auth/MotDePasseOublieControleur.php';
            return;

        case 'deconnexion':
            // Action pure (destruction de session + redirection), aucune
            // page n'est rendue : pas de métadonnées SEO à poser ici.
            require ROOT_PATH . '/controleur/auth/LogoutControleur.php';
            return;

        /* =========================
           ESPACE ADMINISTRATEUR
           (protégé par exiger_role(ROLE_ADMIN) dans chaque contrôleur ;
           non indexable, mais titre/description cohérents pour l'onglet)
        ========================= */

        case 'admin':
            reset_meta();
            $_SESSION['pr_title'] = 'Tableau de bord administrateur - ' . APP_NAME;
            $_SESSION['meta_description'] = "Espace d'administration " . APP_NAME . ' : gestion des commandes, utilisateurs, plats et livraisons.';
            $_SESSION['meta_keywords'] = 'administration, tableau de bord, ' . APP_NAME;
            require ROOT_PATH . '/controleur/admin/DashboardControleur.php';
            return;

        case 'admin/categories':
            reset_meta();
            $_SESSION['pr_title'] = 'Gestion des catégories - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Créez, modifiez et organisez les catégories de plats proposées sur ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'catégories, gestion des catégories, administration, ' . APP_NAME;
            require ROOT_PATH . '/controleur/admin/CategorieControleur.php';
            return;

        case 'admin/plats':
            reset_meta();
            $_SESSION['pr_title'] = 'Gestion des plats - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Ajoutez, modifiez et gérez la disponibilité des plats proposés sur ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'gestion des plats, menu, administration, ' . APP_NAME;
            require ROOT_PATH . '/controleur/admin/PlatControleur.php';
            return;

        case 'admin/commandes':
            reset_meta();
            $_SESSION['pr_title'] = 'Gestion des commandes - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Suivez et gérez l\'ensemble des commandes passées sur ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'gestion des commandes, suivi de commande, administration, ' . APP_NAME;
            require ROOT_PATH . '/controleur/admin/CommandeControleur.php';
            return;

        case 'admin/utilisateurs':
            reset_meta();
            $_SESSION['pr_title'] = 'Gestion des utilisateurs - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Gérez les comptes clients, cuisiniers, livreurs et administrateurs de ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'gestion des utilisateurs, comptes, administration, ' . APP_NAME;
            require ROOT_PATH . '/controleur/admin/UtilisateurControleur.php';
            return;

        case 'admin/zones':
            reset_meta();
            $_SESSION['pr_title'] = 'Zones de livraison - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Configurez les zones de livraison couvertes par ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'zones de livraison, administration, ' . APP_NAME;
            require ROOT_PATH . '/controleur/admin/ZoneControleur.php';
            return;

        case 'admin/cuisiniers':
            reset_meta();
            $_SESSION['pr_title'] = 'Gestion des cuisiniers - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Gérez les comptes et l\'activité des cuisiniers partenaires de ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'gestion des cuisiniers, administration, ' . APP_NAME;
            require ROOT_PATH . '/controleur/admin/CuisinierControleur.php';
            return;

        case 'admin/livreurs':
            reset_meta();
            $_SESSION['pr_title'] = 'Gestion des livreurs - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Gérez les comptes et l\'activité des livreurs de ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'gestion des livreurs, administration, ' . APP_NAME;
            require ROOT_PATH . '/controleur/admin/LivreurControleur.php';
            return;

        case 'admin/assignation':
            reset_meta();
            $_SESSION['pr_title'] = 'Assignation des commandes - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Assignez les commandes aux cuisiniers et aux livreurs sur ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'assignation des commandes, administration, ' . APP_NAME;
            require ROOT_PATH . '/controleur/admin/AssignationControleur.php';
            return;

        case 'admin/menu-semaine':
            reset_meta();
            $_SESSION['pr_title'] = 'Menu de la semaine - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Configurez et publiez le menu de la semaine proposé sur ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'menu de la semaine, administration, ' . APP_NAME;
            require ROOT_PATH . '/controleur/admin/MenuSemaineControleur.php';
            return;

        /* =========================
           ESPACE CLIENT
           (protégé par exiger_role(ROLE_CLIENT))
        ========================= */

        case 'client':
            reset_meta();
            $_SESSION['pr_title'] = 'Menu - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Parcourez le menu de plats faits maison disponibles à la commande sur ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'menu, plats faits maison, commander, ' . APP_NAME;
            require ROOT_PATH . '/controleur/client/MenuControleur.php';
            return;

        case 'client/produit':
            // Titre dynamique : la vue (vue/client/produit.php) définit son
            // propre $pageTitle avec le nom du plat une fois celui-ci chargé
            // en base ; ce titre local reste prioritaire (voir header.php).
            reset_meta();
            $_SESSION['pr_title'] = 'Produit - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Découvrez le détail d\'un plat disponible à la commande sur ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'fiche produit, plat, ' . APP_NAME;
            require ROOT_PATH . '/controleur/client/ProduitControleur.php';
            return;

        case 'client/dashboard':
            reset_meta();
            $_SESSION['pr_title'] = 'Tableau de bord - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Retrouvez un aperçu de votre activité et de vos commandes sur ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'tableau de bord client, ' . APP_NAME;
            require ROOT_PATH . '/controleur/client/DashboardControleur.php';
            return;

        case 'client/panier':
            reset_meta();
            $_SESSION['pr_title'] = 'Mon panier - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Consultez et modifiez le contenu de votre panier avant de passer commande sur ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'panier, commande, ' . APP_NAME;
            require ROOT_PATH . '/controleur/client/PanierControleur.php';
            return;

        case 'client/commander':
            reset_meta();
            $_SESSION['pr_title'] = 'Commander - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Validez votre commande et choisissez vos options de livraison sur ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'commander, validation de commande, ' . APP_NAME;
            require ROOT_PATH . '/controleur/client/CommanderControleur.php';
            return;

        case 'client/mes-commandes':
            reset_meta();
            $_SESSION['pr_title'] = 'Mes commandes - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Retrouvez l\'historique et le statut de vos commandes passées sur ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'mes commandes, historique de commande, ' . APP_NAME;
            require ROOT_PATH . '/controleur/client/MesCommandesControleur.php';
            return;

        case 'client/detail-commande':
            // Titre dynamique : vue/client/detail_commande.php définit son
            // propre $pageTitle avec le numéro de commande ; il reste
            // prioritaire sur la valeur générique posée ici (voir header.php).
            reset_meta();
            $_SESSION['pr_title'] = 'Détail de la commande - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Consultez le détail et le statut d\'une commande passée sur ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'détail commande, suivi de commande, ' . APP_NAME;
            require ROOT_PATH . '/controleur/client/DetailCommandeControleur.php';
            return;

        case 'client/profil':
            reset_meta();
            $_SESSION['pr_title'] = 'Mon profil - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Gérez les informations de votre compte ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'profil, mon compte, ' . APP_NAME;
            require ROOT_PATH . '/controleur/client/ProfilControleur.php';
            return;

        case 'client/menu-semaine':
            reset_meta();
            $_SESSION['pr_title'] = 'Menu de la semaine - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Découvrez le menu de la semaine proposé sur ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'menu de la semaine, ' . APP_NAME;
            require ROOT_PATH . '/controleur/client/MenuSemaineControleur.php';
            return;

        case 'client/notifications':
            reset_meta();
            $_SESSION['pr_title'] = 'Notifications - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Consultez vos notifications liées à vos commandes sur ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'notifications, ' . APP_NAME;
            require ROOT_PATH . '/controleur/client/NotificationsControleur.php';
            return;

        /* =========================
           ESPACE CUISINIER
           (protégé par exiger_role(ROLE_CUISINIER))
        ========================= */

        case 'cuisinier':
            reset_meta();
            $_SESSION['pr_title'] = 'Espace cuisinier - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Suivez et préparez les commandes en cours sur ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'espace cuisinier, préparation des commandes, ' . APP_NAME;
            require ROOT_PATH . '/controleur/cuisinier/DashboardControleur.php';
            return;

        case 'cuisinier/historique':
            reset_meta();
            $_SESSION['pr_title'] = 'Historique cuisinier - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Consultez l\'historique des commandes préparées sur ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'historique cuisinier, ' . APP_NAME;
            require ROOT_PATH . '/controleur/cuisinier/HistoriqueControleur.php';
            return;

        /* =========================
           ESPACE LIVREUR
           (protégé par exiger_role(ROLE_LIVREUR))
        ========================= */

        case 'livreur':
            reset_meta();
            $_SESSION['pr_title'] = 'Espace livreur - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Suivez et gérez vos livraisons en cours sur ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'espace livreur, livraisons, ' . APP_NAME;
            require ROOT_PATH . '/controleur/livreur/DashboardControleur.php';
            return;

        case 'livreur/historique':
            reset_meta();
            $_SESSION['pr_title'] = 'Historique livreur - ' . APP_NAME;
            $_SESSION['meta_description'] = 'Consultez l\'historique de vos livraisons effectuées sur ' . APP_NAME . '.';
            $_SESSION['meta_keywords'] = 'historique livreur, ' . APP_NAME;
            require ROOT_PATH . '/controleur/livreur/HistoriqueControleur.php';
            return;

        /* =========================
           404
        ========================= */

        default:
            http_response_code(404);
            reset_meta();
            $_SESSION['pr_title'] = 'Page introuvable - ' . APP_NAME;
            $_SESSION['meta_description'] = "La page demandée n'existe pas ou plus sur " . APP_NAME . '.';
            $_SESSION['meta_keywords'] = '';
            require ROOT_PATH . '/vue/errors/404.php';
            return;
    }
}