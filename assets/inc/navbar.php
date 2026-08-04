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

function sidebar_lien(string $route, string $icone, string $label, string $routeActuelle, $badge = null): void
{
    $actif = $routeActuelle === $route ? ' class="active"' : '';
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
        <div class="brand">
            <span class="logo-mark" style="width:34px;height:34px;flex-shrink:0;"><?php $logoSurFondSombre = true; include ROOT_PATH . '/assets/inc/logo.php'; ?></span>
            <span><?php echo APP_NAME; ?></span>
        </div>

        <nav>
            <?php if ($role === ROLE_ADMIN): ?>
                <?php sidebar_lien('admin', 'layout-dashboard', 'Tableau de bord', $routeActuelle); ?>
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
                <?php sidebar_lien('client/dashboard', 'layout-dashboard', 'Tableau de bord', $routeActuelle); ?>
                <?php sidebar_lien('client', 'utensils-crossed', 'Menu', $routeActuelle); ?>
                <?php sidebar_lien('client/menu-semaine', 'calendar-days', 'Menu de la semaine', $routeActuelle); ?>
                <?php sidebar_lien('client/panier', 'shopping-cart', 'Panier (' . $panierModele->nombreArticles() . ')', $routeActuelle); ?>
                <?php sidebar_lien('client/mes-commandes', 'package', 'Mes commandes', $routeActuelle); ?>
                <?php sidebar_lien('client/profil', 'user', 'Profil', $routeActuelle); ?>
            <?php elseif ($role === ROLE_CUISINIER): ?>
                <?php sidebar_lien('cuisinier', 'cooking-pot', 'Commandes à préparer', $routeActuelle); ?>
                <?php sidebar_lien('cuisinier/historique', 'history', 'Historique', $routeActuelle); ?>
            <?php elseif ($role === ROLE_LIVREUR): ?>
                <?php sidebar_lien('livreur', 'truck', 'Livraisons du jour', $routeActuelle); ?>
                <?php sidebar_lien('livreur/historique', 'history', 'Historique', $routeActuelle); ?>
            <?php endif; ?>

            <?php sidebar_lien('client/notifications', 'bell', 'Notifications', $routeActuelle, $nbNotifs > 0 ? ($nbNotifs > 9 ? '9+' : $nbNotifs) : null); ?>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-profile">
                <span class="avatar"><?php echo htmlspecialchars($initial); ?></span>
                <div class="profile-info">
                    <strong><?php echo htmlspecialchars($prenom); ?></strong>
                    <span><?php echo htmlspecialchars($roleLabel); ?></span>
                </div>
            </div>
            <a class="logout-link" href="<?php echo BASE_URL; ?>/index.php?route=deconnexion">
                <i data-lucide="log-out" aria-hidden="true"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </aside>

    <div class="main">
        <button type="button" class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">&#9776;</button>
