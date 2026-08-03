<?php
exiger_role(ROLE_ADMIN);

require_once ROOT_PATH . '/modele/ZoneModele.php';

$zoneModele = new ZoneModele();
$message = '';
$erreur = '';

if (isset($_POST['ajouter'])) {
    $nom = trim($_POST['nom']);
    $prix = (float) $_POST['prix_livraison'];
    if (empty($nom) || $prix < 0) {
        $erreur = "Veuillez remplir tous les champs correctement.";
    } else {
        $zoneModele->creer($nom, $prix);
        journaliser_audit('zone.creer', 'nom="' . $nom . '" prix=' . $prix);
        header('Location: ' . BASE_URL . '/index.php?route=admin/zones');
        exit;
    }
}

if (isset($_POST['modifier'])) {
    $id = (int) $_POST['id'];
    $nom = trim($_POST['nom']);
    $prix = (float) $_POST['prix_livraison'];
    if (empty($nom) || $prix < 0) {
        $erreur = "Veuillez remplir tous les champs correctement.";
    } else {
        $zoneModele->mettreAJour($id, $nom, $prix);
        journaliser_audit('zone.modifier', 'id=' . $id . ' nom="' . $nom . '" prix=' . $prix);
        header('Location: ' . BASE_URL . '/index.php?route=admin/zones');
        exit;
    }
}

if (isset($_GET['supprimer'])) {
    $succes = $zoneModele->supprimer((int) $_GET['supprimer']);
    if (!$succes) {
        $erreur = "Impossible de supprimer cette zone : elle est utilisée par des commandes existantes.";
    } else {
        journaliser_audit('zone.supprimer', 'id=' . (int) $_GET['supprimer']);
        header('Location: ' . BASE_URL . '/index.php?route=admin/zones');
        exit;
    }
}

$zones = $zoneModele->getToutes();
$idModifier = '';
$nom = '';
$prix = '';

if (isset($_GET['modifier'])) {
    $zone = $zoneModele->getParId((int) $_GET['modifier']);
    if ($zone) {
        $idModifier = $zone['id'];
        $nom = $zone['nom'];
        $prix = $zone['prix_livraison'];
    }
}

require ROOT_PATH . '/vue/admin/zones.php';
