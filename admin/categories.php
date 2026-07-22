<?php

require_once "../config/db.php";
session_start();
$nom = "";
$description = "";
$image = "";
$idModifier = "";
if (isset($_POST['ajouter'])) {

    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $image = $_POST['image'];

    $stmt = $pdo->prepare("INSERT INTO categories (nom, description, image) VALUES (?, ?, ?)");
    $stmt->execute([$nom, $description, $image]);

    header("Location: index.php");
    exit;
}

if (isset($_POST['modifier'])) {

    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $image = $_POST['image'];

    $stmt = $pdo->prepare("UPDATE categories SET nom = ?, description = ?, image = ? WHERE id = ?");
    $stmt->execute([$nom, $description, $image, $id]);

    header("Location: index.php");
    exit;
}

if (isset($_GET['supprimer'])) {

    $id = $_GET['supprimer'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: index.php");
    exit;

}
if (isset($_GET['modifier'])) {

    $id = $_GET['modifier'];

    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);

    $categorie = $stmt->fetch();

    $idModifier = $categorie['id'];
    $nom = $categorie['nom'];
    $description = $categorie['description'];
    $image = $categorie['image'];
}
$stmt = $pdo->prepare("SELECT * FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Categories</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon-16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/images/favicon-180.png">
    <link rel="shortcut icon" href="../assets/images/favicon.ico">
</head>
<body>

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
            <a href="?supprimer=<?php echo $categorie['id']; ?>">Supprimer</a>
            <a href="?modifier=<?php echo $categorie['id']; ?>">Modifier</a>
        </td>
    </tr>

    <?php } ?>

</table>
<h2>Ajouter une catégorie</h2>

<form method="POST">

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
    
</body>
</html>