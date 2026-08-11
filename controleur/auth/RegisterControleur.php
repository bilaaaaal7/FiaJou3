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
            $userId = $utilisateurModele->creerCompte([
                'prenom' => $prenom,
                'nom' => $nom,
                'telephone' => $telephone,
                'adresse' => $adresse,
                'ville' => $ville,
                'email' => $email,
                'password' => $password,
            ]);

            $limiteur->reinitialiser();

            // Auto-connexion : même comportement que le flux de connexion.
            $profile = $utilisateurModele->findProfileByUserId($userId);

            $_SESSION['user_id'] = $userId;
            $_SESSION['prenom']  = $profile['prenom'];
            $_SESSION['role']    = $profile['role'];
            $_SESSION['email']   = $email;

            // Si l'utilisateur venait d'un bouton « Commander » (retour
            // mémorisé depuis la page de connexion), on le renvoie vers la
            // page de commande initiale une fois le compte créé.
            $retour = retour_connexion_valide($_GET['retour'] ?? null);
            if ($retour !== null) {
                header('Location: ' . BASE_URL . '/' . $retour);
                exit;
            }

            $route = route_par_defaut_pour_role($profile['role']);

            header('Location: ' . BASE_URL . '/index.php?route=' . $route);
            exit;
        }
    }
    }
}

require ROOT_PATH . '/vue/auth/register.php';
