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
    ROLE_CLIENT    => 'client/dashboard',
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

function sidebar_lien(string $route, string $icone, string $label, string $routeActuelle, $badge = null, array $parents = []): void
{
    $parent = $parents[$routeActuelle] ?? null;
    $actif = ($routeActuelle === $route || $parent === $route) ? ' class="active"' : '';
    echo '<a' . $actif . ' href="' . BASE_URL . '/index.php?route=' . htmlspecialchars($route) . '">';
    echo '<i data-lucide="' . htmlspecialchars($icone) . '" aria-hidden="true"></i>';
    echo '<span>' . htmlspecialchars($label);
    if ($badge) {
        echo ' <span class="badge-status st-annulee">' . htmlspecialchars((string) $badge) . '</span>';
    }
    echo '</span></a>';
}

$roleLabels = [
    ROLE_ADMIN     => 'Administrateur',
    ROLE_CLIENT    => 'Client',
    ROLE_CUISINIER => 'Cuisinier',
    ROLE_LIVREUR   => 'Livreur',
];
$prenom = trim((string) ($_SESSION['prenom'] ?? ''));
$initial = $prenom !== '' ? mb_strtoupper(mb_substr($prenom, 0, 1)) : '?';
$roleLabel = $roleLabels[$role] ?? $role;
?>
<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-head">
            <div class="brand">
                <span class="logo-mark" style="width:34px;height:34px;flex-shrink:0;"><?php include ROOT_PATH . '/assets/inc/logo.php'; ?></span>
                <span><?php echo APP_NAME; ?></span>
            </div>
            <?php require ROOT_PATH . '/assets/inc/theme_toggle.php'; ?>
        </div>

        <a class="dashboard-card<?php echo $dashboardActif ? ' active' : ''; ?>"
           href="<?php echo BASE_URL; ?>/index.php?route=<?php echo htmlspecialchars($dashboardRoute); ?>"
           aria-current="<?php echo $dashboardActif ? 'page' : 'false'; ?>">
            <span class="dc-icon"><i data-lucide="home" aria-hidden="true"></i></span>
            <span class="dc-label">Tableau de bord</span>
            <span class="dc-arrow"><i data-lucide="chevron-right" aria-hidden="true"></i></span>
        </a>

        <nav>
            <?php if ($role === ROLE_ADMIN): ?>
                <?php sidebar_lien('admin/plats', 'utensils', 'Produits', $routeActuelle); ?>
                <?php sidebar_lien('admin/categories', 'tags', 'Catégories', $routeActuelle); ?>
                <?php sidebar_lien('admin/commandes', 'shopping-bag', 'Commandes', $routeActuelle); ?>
                <?php sidebar_lien('admin/assignation', 'arrow-right-left', 'Affectations', $routeActuelle); ?>
                <?php sidebar_lien('admin/menu-semaine', 'calendar-days', 'Menu de la semaine', $routeActuelle); ?>
                <?php sidebar_lien('admin/utilisateurs', 'users', 'Clients', $routeActuelle); ?>
                <?php sidebar_lien('admin/cuisiniers', 'chef-hat', 'Cuisiniers', $routeActuelle); ?>
                <?php sidebar_lien('admin/livreurs', 'bike', 'Livreurs', $routeActuelle); ?>
                <?php sidebar_lien('admin/zones', 'map-pin', 'Zones de livraison', $routeActuelle); ?>
            <?php elseif ($role === ROLE_CLIENT): ?>
                <?php sidebar_lien('client', 'utensils-crossed', 'Menu', $routeActuelle); ?>
                <?php sidebar_lien('client/menu-semaine', 'calendar-days', 'Menu de la semaine', $routeActuelle); ?>
                <?php sidebar_lien('client/panier', 'shopping-cart', 'Panier (' . $panierModele->nombreArticles() . ')', $routeActuelle); ?>
                <?php sidebar_lien('client/mes-commandes', 'package', 'Mes commandes', $routeActuelle, null, $parentParDetail); ?>
                <?php sidebar_lien('client/profil', 'user', 'Profil', $routeActuelle); ?>
            <?php elseif ($role === ROLE_CUISINIER): ?>
                <?php sidebar_lien('cuisinier/historique', 'history', 'Historique', $routeActuelle); ?>
            <?php elseif ($role === ROLE_LIVREUR): ?>
                <?php sidebar_lien('livreur/historique', 'history', 'Historique', $routeActuelle); ?>
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
                <button type="button" class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')" aria-label="Ouvrir le menu">&#9776;</button>
                <?php if ($pageHeading !== ''): ?>
                    <h1 class="topheader-title"><?php echo htmlspecialchars($pageHeading); ?></h1>
                <?php endif; ?>
            </div>

            <div class="topheader-actions">
                <a class="topheader-notif" href="<?php echo BASE_URL; ?>/index.php?route=client/notifications" aria-label="Notifications">
                    <i data-lucide="bell" aria-hidden="true"></i>
                    <?php if ($nbNotifs > 0): ?>
                        <span class="topheader-notif-badge"><?php echo $nbNotifs > 9 ? '9+' : $nbNotifs; ?></span>
                    <?php endif; ?>
                </a>

                <div class="topheader-profile" data-profile-menu>
                    <button type="button" class="topheader-profile-trigger" data-profile-trigger aria-haspopup="true" aria-expanded="false">
                        <span class="avatar"><?php echo htmlspecialchars($initial); ?></span>
                        <span class="topheader-profile-name"><?php echo htmlspecialchars($prenom); ?></span>
                        <i data-lucide="chevron-down" class="topheader-profile-caret" aria-hidden="true"></i>
                    </button>

                    <div class="topheader-profile-dropdown" data-profile-dropdown role="menu">
                        <div class="topheader-profile-dropdown-head">
                            <span class="avatar avatar-lg"><?php echo htmlspecialchars($initial); ?></span>
                            <div class="topheader-profile-dropdown-identity">
                                <strong><?php echo htmlspecialchars($prenom); ?></strong>
                                <span><?php echo htmlspecialchars($roleLabel); ?></span>
                            </div>
                        </div>
                        <div class="topheader-profile-dropdown-divider"></div>
                        <a role="menuitem" class="topheader-profile-dropdown-item" href="<?php echo BASE_URL; ?>/index.php?route=<?php echo $role === ROLE_CLIENT ? 'client/profil' : 'profil'; ?>">
                            <i data-lucide="user" aria-hidden="true"></i>
                            <span>Mon profil</span>
                        </a>
                        <a role="menuitem" class="topheader-profile-dropdown-item" href="<?php echo BASE_URL; ?>/index.php?route=parametres">
                            <i data-lucide="settings" aria-hidden="true"></i>
                            <span>Paramètres</span>
                        </a>
                        <div class="topheader-profile-dropdown-divider"></div>
                        <a role="menuitem" class="topheader-profile-dropdown-logout" href="<?php echo BASE_URL; ?>/index.php?route=deconnexion">
                            <i data-lucide="log-out" aria-hidden="true"></i>
                            <span>Déconnexion</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
