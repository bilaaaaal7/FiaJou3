<?php
/**
 * Contrôleur : Paramètres du compte (tous les rôles connectés)
 * Route : /parametres
 * Permet de modifier les informations personnelles, l'email et le mot de passe.
 */

exiger_connexion();

require_once ROOT_PATH . '/modele/UtilisateurModele.php';

$utilisateurModele = new UtilisateurModele();
$userId = (int) $_SESSION['user_id'];
$profil = $utilisateurModele->getProfilComplet($userId);

if (!$profil) {
    http_response_code(404);
    require ROOT_PATH . '/vue/errors/404.php';
    return;
}

$succes = '';
$erreur = '';

if (isset($_POST['modifier_infos'])) {
    $prenom    = trim($_POST['prenom'] ?? '');
    $nom       = trim($_POST['nom'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse   = trim($_POST['adresse'] ?? '');
    $ville     = trim($_POST['ville'] ?? '');

    if ($prenom === '' || $nom === '') {
        $erreur = 'Le prénom et le nom sont obligatoires.';
    } else {
        $utilisateurModele->mettreAJourProfil($userId, [
            'prenom'    => $prenom,
            'nom'       => $nom,
            'telephone' => $telephone,
            'adresse'   => $adresse,
            'ville'     => $ville,
        ]);

        $_SESSION['prenom'] = $prenom;
        $_SESSION['nom']    = $nom;

        $profil = $utilisateurModele->getProfilComplet($userId);
        $succes = 'Informations personnelles mises à jour avec succès.';
    }
}

if (isset($_POST['modifier_email'])) {
    $email = strtolower(trim($_POST['email'] ?? ''));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = 'Adresse email invalide.';
    } elseif (!$utilisateurModele->changerEmail($userId, $email)) {
        $erreur = 'Cette adresse email est déjà utilisée par un autre compte.';
    } else {
        $_SESSION['email'] = $email;
        $profil = $utilisateurModele->getProfilComplet($userId);
        $succes = 'Adresse email mise à jour avec succès.';
    }
}

if (isset($_POST['changer_mdp'])) {
    $ancienMdp       = $_POST['ancien_mdp'] ?? '';
    $nouveauMdp      = $_POST['nouveau_mdp'] ?? '';
    $confirmationMdp = $_POST['confirmation_mdp'] ?? '';

    $user = $utilisateurModele->findById($userId);

    if (empty($ancienMdp) || empty($nouveauMdp)) {
        $erreur = 'Veuillez remplir tous les champs de mot de passe.';
    } elseif (!$user || !password_verify($ancienMdp, $user['password'])) {
        $erreur = 'Le mot de passe actuel est incorrect.';
    } elseif (strlen($nouveauMdp) < 6) {
        $erreur = 'Le nouveau mot de passe doit contenir au moins 6 caractères.';
    } elseif ($nouveauMdp !== $confirmationMdp) {
        $erreur = 'Les mots de passe ne correspondent pas.';
    } else {
        $utilisateurModele->changerMdp($userId, $nouveauMdp);
        $succes = 'Mot de passe changé avec succès.';
    }
}

require ROOT_PATH . '/vue/parametres.php';
