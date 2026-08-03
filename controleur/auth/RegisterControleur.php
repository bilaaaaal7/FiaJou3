<?php
/**
 * Contrôleur : Inscription
 * Route : /inscription
 */

require_once ROOT_PATH . '/modele/UtilisateurModele.php';
require_once ROOT_PATH . '/modele/RateLimiterModele.php';

$error = "";
$success = "";
$limiteur = new RateLimiterModele('inscription');
$blocageRestant = $limiteur->tempsRestantBlocage();

if (isset($_POST['register'])) {
    if (!$limiteur->peutTenter()) {
        $error = "Trop de tentatives. Réessayez dans " . ceil($blocageRestant / 60) . " minute(s).";
    } else {
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $telephone = trim($_POST['telephone']);
    $adresse = trim($_POST['adresse']);
    $ville = trim($_POST['ville']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmation = $_POST['confirmation'];

    if (empty($prenom) || empty($nom) || empty($telephone) || empty($email) || empty($password)) {
        $error = "Tous les champs obligatoires doivent être remplis.";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères.";
    } elseif ($password != $confirmation) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "L'adresse email n'est pas valide.";
    } else {
        $utilisateurModele = new UtilisateurModele();
        $existant = $utilisateurModele->findByEmail($email);

        if ($existant) {
            $error = "Cet email est déjà utilisé.";
        } else {
            $utilisateurModele->creerCompte([
                'prenom' => $prenom,
                'nom' => $nom,
                'telephone' => $telephone,
                'adresse' => $adresse,
                'ville' => $ville,
                'email' => $email,
                'password' => $password,
            ]);

            $limiteur->reinitialiser();
            $success = "Compte créé avec succès. Vous pouvez vous connecter.";
        }
    }
    }
}

require ROOT_PATH . '/vue/auth/register.php';
