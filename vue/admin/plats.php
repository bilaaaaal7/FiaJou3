<?php
$pageTitle = "Gestion des plats - " . APP_NAME;
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>
<table border="1">
<tr>
    <th>ID</th>
    <th>Nom</th>
    <th>Prix</th>
    <th>Images</th>
    <th>Disponible</th>
    <th>Actions</th>
</tr>

<?php foreach ($plats as $plat) { ?>

<tr>
    <td><?php echo $plat['id']; ?></td>
    <td><?php echo $plat['nom']; ?></td>
    <td><?php echo $plat['prix']; ?></td>
    <td>
        <img src="<?php echo UPLOADS_URL; ?>/<?php echo $plat['image']; ?>" width="100">
    </td>
    <td><?php echo $plat['disponible']; ?></td>
    <td>
        <a href="?route=admin/plats&supprimer=<?php echo $plat['id']; ?>">Supprimer</a>
        <a href="?route=admin/plats&modifier=<?php echo $plat['id']; ?>">Modifier</a>
    </td>
</tr>

<?php } ?>
</table>

<?php if ($idModifier != "") { ?>
    <h2>Modifier un plat</h2>
<?php } else { ?>
    <h2>Ajouter un plat</h2>
<?php } ?>

<form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/plats" enctype="multipart/form-data">

    <label>Catégorie</label>

    <select name="category_id" required>

        <?php foreach ($categories as $categorie) { ?>

            <option
                value="<?php echo $categorie['id']; ?>"
                <?php if ($categorie['id'] == $category_id) { echo "selected"; } ?>
            >
                <?php echo $categorie['nom']; ?>
            </option>

        <?php } ?>

    </select>

    <br><br>

<label>Nom</label>
<input type="text" name="nom" value="<?php echo $nom; ?>" required>


<br><br>

<label>Description</label>
<input type="text" name="description" value="<?php echo $description; ?>" required>

<br><br>

<label>Prix</label>
<input type="number" step="0.01" name="prix" value="<?php echo $prix; ?>" required>

<br><br>

<label>Image</label>
<input type="file" name="image">

<br><br>

<label>Disponible</label>

<select name="disponible">
    <option value="1">Oui</option>
    <option value="0">Non</option>
</select>

<input type="hidden" name="id" value="<?php echo $idModifier; ?>">
<input type="hidden" name="ancienne_image" value="<?php echo $image; ?>">
<br><br>

<?php if ($idModifier != "") { ?>

<button type="submit" name="modifier">
    Modifier
</button>

<?php } else { ?>

<button type="submit" name="ajouter">
    Ajouter
</button>

<?php } ?>

</form>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
