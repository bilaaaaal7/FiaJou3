<?php
/**
 * Contrôleur : Mot de passe oublié
 * Route : /mot-de-passe-oublie
 */

require_once ROOT_PATH . '/modele/UtilisateurModele.php';

$message = "";
$erreur = "";
$modeReset = false;
$emailReset = "";

if (isset($_POST['envoyer'])) {
    $email = trim($_POST['email']);
    $utilisateurModele = new UtilisateurModele();
    $user = $utilisateurModele->findByEmail($email);

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $_SESSION['reset_token'] = $token;
        $_SESSION['reset_user_id'] = $user['id'];
        $_SESSION['reset_expiry'] = time() + 3600;
        $modeReset = true;
        $emailReset = $email;
        $message = "Un code de réinitialisation a été généré.";
    } else {
        $erreur = "Aucun compte trouvé avec cet email.";
    }
}

if (isset($_POST['reset'])) {
    $token = $_POST['token'] ?? '';
    $nouveauMdp = $_POST['nouveau_mdp'] ?? '';
    $confirmation = $_POST['confirmation'] ?? '';

    if (empty($nouveauMdp) || strlen($nouveauMdp) < 6) {
        $erreur = "Le mot de passe doit contenir au moins 6 caractères.";
        $modeReset = true;
        $emailReset = $_POST['email'] ?? '';
    } elseif ($nouveauMdp !== $confirmation) {
        $erreur = "Les mots de passe ne correspondent pas.";
        $modeReset = true;
        $emailReset = $_POST['email'] ?? '';
    } elseif (!isset($_SESSION['reset_token']) || $_SESSION['reset_token'] !== $token || time() > ($_SESSION['reset_expiry'] ?? 0)) {
        $erreur = "Le lien de réinitialisation est expiré ou invalide.";
    } else {
        $utilisateurModele = new UtilisateurModele();
        $utilisateurModele->changerMdp($_SESSION['reset_user_id'], $nouveauMdp);

        unset($_SESSION['reset_token'], $_SESSION['reset_user_id'], $_SESSION['reset_expiry']);

        $message = "Mot de passe réinitialisé avec succès. Vous pouvez vous connecter.";
    }
}

require ROOT_PATH . '/vue/auth/mot_de_passe_oublie.php';
