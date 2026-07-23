<?php
$pageTitle = "Accès refusé - " . APP_NAME;
require ROOT_PATH . '/assets/inc/header.php';
?>
<div class="container py-5 text-center">
    <h1>403 - Accès refusé</h1>
    <p>Vous n'avez pas les droits nécessaires pour accéder à cette page.</p>
    <a href="<?php echo BASE_URL; ?>/index.php">Retour à l'accueil</a>
</div>
<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
