<?php
$pageTitle = "Commander - " . APP_NAME;
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<h1>Finaliser la commande</h1>

<form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=client/commander">

    <label>Date de livraison</label><br>
    <input type="date" name="date_livraison" required>
    <br><br>

    <label>Heure de livraison</label><br>
    <input type="time" name="heure_livraison" required>
    <br><br>

    <label>Zone de livraison</label><br>

    <select name="zone_id" required>

        <?php foreach ($zones as $zone) { ?>

        <option value="<?php echo $zone['id']; ?>">
            <?php echo $zone['nom']; ?>
        </option>

        <?php } ?>

    </select>

    <br><br>

    <label>Commentaire</label><br>

    <textarea
        name="commentaire"
        rows="4"
        cols="40">
    </textarea>

    <br><br>

    <h3>Total à payer : <?php echo $total; ?> DH</h3>

    <br>

    <button type="submit" name="commander">
        Valider la commande
    </button>

</form>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
