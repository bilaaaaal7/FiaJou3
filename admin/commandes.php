<?php
session_start();
require_once "../config/db.php";

$statut = "";
$idModifier = "";

if(isset($_GET['modifier'])){

    $id = $_GET['modifier'];

    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$id]);

    $commande = $stmt->fetch();

    $idModifier = $commande['id'];
    $statut = $commande['statut'];
}

if(isset($_POST['modifierStatut'])){

    $id = $_POST['id'];
    $statut = $_POST['statut'];

    $stmt = $pdo->prepare("
        UPDATE orders
        SET statut = ?
        WHERE id = ?
    ");

    $stmt->execute([$statut, $id]);

    header("Location: commandes.php");
    exit();
}

$stmt = $pdo->prepare("
SELECT
    orders.id,
    orders.date_commande,
    orders.date_livraison,
    orders.heure_livraison,
    orders.total,
    orders.statut,
    orders.commentaire,
    users.email,
    profiles.prenom,
    profiles.nom
FROM orders
INNER JOIN users
ON orders.user_id = users.id
INNER JOIN profiles
ON users.id = profiles.user_id
ORDER BY orders.id DESC
");

$stmt->execute();

$commandes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion des commandes</title>
</head>
<body>

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

<?php foreach($commandes as $commande){ ?>

<tr>

    <td><?php echo $commande['id']; ?></td>

    <td>
        <?php echo $commande['prenom']." ".$commande['nom']; ?>
    </td>

    <td><?php echo $commande['email']; ?></td>

    <td><?php echo $commande['date_commande']; ?></td>

    <td><?php echo $commande['date_livraison']; ?></td>

    <td><?php echo $commande['heure_livraison']; ?></td>

    <td><?php echo $commande['total']; ?> DH</td>

    <td><?php echo $commande['statut']; ?></td>

    <td><?php echo $commande['commentaire']; ?></td>

    <td>
        <a href="?modifier=<?php echo $commande['id']; ?>">
            Modifier le statut
        </a>
    </td>

</tr>

<?php } ?>

</table>

<?php if($idModifier != ""){ ?>

<hr>

<h2>Modifier le statut</h2>

<form method="POST">

    <input type="hidden" name="id" value="<?php echo $idModifier; ?>">

    <select name="statut">

        <option value="En attente" <?php if($statut=="En attente") echo "selected"; ?>>
            En attente
        </option>

        <option value="En préparation" <?php if($statut=="En préparation") echo "selected"; ?>>
            En préparation
        </option>

        <option value="En livraison" <?php if($statut=="En livraison") echo "selected"; ?>>
            En livraison
        </option>

        <option value="Livrée" <?php if($statut=="Livrée") echo "selected"; ?>>
            Livrée
        </option>

        <option value="Annulée" <?php if($statut=="Annulée") echo "selected"; ?>>
            Annulée
        </option>

    </select>

    <br><br>

    <button type="submit" name="modifierStatut">
        Enregistrer
    </button>

</form>

<?php } ?>

</body>
</html>