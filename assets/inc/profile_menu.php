<?php
/**
 * Icône de profil (avatar circulaire) + menu déroulant, affichée en haut à
 * droite pour tout utilisateur connecté. Composant autonome (CSS/JS inline
 * chargés une seule fois via profile-menu.css / profile-menu.js) réutilisé :
 *   - dans l'en-tête public de la landing page (vue/accueil.php), pour un
 *     client connecté ;
 *   - dans la barre du tableau de bord (assets/inc/navbar.php), pour les
 *     autres rôles (admin, cuisinier, livreur).
 *
 * Variable optionnelle avant l'include :
 *   $profileMenuVariant : 'light' (fond sombre, texte clair - navbar publique)
 *                         | 'dark' (fond clair, texte sombre - app-shell)
 */

// Ce menu déroulant (Mon Profil / Mes Commandes / Déconnexion) est spécifique
// à l'espace Client. Les autres rôles gardent leur propre lien de déconnexion
// existant dans la sidebar.
if (!est_connecte() || utilisateur_role() !== ROLE_CLIENT) {
    return;
}

$profileMenuVariant = $profileMenuVariant ?? 'dark';
$prenomUser = $_SESSION['prenom'] ?? '';
$nomUser    = $_SESSION['nom'] ?? '';
$initiales  = strtoupper(mb_substr($prenomUser, 0, 1) . mb_substr($nomUser, 0, 1));
if ($initiales === '') {
    $initiales = '?';
}
?>
<div class="profile-menu profile-menu--<?php echo htmlspecialchars($profileMenuVariant); ?>" data-profile-menu>
    <button type="button" class="profile-menu__trigger" data-profile-trigger aria-haspopup="true" aria-expanded="false">
        <span class="profile-menu__avatar"><?php echo htmlspecialchars($initiales); ?></span>
    </button>

    <div class="profile-menu__dropdown" data-profile-dropdown role="menu">
        <div class="profile-menu__header">
            <span class="profile-menu__avatar profile-menu__avatar--lg"><?php echo htmlspecialchars($initiales); ?></span>
            <div>
                <div class="profile-menu__name"><?php echo htmlspecialchars(trim($prenomUser . ' ' . $nomUser)); ?></div>
                <div class="profile-menu__role"><?php echo htmlspecialchars(utilisateur_role() ?? ''); ?></div>
            </div>
        </div>
        <a role="menuitem" href="<?php echo BASE_URL; ?>/index.php?route=client/profil">
            <i class="fa fa-user" aria-hidden="true"></i> Mon Profil
        </a>
        <a role="menuitem" href="<?php echo BASE_URL; ?>/index.php?route=client/mes-commandes">
            <i class="fa fa-shopping-bag" aria-hidden="true"></i> Mes Commandes
        </a>
        <div class="profile-menu__divider"></div>
        <a role="menuitem" href="<?php echo BASE_URL; ?>/index.php?route=deconnexion" class="profile-menu__logout">
            <i class="fa fa-sign-out" aria-hidden="true"></i> Déconnexion
        </a>
    </div>
</div>
