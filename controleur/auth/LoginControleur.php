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
    } elseif (!$user['actif']) {
        $error = "Votre compte a été désactivé. Contactez l'administrateur.";
    } elseif (!password_verify($password, $user['password'])) {
        $error = "Mot de passe incorrect.";
    } else {
        $profile = $utilisateurModele->findProfileByUserId($user['id']);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['prenom']  = $profile['prenom'];
        $_SESSION['role']    = $profile['role'];

        $route = route_par_defaut_pour_role($profile['role']);

        header('Location: ' . BASE_URL . '/index.php?route=' . $route);
        exit;
    }
}

require ROOT_PATH . '/vue/auth/login.php';
