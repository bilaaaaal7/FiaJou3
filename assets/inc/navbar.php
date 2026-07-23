<?php
/**
 * Barre de navigation commune, adaptée selon le rôle en session.
 * N'est affichée que pour les utilisateurs connectés.
 */

if (!est_connecte()) {
    return;
}

$role = utilisateur_role();
$panierModele = null;
if ($role === ROLE_CLIENT) {
    require_once ROOT_PATH . '/modele/PanierModele.php';
    $panierModele = new PanierModele();
}
?>
<nav class="navbar navbar-expand-lg fiajou-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo BASE_URL; ?>/index.php?route=<?php echo htmlspecialchars($role); ?>">
            <?php echo APP_NAME; ?>
        </a>

        <ul class="navbar-nav ms-auto align-items-lg-center">
            <?php if ($role === ROLE_ADMIN): ?>
                <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/index.php?route=admin">Tableau de bord</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/index.php?route=admin/categories">Catégories</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/index.php?route=admin/plats">Plats</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/index.php?route=admin/commandes">Commandes</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/index.php?route=admin/utilisateurs">Utilisateurs</a></li>
            <?php elseif ($role === ROLE_CLIENT): ?>
                <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/index.php?route=client">Menu</a></li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/index.php?route=client/panier">
                        🛒 Panier (<?php echo $panierModele->nombreArticles(); ?>)
                    </a>
                </li>
                <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/index.php?route=client/mes-commandes">Mes commandes</a></li>
            <?php elseif ($role === ROLE_CUISINIER): ?>
                <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/index.php?route=cuisinier">Cuisine</a></li>
            <?php elseif ($role === ROLE_LIVREUR): ?>
                <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/index.php?route=livreur">Livraisons</a></li>
            <?php endif; ?>

            <li class="nav-item">
                <span class="nav-link disabled">Bonjour, <?php echo htmlspecialchars($_SESSION['prenom'] ?? ''); ?></span>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>/index.php?route=deconnexion">Déconnexion</a>
            </li>
        </ul>
    </div>
</nav>
