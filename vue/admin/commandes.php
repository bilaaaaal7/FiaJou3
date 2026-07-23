<?php
$pageTitle = "Gestion des commandes - " . APP_NAME;
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<h1>Gestion des commandes</h1>

<table border="1" cellpadding="10">

<tr>
    <th>ID</th>
    <th>Client</th>
    <th>Email</th>
    <th>Date de commande</th>
    <th>Date livraison</th>
    <th>Heure livraison</th>
    <th>Total</th>
    <th>Statut</th>
    <th>Commentaire</th>
    <th>Action</th>
</tr>

<?php foreach ($commandes as $commande) { ?>

<tr>

    <td><?php echo $commande['id']; ?></td>

    <td>
        <?php echo $commande['prenom'] . " " . $commande['nom']; ?>
    </td>

    <td><?php echo $commande['email']; ?></td>

    <td><?php echo $commande['date_commande']; ?></td>

    <td><?php echo $commande['date_livraison']; ?></td>

    <td><?php echo $commande['heure_livraison']; ?></td>

    <td><?php echo $commande['total']; ?> DH</td>

    <td><?php echo $commande['statut']; ?></td>

    <td><?php echo $commande['commentaire']; ?></td>

    <td>
        <a href="?route=admin/commandes&modifier=<?php echo $commande['id']; ?>">
            Modifier le statut
        </a>
    </td>

</tr>

<?php } ?>

</table>

<?php if ($idModifier != "") { ?>

<hr>

<h2>Modifier le statut</h2>

<form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/commandes">

    <input type="hidden" name="id" value="<?php echo $idModifier; ?>">

    <select name="statut">

        <?php foreach (STATUTS_COMMANDE as $option) { ?>
        <option value="<?php echo $option; ?>" <?php if ($statut == $option) echo "selected"; ?>>
            <?php echo $option; ?>
        </option>
        <?php } ?>

    </select>

    <br><br>

    <button type="submit" name="modifierStatut">
        Enregistrer
    </button>

</form>

<?php } ?>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
