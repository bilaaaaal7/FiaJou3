<?php
$pageTitle = "Accès refusé - " . APP_NAME;
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<div class="panel" style="max-width:600px; margin:60px auto; text-align:center;">
    <h1 style="font-size:3rem; color:var(--gold-dark); margin-bottom:8px;">403</h1>
    <h2 style="margin-bottom:12px;">Accès refusé</h2>
    <p style="color:var(--text-muted); margin-bottom:24px;">Vous n'avez pas les droits nécessaires pour accéder à cette page.</p>
    <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-gold">Retour à l'accueil</a>
</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
