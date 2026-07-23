<?php
$pageTitle = "Menu - " . APP_NAME;
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<h1>Notre Menu</h1>

<p>
<a href="<?php echo BASE_URL; ?>/index.php?route=client/panier">
🛒 Panier (<?php echo $nombreArticles; ?>)
</a>
</p>

<table border="1" cellpadding="10">

<tr>
    <th>Image</th>
    <th>Nom</th>
    <th>Description</th>
    <th>Catégorie</th>
    <th>Prix</th>
    <th>Action</th>
</tr>

<?php foreach ($plats as $plat) { ?>

<tr>

<td>
<img src="<?php echo UPLOADS_URL; ?>/<?php echo $plat['image']; ?>" width="100">
</td>

<td><?php echo $plat['nom']; ?></td>

<td><?php echo $plat['description']; ?></td>

<td><?php echo $plat['categorie']; ?></td>

<td><?php echo $plat['prix']; ?> DH</td>

<td>
    <a href="?route=client&ajouter=<?php echo $plat['id']; ?>">
    Ajouter au panier
</a>
</td>

</tr>

<?php } ?>

</table>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
