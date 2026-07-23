<?php
$pageTitle = "Dashboard Admin - " . APP_NAME;
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<h1>Dashboard Administrateur</h1>
<div class="dashboard">

    <div class="card">
        <h2>Catégories</h2>
        <p><?php echo $nbCategories; ?></p>
    </div>

    <div class="card">
        <h2>Plats</h2>
        <p><?php echo $nbPlats; ?></p>
    </div>

    <div class="card">
        <h2>Utilisateurs</h2>
        <p><?php echo $nbUsers; ?></p>
    </div>

    <div class="card">
        <h2>Commandes</h2>
        <p><?php echo $nbOrders; ?></p>
    </div>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
