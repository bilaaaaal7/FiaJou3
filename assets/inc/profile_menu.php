<?php
/**
 * Menu utilisateur dans la navbar : avatar + nom + chevron, capsule dark.
 * Clic → dropdown (profil, commandes, notifs, paramètres, déconnexion).
 * Composant autonome (CSS dans profile-menu.css, JS dans profile-menu.js).
 *
 * Variable optionnelle avant l'include :
 *   $profileMenuVariant : 'light' (navbar publique sombre)
 *                         | 'dark' (app-shell)
 */

if (!est_connecte() || utilisateur_role() !== ROLE_CLIENT) {
    return;
}

$profileMenuVariant = $profileMenuVariant ?? 'dark';
$prenomUser = $_SESSION['prenom'] ?? '';
$nomUser    = $_SESSION['nom'] ?? '';
$initiales = strtoupper(mb_substr($prenomUser, 0, 1));
if ($initiales === '') {
    $initiales = '?';
}

$nbNotifsNonLues = 0;
if (!empty($_SESSION['user_id'])) {
    require_once ROOT_PATH . '/modele/NotificationModele.php';
    $nbNotifsNonLues = (new NotificationModele())->compterNonLues((int) $_SESSION['user_id']);
}
?>
<div class="user-menu">
    <button type="button" class="user-menu-trigger">
        <span class="user-avatar"><?php echo htmlspecialchars($initiales); ?></span>
        <span class="user-name"><?php echo htmlspecialchars(trim($prenomUser)); ?></span>
        <span class="user-chevron"><i class="fa fa-chevron-down" aria-hidden="true"></i></span>
    </button>

    <div class="user-dropdown">
        <a href="<?php echo BASE_URL; ?>/index.php?route=client/profil">
            <i class="fa fa-user" aria-hidden="true"></i> <span data-i18n="nav.monProfil">Mon Profil</span>
        </a>
        <a href="<?php echo BASE_URL; ?>/index.php?route=client/mes-commandes">
            <i class="fa fa-shopping-bag" aria-hidden="true"></i> <span data-i18n="nav.mesCommandes">Mes Commandes</span>
        </a>
        <a href="<?php echo BASE_URL; ?>/index.php?route=client/notifications">
            <i class="fa fa-bell" aria-hidden="true"></i> <span data-i18n="nav.notifications">Notifications</span>
            <?php if ($nbNotifsNonLues > 0): ?>
                <span class="user-menu-badge"><?php echo $nbNotifsNonLues > 9 ? '9+' : $nbNotifsNonLues; ?></span>
            <?php endif; ?>
        </a>
        <a href="<?php echo BASE_URL; ?>/index.php?route=parametres">
            <i class="fa fa-cog" aria-hidden="true"></i> <span data-i18n="nav.parametres">Paramètres</span>
        </a>
        <div class="user-menu-divider"></div>
        <a href="<?php echo BASE_URL; ?>/index.php?route=deconnexion" class="user-menu-logout">
            <i class="fa fa-sign-out" aria-hidden="true"></i> <span data-i18n="nav.deconnexion">Déconnexion</span>
        </a>
    </div>
</div>
