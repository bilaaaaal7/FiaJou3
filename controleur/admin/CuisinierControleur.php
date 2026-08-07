<?php
exiger_role(ROLE_ADMIN);

require_once ROOT_PATH . '/modele/UtilisateurModele.php';

$utilisateurModele = new UtilisateurModele();
$erreur = '';
$success = '';

if (isset($_POST['ajouter'])) {
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $password = $_POST['password'];

    if (empty($prenom) || empty($nom) || empty($email) || empty($password)) {
        $erreur = "Tous les champs obligatoires doivent être remplis.";
    } elseif (strlen($password) < 6) {
        $erreur = "Le mot de passe doit contenir au moins 6 caractères.";
    } else {
        $existant = $utilisateurModele->findByEmail($email);
        if ($existant) {
            $erreur = "Cet email est déjà utilisé.";
        } else {
            $utilisateurModele->creerComptePersonnel([
                'prenom' => $prenom, 'nom' => $nom, 'email' => $email,
                'telephone' => $telephone, 'password' => $password,
            ], ROLE_CUISINIER);
            journaliser_audit('cuisinier.creer', 'email="' . $email . '"');
            header('Location: ' . BASE_URL . '/index.php?route=admin/cuisiniers&succes=1');
            exit;
        }
    }
}

if (isset($_POST['modifier'])) {
    $id = (int) $_POST['id'];
    $utilisateurModele->mettreAJour($id, [
        'prenom' => $_POST['prenom'], 'nom' => $_POST['nom'],
        'email' => $_POST['email'], 'telephone' => $_POST['telephone'] ?? '',
        'adresse' => $_POST['adresse'] ?? '', 'ville' => $_POST['ville'] ?? '',
        'role' => ROLE_CUISINIER,
    ]);
    journaliser_audit('cuisinier.modifier', 'id=' . $id . ' email="' . $_POST['email'] . '"');
    header('Location: ' . BASE_URL . '/index.php?route=admin/cuisiniers');
    exit;
}

if (isset($_GET['activer'])) {
    $utilisateurModele->setActif((int) $_GET['activer'], true);
    journaliser_audit('cuisinier.activer', 'id=' . (int) $_GET['activer']);
    header('Location: ' . BASE_URL . '/index.php?route=admin/cuisiniers');
    exit;
}

if (isset($_GET['desactiver'])) {
    $utilisateurModele->setActif((int) $_GET['desactiver'], false);
    journaliser_audit('cuisinier.desactiver', 'id=' . (int) $_GET['desactiver']);
    header('Location: ' . BASE_URL . '/index.php?route=admin/cuisiniers');
    exit;
}

if (isset($_GET['supprimer'])) {
    $succes = $utilisateurModele->supprimer((int) $_GET['supprimer']);

    if (!$succes) {
        $erreur = "Impossible de supprimer ce cuisinier : il a des commandes ou données associées. Vous pouvez le désactiver à la place.";
    } else {
        journaliser_audit('cuisinier.supprimer', 'id=' . (int) $_GET['supprimer']);
        header('Location: ' . BASE_URL . '/index.php?route=admin/cuisiniers&supprime=1');
        exit;
    }
}

$cuisiniers = $utilisateurModele->getCuisiniers();
$idModifier = $idModifier ?? '';
$prenom = $prenom ?? '';
$nom = $nom ?? '';
$email = $email ?? '';
$telephone = $telephone ?? '';

if (isset($_GET['modifier'])) {
    $user = $utilisateurModele->getByIdAvecProfil((int) $_GET['modifier']);
    if ($user) {
        $idModifier = $user['id'];
        $prenom = $user['prenom'];
        $nom = $user['nom'];
        $email = $user['email'];
        $telephone = $user['telephone'];
    }
}

require ROOT_PATH . '/vue/admin/cuisiniers.php';
