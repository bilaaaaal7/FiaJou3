<?php
session_start();
require_once "../config/db.php";
$stmt = $pdo->query("SELECT COUNT(*) FROM categories");
$nbCategories = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT COUNT(*) FROM plats");
$nbPlats = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$nbUsers = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$nbOrders = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon-16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/images/favicon-180.png">
    <link rel="shortcut icon" href="../assets/images/favicon.ico">
<style>
body{
    font-family: Arial, sans-serif;
    background:#f5f5f5;
    margin:40px;
}

h1{
    text-align:center;
}

.dashboard{
    display:flex;
    gap:20px;
    flex-wrap:wrap;
    margin-top:40px;
}

.card{
    width:220px;
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,0.15);
    text-align:center;
}

.card h2{
    margin:0;
    font-size:20px;
}

.card p{
    font-size:40px;
    font-weight:bold;
    margin-top:15px;
}
</style>
</head>
<body>

<h1>Dashboard Administrateur</h1>
<div class="dashboard">

    <div class="card">
        <h2>Catégories</h2>
        <p><?php echo $nbCategories; ?></p>
    </div>

    <div class="card">
        <h2>Plats</h2>
        <p><?php echo $nbPlats; ?></p>
    </div>

    <div class="card">
        <h2>Utilisateurs</h2>
        <p><?php echo $nbUsers; ?></p>
    </div>

    <div class="card">
        <h2>Commandes</h2>
        <p><?php echo $nbOrders; ?></p>
    </div>

</div> 

</body>
</html>