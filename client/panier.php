<?php
session_start();
require_once "../config/db.php";

if(isset($_GET['plus'])){

    $id = $_GET['plus'];

    if(isset($_SESSION['panier'][$id])){
        $_SESSION['panier'][$id]++;
    }

    header("Location: panier.php");
    exit();
}

if(isset($_GET['moins'])){

    $id = $_GET['moins'];

    if(isset($_SESSION['panier'][$id])){

        $_SESSION['panier'][$id]--;

        if($_SESSION['panier'][$id] <= 0){
            unset($_SESSION['panier'][$id]);
        }

    }

    header("Location: panier.php");
    exit();
}

if(isset($_GET['supprimer'])){

    $id = $_GET['supprimer'];

    unset($_SESSION['panier'][$id]);

    header("Location: panier.php");
    exit();
}

$panier = [];

$total = 0;

if(isset($_SESSION['panier']) && !empty($_SESSION['panier'])){

    foreach($_SESSION['panier'] as $id=>$quantite){

        $stmt = $pdo->prepare("SELECT * FROM plats WHERE id=?");
        $stmt->execute([$id]);

        $plat = $stmt->fetch();

        if($plat){

            $plat['quantite']=$quantite;
            $plat['sous_total']=$plat['prix']*$quantite;

            $total += $plat['sous_total'];

            $panier[]=$plat;
        }

    }

}

if(isset($_GET['vider'])){

    unset($_SESSION['panier']);

    header("Location: panier.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Mon panier</title>
</head>

<body>

<h1>Mon panier</h1>

<table border="1" cellpadding="10">

<tr>

<th>Image</th>
<th>Nom</th>
<th>Prix</th>
<th>Quantité</th>
<th>Sous-total</th>
<th>Actions</th>

</tr>


<?php foreach($panier as $plat){ ?>

<tr>

<td>
<img src="../uploads/<?php echo $plat['image']; ?>" width="100">
</td>

<td><?php echo $plat['nom']; ?></td>

<td><?php echo $plat['prix']; ?> DH</td>

<td><?php echo $plat['quantite']; ?></td>

<td><?php echo $plat['sous_total']; ?> DH</td>

<td>

<a href="?moins=<?php echo $plat['id']; ?>">➖</a>

&nbsp;

<a href="?plus=<?php echo $plat['id']; ?>">➕</a>

&nbsp;

<a href="?supprimer=<?php echo $plat['id']; ?>"
   onclick="return confirm('Supprimer ce plat du panier ?')">
   🗑️
</a>

</td>

</tr>

<?php } ?>

</table>

<br>

<a href="?vider=1"
   onclick="return confirm('Voulez-vous vider le panier ?')">
    Vider le panier
</a>

<h2>Total : <?php echo $total; ?> DH</h2>

<br><br>

<a href="commander.php">
    Commander
</a>

</body>
</html>