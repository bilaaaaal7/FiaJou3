<?php
/**
 * Routeur de l'application (urlRewrite.php)
 *
 * Centralise :
 *   1. les URLs / routes de l'application ;
 *   2. les métadonnées SEO de chaque route, stockées dans $_SESSION
 *      (pr_title, meta_description, meta_keywords, meta_robots,
 *       meta_canonical, og:title, og:description, og:type, og:url,
 *       og:image, twitter:card) ;
 *   3. le chargement du contrôleur correspondant à la route.
 *
 * Repris à l'identique du fonctionnement historique du projet FiaJou3
 * (métadonnées posées directement en session avant le require du
 * contrôleur), avec une seule adaptation volontaire :
 *
 *   -> reset_meta() ne réinitialise QUE les clés SEO, pas toute la
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
    unset(
        $_SESSION['pr_title'], $_SESSION['meta_description'], $_SESSION['meta_keywords'],
        $_SESSION['meta_robots'], $_SESSION['meta_canonical'],
        $_SESSION['og:title'], $_SESSION['og:description'], $_SESSION['og:type'],
        $_SESSION['og:url'], $_SESSION['og:image'], $_SESSION['twitter:card']
    );
}

/**
 * Définit toutes les métadonnées SEO de la page courante en une seule appelée.
 *
 * @param string $title       Titre de la page (balise <title> + og:title)
 * @param string $description Méta description (meta description + og:description)
 * @param string $keywords    Mots-clés (meta keywords), vide si non pertinent
 * @param string $robots      Directive robots ('index, follow' | 'noindex, nofollow')
 * @param string $ogType      Type Open Graph ('website' | 'article' | 'profile')
 * @param string $canonical   URL canonique vide = auto (BASE_URL + route courante)
 */
function set_meta(
    string $title,
    string $description,
    string $keywords,
    string $robots = 'noindex, nofollow',
    string $ogType = 'website',
    string $canonical = ''
): void {
    $currentRoute = $_GET['route'] ?? '';
    $baseUrl      = defined('BASE_URL') ? BASE_URL : '';
    $url          = $canonical !== '' ? $canonical : $baseUrl . '/index.php?route=' . $currentRoute;

    $_SESSION['pr_title']         = $title;
    $_SESSION['meta_description'] = $description;
    $_SESSION['meta_keywords']    = $keywords;
    $_SESSION['meta_robots']      = $robots;
    $_SESSION['meta_canonical']   = $url;
    $_SESSION['og:title']         = $title;
    $_SESSION['og:description']   = $description;
    $_SESSION['og:type']          = $ogType;
    $_SESSION['og:url']           = $url;
    $_SESSION['og:image']         = $baseUrl . '/assets/images/logo.png';
    $_SESSION['twitter:card']     = 'summary_large_image';
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
            set_meta(
                APP_NAME . ' - Repas faits maison, livrés chez vous',
                'Commandez des repas faits maison préparés par des cuisiniers locaux et faites-vous livrer rapidement avec ' . APP_NAME . '.',
                'repas maison, livraison de repas, cuisine locale, commande de repas, ' . APP_NAME,
                'index, follow'
            );
            require ROOT_PATH . '/controleur/AccueilControleur.php';
            return;

        case 'connexion':
            reset_meta();
            set_meta(
                'Connexion - ' . APP_NAME,
                'Connectez-vous à votre compte ' . APP_NAME . ' pour commander vos repas faits maison.',
                'connexion, se connecter, compte ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/auth/LoginControleur.php';
            return;

        case 'inscription':
            reset_meta();
            set_meta(
                'Inscription - ' . APP_NAME,
                'Créez votre compte ' . APP_NAME . ' et commencez à commander des repas faits maison livrés chez vous.',
                'inscription, créer un compte, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/auth/RegisterControleur.php';
            return;

        case 'mot-de-passe-oublie':
            reset_meta();
            set_meta(
                'Mot de passe oublié - ' . APP_NAME,
                'Réinitialisez le mot de passe de votre compte ' . APP_NAME . '.',
                'mot de passe oublié, réinitialisation, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/auth/MotDePasseOublieControleur.php';
            return;

        case 'deconnexion':
            // Action pure (destruction de session + redirection), aucune
            // page n'est rendue : pas de métadonnées SEO à poser ici.
            require ROOT_PATH . '/controleur/auth/LogoutControleur.php';
            return;

        /* =========================
           CONNEXION AVEC GOOGLE (OAuth 2.0 / OpenID Connect)
           auth/google          : démarre le flux (redirection vers Google)
           auth/google/callback : retour de Google, connecte/crée le compte
           auth/google/complete : complète les champs obligatoires manquants
                                   (téléphone) pour un nouveau compte Google
        ========================= */

        case 'auth/google':
            // Action pure (redirection vers Google), aucune page n'est rendue.
            require ROOT_PATH . '/controleur/auth/GoogleControleur.php';
            return;

        case 'auth/google/callback':
            // Action pure (traite le retour de Google puis redirige).
            require ROOT_PATH . '/controleur/auth/GoogleCallbackControleur.php';
            return;

        case 'auth/google/complete':
            reset_meta();
            set_meta(
                "Finaliser l'inscription - " . APP_NAME,
                'Complétez votre inscription ' . APP_NAME . ' après connexion avec Google.',
                'inscription google, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/auth/GoogleCompleteControleur.php';
            return;

        /* =========================
           PARTENARIAT (cuisinier / livreur)
           Deux flows distincts du Register client :
             - partenaire/demande : AJAX, reçoit l'email + le rôle depuis la
               modale "Rejoignez FiaJou3", crée l'invitation et envoie l'email.
             - partenaire : page de complétion du dossier ouverte depuis le
               lien sécurisé reçu par email (GET) et soumission du dossier (POST).
        ========================= */

        case 'partenaire/demande':
            // Point d'action AJAX : aucune page n'est rendue, pas de métadonnées.
            require ROOT_PATH . '/controleur/PartenaireDemandeControleur.php';
            return;

        case 'partenaire':
            reset_meta();
            set_meta(
                'Devenir partenaire - ' . APP_NAME,
                'Complétez votre dossier de candidature pour rejoindre ' . APP_NAME . ' en tant que cuisinier ou livreur partenaire.',
                'devenir partenaire, cuisinier partenaire, livreur partenaire, ' . APP_NAME,
                'noindex, nofollow'
            );
            require ROOT_PATH . '/controleur/PartenaireControleur.php';
            return;

        case 'langue':
            // Point d'action AJAX : persistance de la langue choisie par le
            // sélecteur (session + compte connecté + cookie). Aucune page rendue.
            require ROOT_PATH . '/controleur/LangueControleur.php';
            return;

        /* =========================
           ESPACE PERSONNEL
           (accessible à tout rôle connecté via exiger_connexion)
        ========================= */

        case 'profil':
            reset_meta();
            set_meta(
                'Mon profil - ' . APP_NAME,
                'Consultez les informations de votre compte ' . APP_NAME . '.',
                'profil, mon compte, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/ProfilControleur.php';
            return;

        case 'parametres':
            reset_meta();
            set_meta(
                'Paramètres - ' . APP_NAME,
                'Modifiez vos informations personnelles, votre email et votre mot de passe sur ' . APP_NAME . '.',
                'paramètres, mot de passe, email, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/ParametresControleur.php';
            return;

        /* =========================
           ESPACE ADMINISTRATEUR
           (protégé par exiger_role(ROLE_ADMIN) dans chaque contrôleur ;
           non indexable, mais titre/description cohérents pour l'onglet)
        ========================= */

        case 'admin':
            reset_meta();
            set_meta(
                'Tableau de bord administrateur - ' . APP_NAME,
                "Espace d'administration " . APP_NAME . ' : gestion des commandes, utilisateurs, plats et livraisons.',
                'administration, tableau de bord, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/admin/DashboardControleur.php';
            return;

        case 'admin/categories':
            reset_meta();
            set_meta(
                'Gestion des catégories - ' . APP_NAME,
                'Créez, modifiez et organisez les catégories de plats proposées sur ' . APP_NAME . '.',
                'catégories, gestion des catégories, administration, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/admin/CategorieControleur.php';
            return;

        case 'admin/plats':
            reset_meta();
            set_meta(
                'Gestion des plats - ' . APP_NAME,
                'Ajoutez, modifiez et gérez la disponibilité des plats proposés sur ' . APP_NAME . '.',
                'gestion des plats, menu, administration, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/admin/PlatControleur.php';
            return;

        case 'admin/commandes':
            reset_meta();
            set_meta(
                'Gestion des commandes - ' . APP_NAME,
                'Suivez et gérez l\'ensemble des commandes passées sur ' . APP_NAME . '.',
                'gestion des commandes, suivi de commande, administration, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/admin/CommandeControleur.php';
            return;

        case 'admin/utilisateurs':
            reset_meta();
            set_meta(
                'Gestion des utilisateurs - ' . APP_NAME,
                'Gérez les comptes clients, cuisiniers, livreurs et administrateurs de ' . APP_NAME . '.',
                'gestion des utilisateurs, comptes, administration, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/admin/UtilisateurControleur.php';
            return;

        case 'admin/zones':
            reset_meta();
            set_meta(
                'Zones de livraison - ' . APP_NAME,
                'Configurez les zones de livraison couvertes par ' . APP_NAME . '.',
                'zones de livraison, administration, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/admin/ZoneControleur.php';
            return;

        case 'admin/cuisiniers':
            reset_meta();
            set_meta(
                'Gestion des cuisiniers - ' . APP_NAME,
                'Gérez les comptes et l\'activité des cuisiniers partenaires de ' . APP_NAME . '.',
                'gestion des cuisiniers, administration, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/admin/CuisinierControleur.php';
            return;

        case 'admin/livreurs':
            reset_meta();
            set_meta(
                'Gestion des livreurs - ' . APP_NAME,
                'Gérez les comptes et l\'activité des livreurs de ' . APP_NAME . '.',
                'gestion des livreurs, administration, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/admin/LivreurControleur.php';
            return;

        case 'admin/assignation':
            reset_meta();
            set_meta(
                'Assignation des commandes - ' . APP_NAME,
                'Assignez les commandes aux cuisiniers et aux livreurs sur ' . APP_NAME . '.',
                'assignation des commandes, administration, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/admin/AssignationControleur.php';
            return;

        case 'admin/menu-semaine':
            reset_meta();
            set_meta(
                'Menu de la semaine (admin) - ' . APP_NAME,
                'Configurez et publiez le menu de la semaine proposé sur ' . APP_NAME . '.',
                'menu de la semaine, administration, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/admin/MenuSemaineControleur.php';
            return;

        /* =========================
           ESPACE CLIENT
           (protégé par exiger_role(ROLE_CLIENT))
        ========================= */

        case 'client':
            reset_meta();
            set_meta(
                'Menu - ' . APP_NAME,
                'Parcourez le menu de plats faits maison disponibles à la commande sur ' . APP_NAME . '.',
                'menu, plats faits maison, commander, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/client/MenuControleur.php';
            return;

        case 'client/produit':
            // Titre dynamique : la vue (vue/client/produit.php) définit son
            // propre $pageTitle avec le nom du plat une fois celui-ci chargé
            // en base ; ce titre local reste prioritaire (voir header.php).
            reset_meta();
            set_meta(
                'Produit - ' . APP_NAME,
                'Découvrez le détail d\'un plat disponible à la commande sur ' . APP_NAME . '.',
                'fiche produit, plat, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/client/ProduitControleur.php';
            return;

        case 'client/panier':
            reset_meta();
            set_meta(
                'Mon panier - ' . APP_NAME,
                'Consultez et modifiez le contenu de votre panier avant de passer commande sur ' . APP_NAME . '.',
                'panier, commande, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/client/PanierControleur.php';
            return;

        case 'client/commander':
            reset_meta();
            set_meta(
                'Commander - ' . APP_NAME,
                'Validez votre commande et choisissez vos options de livraison sur ' . APP_NAME . '.',
                'commander, validation de commande, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/client/CommanderControleur.php';
            return;

        case 'client/mes-commandes':
            reset_meta();
            set_meta(
                'Mes commandes - ' . APP_NAME,
                'Retrouvez l\'historique et le statut de vos commandes passées sur ' . APP_NAME . '.',
                'mes commandes, historique de commande, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/client/MesCommandesControleur.php';
            return;

        case 'client/detail-commande':
            // Titre dynamique : vue/client/detail_commande.php définit son
            // propre $pageTitle avec le numéro de commande ; il reste
            // prioritaire sur la valeur générique posée ici (voir header.php).
            reset_meta();
            set_meta(
                'Détail de la commande - ' . APP_NAME,
                'Consultez le détail et le statut d\'une commande passée sur ' . APP_NAME . '.',
                'détail commande, suivi de commande, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/client/DetailCommandeControleur.php';
            return;

        case 'client/profil':
            reset_meta();
            set_meta(
                'Mon profil - ' . APP_NAME,
                'Gérez les informations de votre compte ' . APP_NAME . '.',
                'profil, mon compte, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/client/ProfilControleur.php';
            return;

        case 'client/menu-semaine':
            reset_meta();
            set_meta(
                'Menu de la semaine - ' . APP_NAME,
                'Découvrez le menu de la semaine proposé sur ' . APP_NAME . '.',
                'menu de la semaine, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/client/MenuSemaineControleur.php';
            return;

        case 'client/notifications':
            reset_meta();
            set_meta(
                'Notifications - ' . APP_NAME,
                'Consultez vos notifications liées à vos commandes sur ' . APP_NAME . '.',
                'notifications, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/client/NotificationsControleur.php';
            return;

        case 'client/abonnement':
            reset_meta();
            set_meta(
                'Abonnement mensuel - ' . APP_NAME,
                'Souscrivez à un abonnement mensuel ' . APP_NAME . ' pour bénéficier d\'avantages exclusifs.',
                'abonnement, souscription, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/client/AbonnementControleur.php';
            return;

        /* =========================
           ESPACE CUISINIER
           (protégé par exiger_role(ROLE_CUISINIER))
        ========================= */

        case 'cuisinier':
            reset_meta();
            set_meta(
                'Espace cuisinier - ' . APP_NAME,
                'Suivez et préparez les commandes en cours sur ' . APP_NAME . '.',
                'espace cuisinier, préparation des commandes, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/cuisinier/DashboardControleur.php';
            return;

        case 'cuisinier/commande':
            reset_meta();
            set_meta(
                'Commande cuisinier - ' . APP_NAME,
                'Consultez une commande et faites évoluer son statut de préparation sur ' . APP_NAME . '.',
                'commande, cuisinier, préparation, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/cuisinier/CommandeControleur.php';
            return;

        case 'cuisinier/historique':
            reset_meta();
            set_meta(
                'Historique cuisinier - ' . APP_NAME,
                'Consultez l\'historique des commandes préparées sur ' . APP_NAME . '.',
                'historique cuisinier, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/cuisinier/HistoriqueControleur.php';
            return;

        case 'cuisinier/detail-commande':
            reset_meta();
            set_meta(
                'Détail de la commande - ' . APP_NAME,
                'Consultez le détail et la chronologie d\'une commande à préparer sur ' . APP_NAME . '.',
                'détail commande cuisinier, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/cuisinier/DetailCommandeControleur.php';
            return;

        /* =========================
           ESPACE LIVREUR
           (protégé par exiger_role(ROLE_LIVREUR))
        ========================= */

        case 'livreur':
            reset_meta();
            set_meta(
                'Espace livreur - ' . APP_NAME,
                'Suivez et gérez vos livraisons en cours sur ' . APP_NAME . '.',
                'espace livreur, livraisons, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/livreur/DashboardControleur.php';
            return;

        case 'livreur/commande':
            reset_meta();
            set_meta(
                'Livraison livreur - ' . APP_NAME,
                'Consultez une livraison et confirmez sa remise sur ' . APP_NAME . '.',
                'livraison, livreur, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/livreur/CommandeControleur.php';
            return;

        case 'livreur/historique':
            reset_meta();
            set_meta(
                'Historique livreur - ' . APP_NAME,
                'Consultez l\'historique de vos livraisons effectuées sur ' . APP_NAME . '.',
                'historique livreur, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/livreur/HistoriqueControleur.php';
            return;

        case 'livreur/detail-commande':
            reset_meta();
            set_meta(
                'Détail de la commande - ' . APP_NAME,
                'Consultez le détail et la chronologie d\'une livraison sur ' . APP_NAME . '.',
                'détail commande livreur, ' . APP_NAME
            );
            require ROOT_PATH . '/controleur/livreur/DetailCommandeControleur.php';
            return;

        /* =========================
           404
        ========================= */

        default:
            http_response_code(404);
            reset_meta();
            set_meta(
                'Page introuvable - ' . APP_NAME,
                "La page demandée n'existe pas ou plus sur " . APP_NAME . '.',
                ''
            );
            require ROOT_PATH . '/vue/errors/404.php';
            return;
    }
}
