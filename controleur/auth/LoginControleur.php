<?php
/**
 * Contrôleur : Connexion
 * Route : /connexion
 */

require_once ROOT_PATH . '/modele/UtilisateurModele.php';
require_once ROOT_PATH . '/modele/RateLimiterModele.php';
require_once ROOT_PATH . '/assets/inc/langue.php';

$error = "";
$limiteur = new RateLimiterModele('connexion');
$blocageRestant = $limiteur->tempsRestantBlocage();

// Message flash à usage unique (ex : posé par MotDePasseOublieControleur
// après une réinitialisation de mot de passe réussie).
$flashSucces = $_SESSION['flash_succes'] ?? '';
unset($_SESSION['flash_succes']);

if (isset($_POST['login'])) {
    if (!$limiteur->peutTenter()) {
        $error = "Trop de tentatives échouées. Réessayez dans " . ceil($blocageRestant / 60) . " minute(s).";
    } else {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $utilisateurModele = new UtilisateurModele();
        $user = $utilisateurModele->findByEmail($email);

        if (!$user) {
            $limiteur->enregistrerEchec();
            $error = "Cet email n'existe pas.";
        } elseif (!$user['actif']) {
            $error = "Votre compte a été désactivé. Contactez l'administrateur.";
        } elseif (!password_verify($password, $user['password'])) {
            $limiteur->enregistrerEchec();
            $error = "Mot de passe incorrect.";
        } else {
            $limiteur->reinitialiser();
            $profile = $utilisateurModele->findProfileByUserId($user['id']);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['prenom']  = $profile['prenom'];
            $_SESSION['role']    = $profile['role'];
            $_SESSION['email']   = $email;

            // Langue du compte (profiles.langue) si définie, sinon celle
            // choisie sur le navigateur (cookie posé par le sélecteur).
            if (langue_valide($profile['langue'] ?? null)) {
                $_SESSION['langue'] = $profile['langue'];
            } elseif (langue_valide($_COOKIE['fiajou3_lang'] ?? null)) {
                $_SESSION['langue'] = $_COOKIE['fiajou3_lang'];
            }

            if ($profile['role'] === ROLE_ADMIN) {
                journaliser_audit('connexion.reussie', 'email="' . $email . '"');
            }

            // Si l'utilisateur venait d'un bouton « Commander » (retour
            // mémorisé), on le renvoie vers la page de commande initiale.
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

require ROOT_PATH . '/vue/auth/login.php';
