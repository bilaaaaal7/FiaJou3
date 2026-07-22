<?php
session_start();
require_once "../config/db.php";

if (isset($_GET['ajouter'])) {

    $id = $_GET['ajouter'];

    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    if (isset($_SESSION['panier'][$id])) {
        $_SESSION['panier'][$id]++;
    } else {
        $_SESSION['panier'][$id] = 1;
    }

    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("
SELECT
    plats.id,
    plats.nom,
    plats.description,
    plats.prix,
    plats.image,
    categories.nom AS categorie
FROM plats
INNER JOIN categories
ON plats.category_id = categories.id
ORDER BY categories.nom, plats.nom
");

$stmt->execute();

$plats = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Menu</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon-16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/images/favicon-180.png">
    <link rel="shortcut icon" href="../assets/images/favicon.ico">
</head>
<body>

<h1>Notre Menu</h1>

<?php

$nombreArticles = 0;

if(isset($_SESSION['panier'])){
    $nombreArticles = array_sum($_SESSION['panier']);
}

?>

<p>
<a href="panier.php">
🛒 Panier (<?php echo $nombreArticles; ?>)
</a>
</p>

<table border="1" cellpadding="10">

<tr>
    <th>Image</th>
    <th>Nom</th>
    <th>Description</th>
    <th>Catégorie</th>
    <th>Prix</th>
    <th>Action</th>
</tr>

<?php foreach($plats as $plat){ ?>

<tr>

<td>
<img src="../uploads/<?php echo $plat['image']; ?>" width="100">
</td>

<td><?php echo $plat['nom']; ?></td>

<td><?php echo $plat['description']; ?></td>

<td><?php echo $plat['categorie']; ?></td>

<td><?php echo $plat['prix']; ?> DH</td>

<td>
    <a href="?ajouter=<?php echo $plat['id']; ?>">
    Ajouter au panier
</a>
</td>

</tr>

<?php } ?>

</table>

</body>
</html>