<?php
/**
 * Sidebar de navigation commune, adaptée selon le rôle en session.
 * Icônes Lucide (via data-lucide) — les <i> sont remplacés par des SVG
 * au chargement (lucide.createIcons() dans assets/inc/footer.php).
 */

if (!est_connecte()) {
    return;
}

$role = utilisateur_role();
$panierModele = null;
$nbNotifs = 0;
if ($role === ROLE_CLIENT) {
    require_once ROOT_PATH . '/modele/PanierModele.php';
    $panierModele = new PanierModele();
}
if (est_connecte()) {
    require_once ROOT_PATH . '/modele/NotificationModele.php';
    $notifModele = new NotificationModele();
    $nbNotifs = $notifModele->compterNonLues((int) $_SESSION['user_id']);
}

$routeActuelle = $_GET['route'] ?? '';

// Route « Tableau de bord » propre à chaque rôle : c'est la carte dédiée
// placée en haut de la sidebar (indépendante des autres menus).
$routesDashboard = [
    ROLE_ADMIN     => 'admin',
    ROLE_CLIENT    => '',
    ROLE_CUISINIER => 'cuisinier',
    ROLE_LIVREUR   => 'livreur',
];
$dashboardRoute = $routesDashboard[$role] ?? '';
$dashboardActif = $routeActuelle === $dashboardRoute;

// Pages de détail qui dépendent d'un menu parent (mise en évidence).
$parentParDetail = [
    'client/detail-commande' => 'client/mes-commandes',
    'client/commande'        => 'client/mes-commandes',
];

function sidebar_lien(string $route, string $icone, string $label, string $routeActuelle, $badge = null, array $parents = [], string $cleI18n = ''): void
{
    $parent = $parents[$routeActuelle] ?? null;
    $actif = ($routeActuelle === $route || $parent === $route) ? ' class="active"' : '';
    echo '<a' . $actif . ' href="' . BASE_URL . '/index.php?route=' . htmlspecialchars($route) . '">';
    echo '<i data-lucide="' . htmlspecialchars($icone) . '" aria-hidden="true"></i>';
    echo '<span' . ($cleI18n !== '' ? ' data-i18n="' . htmlspecialchars($cleI18n) . '"' : '') . '>' . htmlspecialchars($label);
    if ($badge) {
        echo ' <span class="nav-badge">' . htmlspecialchars((string) $badge) . '</span>';
    }
    echo '</span></a>';
}

$roleLabels = [
    ROLE_ADMIN     => 'Administrateur',
    ROLE_CLIENT    => 'Client',
    ROLE_CUISINIER => 'Cuisinier',
    ROLE_LIVREUR   => 'Livreur',
];
$roleI18n = [
    ROLE_ADMIN     => 'nav.roleAdmin',
    ROLE_CLIENT    => 'nav.roleClient',
    ROLE_CUISINIER => 'nav.roleCuisinier',
    ROLE_LIVREUR   => 'nav.roleLivreur',
];
$prenomNavbar = trim((string) ($_SESSION['prenom'] ?? ''));
$initial = $prenomNavbar !== '' ? mb_strtoupper(mb_substr($prenomNavbar, 0, 1)) : '?';
$roleLabel = $roleLabels[$role] ?? $role;
$roleCleI18n = $roleI18n[$role] ?? '';
?>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar(false)" aria-hidden="true"></div>
<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-head">
            <div class="brand">
                <span class="logo-mark" style="width:34px;height:34px;flex-shrink:0;"><?php include ROOT_PATH . '/assets/inc/logo.php'; ?></span>
                <span><?php echo APP_NAME; ?></span>
            </div>
            <?php require ROOT_PATH . '/assets/inc/theme_toggle.php'; ?>
        </div>

        <?php if ($dashboardRoute !== ''): ?>
        <a class="dashboard-card<?php echo $dashboardActif ? ' active' : ''; ?>"
           href="<?php echo BASE_URL; ?>/index.php?route=<?php echo htmlspecialchars($dashboardRoute); ?>"
           aria-current="<?php echo $dashboardActif ? 'page' : 'false'; ?>">
            <span class="dc-icon"><i data-lucide="home" aria-hidden="true"></i></span>
            <span class="dc-label" data-i18n="nav.tableauBord">Tableau de bord</span>
            <span class="dc-arrow"><i data-lucide="chevron-right" aria-hidden="true"></i></span>
        </a>
        <?php endif; ?>

        <nav>
            <?php if ($role === ROLE_ADMIN): ?>
                <?php sidebar_lien('admin/plats', 'utensils', 'Produits', $routeActuelle, null, [], 'nav.produits'); ?>
                <?php sidebar_lien('admin/categories', 'tags', 'Catégories', $routeActuelle, null, [], 'nav.categories'); ?>
                <?php sidebar_lien('admin/commandes', 'shopping-bag', 'Commandes', $routeActuelle, null, [], 'nav.commandes'); ?>
                <?php sidebar_lien('admin/menu-semaine', 'calendar-days', 'Menu de la semaine', $routeActuelle, null, [], 'nav.menuSemaine'); ?>
                <?php sidebar_lien('admin/utilisateurs', 'users', 'Clients', $routeActuelle, null, [], 'nav.clients'); ?>
                <?php sidebar_lien('admin/cuisiniers', 'chef-hat', 'Cuisiniers', $routeActuelle, null, [], 'nav.cuisiniers'); ?>
                <?php sidebar_lien('admin/livreurs', 'bike', 'Livreurs', $routeActuelle, null, [], 'nav.livreurs'); ?>
                <?php sidebar_lien('admin/zones', 'map-pin', 'Zones de livraison', $routeActuelle, null, [], 'nav.zones'); ?>
            <?php elseif ($role === ROLE_CLIENT): ?>
                <?php sidebar_lien('accueil', 'home', 'Accueil', $routeActuelle, null, [], 'nav.accueil'); ?>
                <?php sidebar_lien('client', 'utensils-crossed', 'Menu', $routeActuelle, null, [], 'nav.menu'); ?>
                <?php sidebar_lien('client/menu-semaine', 'calendar-days', 'Menu de la semaine', $routeActuelle, null, [], 'nav.menuSemaine'); ?>
                <?php sidebar_lien('client/mes-commandes', 'package', 'Mes commandes', $routeActuelle, null, $parentParDetail, 'nav.mesCommandes'); ?>
                <?php sidebar_lien('client/profil', 'user', 'Profil', $routeActuelle, null, [], 'nav.profil'); ?>
            <?php elseif ($role === ROLE_CUISINIER): ?>
                <?php sidebar_lien('cuisinier/historique', 'history', 'Historique', $routeActuelle, null, [], 'nav.historique'); ?>
            <?php elseif ($role === ROLE_LIVREUR): ?>
                <?php sidebar_lien('livreur/historique', 'history', 'Historique', $routeActuelle, null, [], 'nav.historique'); ?>
            <?php endif; ?>
        </nav>
    </aside>

    <div class="main">
        <?php
        // Titre affiché dans l'en-tête : $pageHeading si la vue le définit.
        // Les vues qui possèdent leur propre titre (topbar, page client)
        // ne définissent pas $pageHeading pour éviter toute duplication.
        $pageHeading = (string) ($pageHeading ?? '');
        ?>
        <div class="topheader">
            <div class="topheader-left">
                <button type="button" class="menu-toggle" onclick="toggleSidebar()" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="sidebar" data-i18n-aria="nav.ouvrirMenu">&#9776;</button>
                <?php if ($pageHeading !== ''): ?>
                    <h1 class="topheader-title"><?php echo htmlspecialchars($pageHeading); ?></h1>
                <?php endif; ?>
            </div>

            <div class="topheader-actions">
                <?php $langSwitcherCompact = true; require ROOT_PATH . '/assets/inc/lang_switcher.php'; unset($langSwitcherCompact); ?>
                <a class="topheader-notif" href="<?php echo BASE_URL; ?>/index.php?route=client/notifications" aria-label="Notifications" data-i18n-aria="nav.notifications">
                    <i data-lucide="bell" aria-hidden="true"></i>
                    <?php if ($nbNotifs > 0): ?>
                        <span class="topheader-notif-badge"><?php echo $nbNotifs > 9 ? '9+' : $nbNotifs; ?></span>
                    <?php endif; ?>
                </a>

                <?php if ($role === ROLE_CLIENT): ?>
                <button type="button" class="topheader-notif" onclick="fjCartOuvrir()"
                        aria-label="Ouvrir mon panier" title="Mon panier" data-i18n-aria="nav.ouvrirMonPanier">
                    <i data-lucide="shopping-cart" aria-hidden="true"></i>
                    <span class="topheader-notif-badge" data-fj-cart-badge<?php echo $panierModele->nombreArticles() > 0 ? '' : ' hidden'; ?>><?php echo $panierModele->nombreArticles() > 9 ? '9+' : $panierModele->nombreArticles(); ?></span>
                </button>
                <?php endif; ?>

                <div class="topheader-profile" data-profile-menu>
                    <button type="button" class="topheader-profile-trigger" data-profile-trigger aria-haspopup="true" aria-expanded="false">
                        <span class="avatar"><?php echo htmlspecialchars($initial); ?></span>
                        <span class="topheader-profile-name"><?php echo htmlspecialchars($prenomNavbar); ?></span>
                        <i data-lucide="chevron-down" class="topheader-profile-caret" aria-hidden="true"></i>
                    </button>

                    <div class="topheader-profile-dropdown" data-profile-dropdown role="menu">
                        <div class="topheader-profile-dropdown-head">
                            <span class="avatar avatar-lg"><?php echo htmlspecialchars($initial); ?></span>
                            <div class="topheader-profile-dropdown-identity">
                                <strong><?php echo htmlspecialchars($prenomNavbar); ?></strong>
                                <span<?php echo $roleCleI18n !== '' ? ' data-i18n="' . htmlspecialchars($roleCleI18n) . '"' : ''; ?>><?php echo htmlspecialchars($roleLabel); ?></span>
                            </div>
                        </div>
                        <div class="topheader-profile-dropdown-divider"></div>
                        <a role="menuitem" class="topheader-profile-dropdown-item" href="<?php echo BASE_URL; ?>/index.php?route=<?php echo $role === ROLE_CLIENT ? 'client/profil' : 'profil'; ?>">
                            <i data-lucide="user" aria-hidden="true"></i>
                            <span data-i18n="nav.monProfil">Mon profil</span>
                        </a>
                        <?php if ($role === ROLE_CLIENT): ?>
                        <a role="menuitem" class="topheader-profile-dropdown-item" href="<?php echo BASE_URL; ?>/index.php?route=client/mes-commandes">
                            <i data-lucide="package" aria-hidden="true"></i>
                            <span data-i18n="nav.mesCommandes">Mes commandes</span>
                        </a>
                        <?php endif; ?>
                        <a role="menuitem" class="topheader-profile-dropdown-item" href="<?php echo BASE_URL; ?>/index.php?route=parametres">
                            <i data-lucide="settings" aria-hidden="true"></i>
                            <span data-i18n="nav.parametres">Paramètres</span>
                        </a>
                        <div class="topheader-profile-dropdown-divider"></div>
                        <a role="menuitem" class="topheader-profile-dropdown-logout" href="<?php echo BASE_URL; ?>/index.php?route=deconnexion">
                            <i data-lucide="log-out" aria-hidden="true"></i>
                            <span data-i18n="nav.deconnexion">Déconnexion</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
