<?php
exiger_role(ROLE_ADMIN);

require_once ROOT_PATH . '/modele/ZoneModele.php';

$zoneModele = new ZoneModele();
$message = '';
$erreur = '';
$nom = '';
$prix = '';
$nomEn = '';
$nomAr = '';
$lat = '';
$lng = '';
$rayon = '';
$idModifier = '';

 if (isset($_POST['ajouter'])) {
    $nom = trim($_POST['nom']);
    $prix = (float) $_POST['prix_livraison'];
    $nomEn = trim($_POST['nom_en'] ?? '');
    $nomAr = trim($_POST['nom_ar'] ?? '');
    $lat = ($_POST['lat'] ?? '') !== '' ? (float) $_POST['lat'] : null;
    $lng = ($_POST['lng'] ?? '') !== '' ? (float) $_POST['lng'] : null;
    $rayon = ($_POST['rayon_km'] ?? '') !== '' ? (float) $_POST['rayon_km'] : null;
    if (empty($nom) || $prix < 0) {
        $erreur = "Veuillez remplir tous les champs correctement.";
    } else {
        $zoneModele->creer($nom, $prix, $nomEn, $nomAr, $lat, $lng, $rayon);
        journaliser_audit('zone.creer', 'nom="' . $nom . '" prix=' . $prix);
        header('Location: ' . BASE_URL . '/index.php?route=admin/zones&succes=1');
        exit;
    }
}

if (isset($_POST['modifier'])) {
    $id = (int) $_POST['id'];
    $nom = trim($_POST['nom']);
    $prix = (float) $_POST['prix_livraison'];
    $nomEn = trim($_POST['nom_en'] ?? '');
    $nomAr = trim($_POST['nom_ar'] ?? '');
    $lat = ($_POST['lat'] ?? '') !== '' ? (float) $_POST['lat'] : null;
    $lng = ($_POST['lng'] ?? '') !== '' ? (float) $_POST['lng'] : null;
    $rayon = ($_POST['rayon_km'] ?? '') !== '' ? (float) $_POST['rayon_km'] : null;
    if (empty($nom) || $prix < 0) {
        $erreur = "Veuillez remplir tous les champs correctement.";
        $idModifier = $id;
    } else {
        $zoneModele->mettreAJour($id, $nom, $prix, $nomEn, $nomAr, $lat, $lng, $rayon);
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

if (isset($_GET['modifier'])) {
    $zone = $zoneModele->getParId((int) $_GET['modifier']);
    if ($zone) {
        $idModifier = $zone['id'];
        $nom = $zone['nom'];
        $prix = $zone['prix_livraison'];
        $nomEn = $zone['nom_en'] ?? '';
        $nomAr = $zone['nom_ar'] ?? '';
        $lat = $zone['lat'] ?? '';
        $lng = $zone['lng'] ?? '';
        $rayon = $zone['rayon_km'] ?? '';
    }
}

require ROOT_PATH . '/vue/admin/zones.php';
