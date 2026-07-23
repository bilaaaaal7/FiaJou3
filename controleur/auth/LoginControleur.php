<?php
/**
 * Contrôleur : Connexion
 * Route : /connexion
 */

require_once ROOT_PATH . '/modele/UtilisateurModele.php';

$error = "";

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $utilisateurModele = new UtilisateurModele();
    $user = $utilisateurModele->findByEmail($email);

    if (!$user) {
        $error = "Cet email n'existe pas.";
    } elseif (!password_verify($password, $user['password'])) {
        $error = "Mot de passe incorrect.";
    } else {
        $profile = $utilisateurModele->findProfileByUserId($user['id']);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['prenom']  = $profile['prenom'];
        $_SESSION['role']    = $profile['role'];

        header('Location: ' . BASE_URL . '/index.php?route=' . $profile['role']);
        exit;
    }
}

require ROOT_PATH . '/vue/auth/login.php';
