<?php
session_start();
require_once "../config/db.php";

$nom = "";
$description = "";
$prix = "";
$image = "";
$disponible = 1;
$category_id = "";
$idModifier = "";

if (isset($_POST['ajouter'])) {

    $category_id = $_POST['category_id'];
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];

    $ancienne_image = $_POST['ancienne_image'];

    if ($_FILES['image']['name'] != "") {

        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];

        move_uploaded_file($tmp, "../uploads/" . $image);

    } else {

        $image = $ancienne_image;

    }

    $disponible = $_POST['disponible'];

    $stmt = $pdo->prepare("
        INSERT INTO plats
        (category_id, nom, description, prix, image, disponible)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $category_id,
        $nom,
        $description,
        $prix,
        $image,
        $disponible
    ]);

    header("Location: plats.php");
    exit;
}

if (isset($_POST['modifier'])) {

    $id = $_POST['id'];
    $category_id = $_POST['category_id'];
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];

    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    move_uploaded_file($tmp, "../uploads/" . $image);

    $disponible = $_POST['disponible'];

    $stmt = $pdo->prepare("
        UPDATE plats
        SET
            category_id = ?,
            nom = ?,
            description = ?,
            prix = ?,
            image = ?,
            disponible = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $category_id,
        $nom,
        $description,
        $prix,
        $image,
        $disponible,
        $id
    ]);

    header("Location: plats.php");
    exit;
}

if (isset($_GET['supprimer'])) {

    $id = $_GET['supprimer'];
    $stmt = $pdo->prepare("DELETE FROM plats WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: plats.php");
    exit;

}

if (isset($_GET['modifier'])) {

    $id = $_GET['modifier'];

    $stmt = $pdo->prepare("SELECT * FROM plats WHERE id = ?");
    $stmt->execute([$id]);

    $plat = $stmt->fetch();

    $idModifier = $plat['id'];
    $category_id = $plat['category_id'];
    $nom = $plat['nom'];
    $description = $plat['description'];
    $prix = $plat['prix'];
    $image = $plat['image'];
    $disponible = $plat['disponible'];
}

$stmt = $pdo->prepare("SELECT * FROM plats");
$stmt->execute();

$plats = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM categories");
$stmt->execute();

$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    <table border="1">
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Prix</th>
        <th>Images</th>
        <th>Disponible</th>
        <th>Actions</th>
    </tr>

    <?php foreach($plats as $plat){ ?>

    <tr>
        <td><?php echo $plat['id']; ?></td>
        <td><?php echo $plat['nom']; ?></td>
        <td><?php echo $plat['prix']; ?></td>
        <td>
            <img src="../uploads/<?php echo $plat['image']; ?>" width="100">
        </td>
        <td><?php echo $plat['disponible']; ?></td>
        <td>
            <a href="?supprimer=<?php echo $plat['id']; ?>">Supprimer</a>
            <a href="?modifier=<?php echo $plat['id']; ?>">Modifier</a>
        </td>
    </tr>

    <?php } ?>
</table>

<?php if ($idModifier != "") { ?>
    <h2>Modifier un plat</h2>
<?php } else { ?>
    <h2>Ajouter un plat</h2>
<?php } ?>

<form method="POST" enctype="multipart/form-data">

    <label>Catégorie</label>

    <select name="category_id" required>

        <?php foreach ($categories as $categorie) { ?>

            <option
                value="<?php echo $categorie['id']; ?>"
                <?php if($categorie['id'] == $category_id){ echo "selected"; } ?>
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
    
</body>
</html>