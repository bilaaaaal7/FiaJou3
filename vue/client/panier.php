<?php
$pageTitle = "Mon panier - " . APP_NAME;
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<h1>Mon panier</h1>

<table border="1" cellpadding="10">

<tr>

<th>Image</th>
<th>Nom</th>
<th>Prix</th>
<th>Quantité</th>
<th>Sous-total</th>
<th>Actions</th>

</tr>


<?php foreach ($panier as $plat) { ?>

<tr>

<td>
<img src="<?php echo UPLOADS_URL; ?>/<?php echo $plat['image']; ?>" width="100">
</td>

<td><?php echo $plat['nom']; ?></td>

<td><?php echo $plat['prix']; ?> DH</td>

<td><?php echo $plat['quantite']; ?></td>

<td><?php echo $plat['sous_total']; ?> DH</td>

<td>

<a href="?route=client/panier&moins=<?php echo $plat['id']; ?>">➖</a>

&nbsp;

<a href="?route=client/panier&plus=<?php echo $plat['id']; ?>">➕</a>

&nbsp;

<a href="?route=client/panier&supprimer=<?php echo $plat['id']; ?>"
   onclick="return confirm('Supprimer ce plat du panier ?')">
   🗑️
</a>

</td>

</tr>

<?php } ?>

</table>

<br>

<a href="?route=client/panier&vider=1"
   onclick="return confirm('Voulez-vous vider le panier ?')">
    Vider le panier
</a>

<h2>Total : <?php echo $total; ?> DH</h2>

<br><br>

<a href="<?php echo BASE_URL; ?>/index.php?route=client/commander">
    Commander
</a>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
