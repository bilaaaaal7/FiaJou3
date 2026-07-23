<?php
/**
 * Contrôleur : Inscription
 * Route : /inscription
 */

require_once ROOT_PATH . '/modele/UtilisateurModele.php';

$error = "";
$success = "";

if (isset($_POST['register'])) {
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $telephone = trim($_POST['telephone']);
    $adresse = trim($_POST['adresse']);
    $ville = trim($_POST['ville']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmation = $_POST['confirmation'];

    if ($password != $confirmation) {
        $error = "Les mots de passe ne correspondent pas.";
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

            $success = "Compte créé avec succès. Vous pouvez vous connecter.";
        }
    }
}

require ROOT_PATH . '/vue/auth/register.php';
