<?php
session_start();
require_once "../config/db.php";

if(!isset($_SESSION['panier']) || empty($_SESSION['panier'])){
    header("Location: panier.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM delivery_zones ORDER BY nom");
$stmt->execute();

$zones = $stmt->fetchAll();

$total = 0;

foreach($_SESSION['panier'] as $id => $quantite){

    $stmt = $pdo->prepare("SELECT prix FROM plats WHERE id = ?");
    $stmt->execute([$id]);

    $plat = $stmt->fetch();

    if($plat){
        $total += $plat['prix'] * $quantite;
    }
}

if(isset($_POST['commander'])){

    $user_id = $_SESSION['user_id'];

    $zone_id = $_POST['zone_id'];
    $date_livraison = $_POST['date_livraison'];
    $heure_livraison = $_POST['heure_livraison'];
    $commentaire = $_POST['commentaire'];

    $date_commande = date("Y-m-d");

    $stmt = $pdo->prepare("
        INSERT INTO orders(
            user_id,
            zone_id,
            date_commande,
            date_livraison,
            heure_livraison,
            total,
            statut,
            commentaire
        )
        VALUES(?,?,?,?,?,?,?,?)
    ");

    $stmt->execute([
        $user_id,
        $zone_id,
        $date_commande,
        $date_livraison,
        $heure_livraison,
        $total,
        "En attente",
        $commentaire
    ]);


    $order_id = $pdo->lastInsertId();



    foreach($_SESSION['panier'] as $product_id => $quantite){

        $stmt = $pdo->prepare("SELECT prix FROM plats WHERE id = ?");
        $stmt->execute([$product_id]);

        $plat = $stmt->fetch();

        $prix = $plat['prix'];

        $stmt = $pdo->prepare("
            INSERT INTO order_items(
                order_id,
                product_id,
                quantite,
                prix
            )
            VALUES(?,?,?,?)
        ");

        $stmt->execute([
            $order_id,
            $product_id,
            $quantite,
            $prix
        ]);

    }

    unset($_SESSION['panier']);

    header("Location: mes_commandes.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commander</title>
</head>
<body>

<h1>Finaliser la commande</h1>

<form method="POST">

    <label>Date de livraison</label><br>
    <input type="date" name="date_livraison" required>
    <br><br>

    <label>Heure de livraison</label><br>
    <input type="time" name="heure_livraison" required>
    <br><br>

    <label>Zone de livraison</label><br>

    <select name="zone_id" required>

        <?php foreach($zones as $zone){ ?>

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

</body>
</html>