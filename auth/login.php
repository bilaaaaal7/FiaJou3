<?php
require_once "../config/db.php";
session_start();
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    $user = $stmt->fetch();

    echo "<pre>";
    print_r($user);
    echo "</pre>";

    if (!$user) {
        echo "Cet email n'existe pas.";
    }

    else {
    if (password_verify($password, $user['password'])) {
            $profile = $stmt->fetch();
            echo "<pre>";
            var_dump($profile);
            echo "</pre>";
            $stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            $profile = $stmt->fetch();

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['prenom'] = $profile['prenom'];
            $_SESSION['role'] = $profile['role'];

            if ($profile['role'] == "admin") {

                header("Location: ../admin/index.php");
                exit;

            } elseif ($profile['role'] == "client") {

                header("Location: ../client/index.php");
                exit;

            } elseif ($profile['role'] == "cuisinier") {

                header("Location: ../cuisinier/index.php");
                exit;

            } elseif ($profile['role'] == "livreur") {

                header("Location: ../livreur/index.php");
                exit;

}
        echo "Connexion réussie.";
    } else {

        echo "Mot de passe incorrect.";
    }
    }

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Formulaire Connexion</title>
</head>
<body>

    <h2>Connexion</h2>

    <form method="POST">

    <label>Email</label>
    <input type="email" name="email" required>

    <br><br>

    <label>Mot de passe</label>
    <input type="password" name="password" required>

    <br><br>

    <button type="submit" name="login">
        Se connecter
    </button>

</form>
    
</body>
</html>