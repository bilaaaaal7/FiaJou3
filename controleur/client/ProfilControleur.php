<?php
/**
 * Contrôleur : Profil client
 * Route : /client/profil
 */

exiger_role(ROLE_CLIENT);

require_once ROOT_PATH . '/modele/UtilisateurModele.php';

$utilisateurModele = new UtilisateurModele();
$userId = (int) $_SESSION['user_id'];
$profil = $utilisateurModele->getProfilComplet($userId);

$succes = '';
$erreur = '';

if (isset($_POST['modifier'])) {
    $prenom   = trim($_POST['prenom'] ?? '');
    $nom      = trim($_POST['nom'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse  = trim($_POST['adresse'] ?? '');
    $ville    = trim($_POST['ville'] ?? '');

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
        $succes  = 'Profil mis à jour avec succès.';
    }
}

if (isset($_POST['changer_mdp'])) {
    $ancienMdp       = $_POST['ancien_mdp'] ?? '';
    $nouveauMdp      = $_POST['nouveau_mdp'] ?? '';
    $confirmationMdp = $_POST['confirmation_mdp'] ?? '';

    $user = $utilisateurModele->findByEmail($profil['email']);

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

require ROOT_PATH . '/vue/client/profil.php';
