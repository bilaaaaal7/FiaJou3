<?php
$pageTitle = "Espace cuisinier - " . APP_NAME;
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<h1>Commandes en attente</h1>

<table border="1" cellpadding="10">
<tr>
    <th>ID</th>
    <th>Client</th>
    <th>Livraison</th>
    <th>Commentaire</th>
    <th>Action</th>
</tr>
<?php foreach ($commandesEnAttente as $commande) { ?>
<tr>
    <td><?php echo $commande['id']; ?></td>
    <td><?php echo $commande['prenom'] . ' ' . $commande['nom']; ?></td>
    <td><?php echo $commande['date_livraison'] . ' ' . $commande['heure_livraison']; ?></td>
    <td><?php echo $commande['commentaire']; ?></td>
    <td>
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=cuisinier">
            <input type="hidden" name="id" value="<?php echo $commande['id']; ?>">
            <input type="hidden" name="nouveau_statut" value="En préparation">
            <button type="submit" name="avancerStatut">Commencer la préparation</button>
        </form>
    </td>
</tr>
<?php } ?>
</table>

<h1>Commandes en préparation</h1>

<table border="1" cellpadding="10">
<tr>
    <th>ID</th>
    <th>Client</th>
    <th>Livraison</th>
    <th>Commentaire</th>
    <th>Action</th>
</tr>
<?php foreach ($commandesEnPreparation as $commande) { ?>
<tr>
    <td><?php echo $commande['id']; ?></td>
    <td><?php echo $commande['prenom'] . ' ' . $commande['nom']; ?></td>
    <td><?php echo $commande['date_livraison'] . ' ' . $commande['heure_livraison']; ?></td>
    <td><?php echo $commande['commentaire']; ?></td>
    <td>
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=cuisinier">
            <input type="hidden" name="id" value="<?php echo $commande['id']; ?>">
            <input type="hidden" name="nouveau_statut" value="En livraison">
            <button type="submit" name="avancerStatut">Prête, remettre au livreur</button>
        </form>
    </td>
</tr>
<?php } ?>
</table>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
