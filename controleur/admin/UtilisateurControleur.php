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
$roleUser = "";
$idModifier = "";
$error = "";

$users = $utilisateurModele->getClients();

if (isset($_GET['activer'])) {
    $utilisateurModele->setActif((int) $_GET['activer'], true);
    journaliser_audit('client.activer', 'id=' . (int) $_GET['activer']);
    header('Location: ' . BASE_URL . '/index.php?route=admin/utilisateurs');
    exit;
}

if (isset($_GET['desactiver'])) {
    $utilisateurModele->setActif((int) $_GET['desactiver'], false);
    journaliser_audit('client.desactiver', 'id=' . (int) $_GET['desactiver']);
    header('Location: ' . BASE_URL . '/index.php?route=admin/utilisateurs');
    exit;
}

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
        $roleUser = $user['role'];
    }
}

if (isset($_GET['supprimer'])) {
    $succes = $utilisateurModele->supprimer((int) $_GET['supprimer']);

    if (!$succes) {
        $error = "Impossible de supprimer cet utilisateur : il a des commandes ou données associées. Vous pouvez le désactiver à la place.";
    } else {
        journaliser_audit('utilisateur.supprimer', 'id=' . (int) $_GET['supprimer']);
        header('Location: ' . BASE_URL . '/index.php?route=admin/utilisateurs');
        exit;
    }
}

if (isset($_POST['ajouter'])) {
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $roleUser = $_POST['role'] ?? ROLE_CLIENT;
    $password = $_POST['password'] ?? '';

    if (empty($prenom) || empty($nom) || empty($email) || empty($password)) {
        $error = "Tous les champs obligatoires doivent être remplis.";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères.";
    } else {
        $existant = $utilisateurModele->findByEmail($email);
        if ($existant) {
            $error = "Cet email est déjà utilisé.";
        } else {
            $utilisateurModele->creerCompte([
                'prenom' => $prenom,
                'nom' => $nom,
                'email' => $email,
                'telephone' => $telephone,
                'adresse' => $adresse,
                'ville' => $ville,
                'role' => $roleUser,
                'password' => $password,
            ]);
            journaliser_audit('client.creer', 'email="' . $email . '"');
            header('Location: ' . BASE_URL . '/index.php?route=admin/utilisateurs&succes=1');
            exit;
        }
    }
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
    journaliser_audit('utilisateur.modifier', 'id=' . $id . ' role="' . $_POST['role'] . '" email="' . $_POST['email'] . '"');

    header('Location: ' . BASE_URL . '/index.php?route=admin/utilisateurs');
    exit;
}

require ROOT_PATH . '/vue/admin/utilisateurs.php';
