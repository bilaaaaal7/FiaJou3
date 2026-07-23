<?php
/**
 * Contrôleur : Gestion des utilisateurs (admin)
 * Route : /admin/utilisateurs
 */

exiger_role(ROLE_ADMIN);

require_once ROOT_PATH . '/modele/UtilisateurModele.php';

$utilisateurModele = new UtilisateurModele();

$prenom = "";
$nom = "";
$email = "";
$telephone = "";
$adresse = "";
$ville = "";
$role = "";
$idModifier = "";

$users = $utilisateurModele->getTousAvecProfil();

if (isset($_GET['modifier'])) {
    $user = $utilisateurModele->getByIdAvecProfil((int) $_GET['modifier']);

    if ($user) {
        $idModifier = $user['id'];
        $prenom = $user['prenom'];
        $nom = $user['nom'];
        $email = $user['email'];
        $telephone = $user['telephone'];
        $adresse = $user['adresse'];
        $ville = $user['ville'];
        $role = $user['role'];
    }
}

if (isset($_GET['supprimer'])) {
    $utilisateurModele->supprimer((int) $_GET['supprimer']);

    header('Location: ' . BASE_URL . '/index.php?route=admin/utilisateurs');
    exit;
}

if (isset($_POST['modifier'])) {
    $id = (int) $_POST['id'];

    $utilisateurModele->mettreAJour($id, [
        'prenom' => $_POST['prenom'],
        'nom' => $_POST['nom'],
        'email' => $_POST['email'],
        'telephone' => $_POST['telephone'],
        'adresse' => $_POST['adresse'],
        'ville' => $_POST['ville'],
        'role' => $_POST['role'],
    ]);

    header('Location: ' . BASE_URL . '/index.php?route=admin/utilisateurs');
    exit;
}

require ROOT_PATH . '/vue/admin/utilisateurs.php';
