<?php
$pageTitle = "Espace livreur - " . APP_NAME;
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<h1>Livraisons en cours</h1>

<table border="1" cellpadding="10">
<tr>
    <th>ID</th>
    <th>Client</th>
    <th>Livraison prévue</th>
    <th>Commentaire</th>
    <th>Action</th>
</tr>
<?php foreach ($commandesEnLivraison as $commande) { ?>
<tr>
    <td><?php echo $commande['id']; ?></td>
    <td><?php echo $commande['prenom'] . ' ' . $commande['nom']; ?></td>
    <td><?php echo $commande['date_livraison'] . ' ' . $commande['heure_livraison']; ?></td>
    <td><?php echo $commande['commentaire']; ?></td>
    <td>
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=livreur">
            <input type="hidden" name="id" value="<?php echo $commande['id']; ?>">
            <button type="submit" name="livrer">Marquer comme livrée</button>
        </form>
    </td>
</tr>
<?php } ?>
</table>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
