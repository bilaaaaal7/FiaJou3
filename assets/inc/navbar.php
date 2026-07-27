<?php
/**
 * Sidebar de navigation commune, adaptée selon le rôle en session.
 * Utilise le système de layout .app-shell / .sidebar / .main déjà défini
 * dans assets/css/admin.css (existant avant cette réorganisation, incluant
 * son propre responsive off-canvas via .menu-toggle / .sidebar.open).
 * Ouvre .app-shell et .main ; la fermeture correspondante se fait dans
 * assets/inc/footer.php.
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
    echo '<span>' . $icone . '</span><span>' . htmlspecialchars($label);
    if ($badge) {
        echo ' <span class="badge-status st-annulee">' . htmlspecialchars((string) $badge) . '</span>';
    }
    echo '</span></a>';
}
?>
<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <span style="font-size:1.4rem;">🍽️</span>
            <span><?php echo APP_NAME; ?></span>
        </div>

        <span class="role-badge"><?php echo htmlspecialchars($role ?? ''); ?> — <?php echo htmlspecialchars($_SESSION['prenom'] ?? ''); ?></span>

        <nav>
            <?php if ($role === ROLE_ADMIN): ?>
                <?php sidebar_lien('admin', '📊', 'Tableau de bord', $routeActuelle); ?>
                <?php sidebar_lien('admin/plats', '🍽️', 'Produits', $routeActuelle); ?>
                <?php sidebar_lien('admin/categories', '🏷️', 'Catégories', $routeActuelle); ?>
                <?php sidebar_lien('admin/commandes', '📦', 'Commandes', $routeActuelle); ?>
                <?php sidebar_lien('admin/assignation', '🚚', 'Affectations', $routeActuelle); ?>
                <?php sidebar_lien('admin/menu-semaine', '📅', 'Menu de la semaine', $routeActuelle); ?>
                <?php sidebar_lien('admin/utilisateurs', '👥', 'Clients', $routeActuelle); ?>
                <?php sidebar_lien('admin/cuisiniers', '👨‍🍳', 'Cuisiniers', $routeActuelle); ?>
                <?php sidebar_lien('admin/livreurs', '🛵', 'Livreurs', $routeActuelle); ?>
                <?php sidebar_lien('admin/zones', '📍', 'Zones de livraison', $routeActuelle); ?>
            <?php elseif ($role === ROLE_CLIENT): ?>
                <?php sidebar_lien('client/dashboard', '📊', 'Tableau de bord', $routeActuelle); ?>
                <?php sidebar_lien('client', '🍽️', 'Menu', $routeActuelle); ?>
                <?php sidebar_lien('client/menu-semaine', '📅', 'Menu de la semaine', $routeActuelle); ?>
                <?php sidebar_lien('client/panier', '🛒', 'Panier (' . $panierModele->nombreArticles() . ')', $routeActuelle); ?>
                <?php sidebar_lien('client/mes-commandes', '📦', 'Mes commandes', $routeActuelle); ?>
                <?php sidebar_lien('client/profil', '👤', 'Profil', $routeActuelle); ?>
            <?php elseif ($role === ROLE_CUISINIER): ?>
                <?php sidebar_lien('cuisinier', '👨‍🍳', 'Commandes à préparer', $routeActuelle); ?>
                <?php sidebar_lien('cuisinier/historique', '🕒', 'Historique', $routeActuelle); ?>
            <?php elseif ($role === ROLE_LIVREUR): ?>
                <?php sidebar_lien('livreur', '🛵', 'Livraisons du jour', $routeActuelle); ?>
                <?php sidebar_lien('livreur/historique', '🕒', 'Historique', $routeActuelle); ?>
            <?php endif; ?>

            <?php sidebar_lien('client/notifications', '🔔', 'Notifications', $routeActuelle, $nbNotifs > 0 ? ($nbNotifs > 9 ? '9+' : $nbNotifs) : null); ?>
        </nav>

        <div class="logout-link">
            <a href="<?php echo BASE_URL; ?>/index.php?route=deconnexion">🚪 Déconnexion</a>
        </div>
    </aside>

    <div class="main">
        <button type="button" class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">&#9776;</button>
