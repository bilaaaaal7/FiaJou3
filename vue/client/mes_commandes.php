<?php
$pageTitle = "Mes commandes - " . APP_NAME;
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<h1>Mes commandes</h1>

<table border="1" cellpadding="10">

<tr>
    <th>ID</th>
    <th>Date de commande</th>
    <th>Date livraison</th>
    <th>Heure livraison</th>
    <th>Total</th>
    <th>Statut</th>
    <th>Commentaire</th>
</tr>

<?php foreach ($commandes as $commande) { ?>

<tr>
    <td><?php echo $commande['id']; ?></td>
    <td><?php echo $commande['date_commande']; ?></td>
    <td><?php echo $commande['date_livraison']; ?></td>
    <td><?php echo $commande['heure_livraison']; ?></td>
    <td><?php echo $commande['total']; ?> DH</td>
    <td><?php echo $commande['statut']; ?></td>
    <td><?php echo $commande['commentaire']; ?></td>
</tr>

<?php } ?>

<?php if (empty($commandes)) { ?>
<tr>
    <td colspan="7">Vous n'avez pas encore passé de commande.</td>
</tr>
<?php } ?>

</table>

<br>

<a href="<?php echo BASE_URL; ?>/index.php?route=client">Retour au menu</a>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
