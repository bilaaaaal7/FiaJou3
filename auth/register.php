<?php
require_once "../config/db.php";

if (isset($_POST['register'])) {

    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $telephone = $_POST['telephone'];
    $adresse = $_POST['adresse'];
    $ville = $_POST['ville'];
    $email = $_POST['email'];
    $password  = $_POST['password'];
    $confirmation = $_POST['confirmation'];

    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

if ($password != $confirmation) {
    echo "Les mots de passe ne correspondent pas.";
} else {

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);

$user = $stmt->fetch();

if ($user) {
    echo "Cet email est déjà utilisé.";
}else {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->execute([$email, $hashedPassword]);

    $userId = $pdo->lastInsertId();

    $role = "client";
    $stmt = $pdo->prepare("INSERT INTO profiles (user_id, prenom, nom, telephone, adresse, ville, role) VALUES (?, ?, ?, ?, ?,?, ?)");
    $stmt->execute([$userId, $prenom, $nom, $telephone, $adresse, $ville, $role]);
}

}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Formulaire d'inscription</title>
</head>
<body>
    <h2>Inscription</h2>
    <form action="" method="POST">
        <label for="prenom">Prenom</label>
        <input type="text" id="prenom" name="prenom" required>
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" required>
        <label for="telephone">Telephone</label>
        <input type="tel" id="telephone" name="telephone" required>
        <label for="adresse">Adresse</label>
        <input type="text" id="adresse" name="adresse" required>
        <label for="ville">Ville</label>
        <input type="text" id="ville" name="ville" required>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
        <label for="mot de passe">Mot de passe</label>
        <input type="password" id="password" name="password" required>
        <label for="confirmation de mdp">Confirmer le mot de passe</label>
        <input type="password" id="confirmation" name="confirmation" required>

        <button type= "submit" name= "register">S'inscrire</button>
    </form>
</body>
</html>