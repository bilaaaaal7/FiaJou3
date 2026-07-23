<?php
$pageTitle = "Page introuvable - " . APP_NAME;
require ROOT_PATH . '/assets/inc/header.php';
?>
<div class="container py-5 text-center">
    <h1>404 - Page introuvable</h1>
    <p>La page que vous cherchez n'existe pas.</p>
    <a href="<?php echo BASE_URL; ?>/index.php">Retour à l'accueil</a>
</div>
<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
