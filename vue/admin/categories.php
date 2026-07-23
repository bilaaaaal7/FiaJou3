<?php
$pageTitle = "Categories - " . APP_NAME;
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<table border="1">

<tr>
    <th>ID</th>
    <th>Nom</th>
    <th>Description</th>
    <th>Image</th>
    <th>Actions</th>
</tr>

<?php foreach ($categories as $categorie) { ?>

<tr>
    <td><?php echo $categorie['id']; ?></td>
    <td><?php echo $categorie['nom']; ?></td>
    <td><?php echo $categorie['description']; ?></td>
    <td><?php echo $categorie['image']; ?></td>
    <td>
        <a href="?route=admin/categories&supprimer=<?php echo $categorie['id']; ?>">Supprimer</a>
        <a href="?route=admin/categories&modifier=<?php echo $categorie['id']; ?>">Modifier</a>
    </td>
</tr>

<?php } ?>

</table>
<h2>Ajouter une catégorie</h2>

<form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/categories">

    <label>Nom</label>
    <input type="text" name="nom" value="<?php echo $nom; ?>" required>

    <br><br>

    <label>Description</label>
    <input type="text" name="description" value="<?php echo $description; ?>" required>

    <br><br>

    <label>Image</label>
    <input type="text" name="image" value="<?php echo $image; ?>" required>

    <input type="hidden" name="id" value="<?php echo $idModifier; ?>">

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

<br><br>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
