<?php
/**
 * Contrôleur : Gestion des plats (admin)
 * Route : /admin/plats
 */

exiger_role(ROLE_ADMIN);

require_once ROOT_PATH . '/modele/PlatModele.php';
require_once ROOT_PATH . '/modele/CategorieModele.php';
require_once ROOT_PATH . '/modele/UploadModele.php';

$platModele = new PlatModele();
$categorieModele = new CategorieModele();

$nom = "";
$description = "";
$nomEn = "";
$nomAr = "";
$descriptionEn = "";
$descriptionAr = "";
$prix = "";
$image = "";
$disponible = 1;
$category_id = "";
$idModifier = "";
$error = "";

if (isset($_POST['ajouter'])) {
    $category_id = $_POST['category_id'];
    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $nomEn = trim($_POST['nom_en'] ?? '');
    $nomAr = trim($_POST['nom_ar'] ?? '');
    $descriptionEn = trim($_POST['description_en'] ?? '');
    $descriptionAr = trim($_POST['description_ar'] ?? '');
    $prix = $_POST['prix'];
    $disponible = $_POST['disponible'];

    $ancienne_image = $_POST['ancienne_image'] ?? "";

    try {
        $image = UploadModele::enregistrer($_FILES['image'] ?? []) ?? $ancienne_image;
    } catch (RuntimeException $e) {
        $error = $e->getMessage();
    }

    if (empty($error)) {
        $platModele->creer([
            'category_id' => $category_id,
            'nom' => $nom,
            'nom_en' => $nomEn,
            'nom_ar' => $nomAr,
            'description' => $description,
            'description_en' => $descriptionEn,
            'description_ar' => $descriptionAr,
            'prix' => $prix,
            'image' => $image,
            'disponible' => $disponible,
        ]);
        journaliser_audit('plat.creer', 'nom="' . $nom . '" prix=' . $prix);

        header('Location: ' . BASE_URL . '/index.php?route=admin/plats');
        exit;
    }
}

if (isset($_POST['modifier'])) {
    $id = (int) $_POST['id'];
    $category_id = $_POST['category_id'];
    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $nomEn = trim($_POST['nom_en'] ?? '');
    $nomAr = trim($_POST['nom_ar'] ?? '');
    $descriptionEn = trim($_POST['description_en'] ?? '');
    $descriptionAr = trim($_POST['description_ar'] ?? '');
    $prix = $_POST['prix'];
    $disponible = $_POST['disponible'];

    $plat = $platModele->getParId($id);
    $ancienneImage = $plat['image'] ?? "";

    try {
        $nouvelleImage = UploadModele::enregistrer($_FILES['image'] ?? []);
    } catch (RuntimeException $e) {
        $error = $e->getMessage();
        $idModifier = $id;
    }

    if (empty($error)) {
        $image = $nouvelleImage ?? $ancienneImage;

        if ($nouvelleImage !== null && $ancienneImage !== "") {
            UploadModele::supprimer($ancienneImage);
        }

        $platModele->mettreAJour($id, [
            'category_id' => $category_id,
            'nom' => $nom,
            'nom_en' => $nomEn,
            'nom_ar' => $nomAr,
            'description' => $description,
            'description_en' => $descriptionEn,
            'description_ar' => $descriptionAr,
            'prix' => $prix,
            'image' => $image,
            'disponible' => $disponible,
        ]);
        journaliser_audit('plat.modifier', 'id=' . $id . ' nom="' . $nom . '" prix=' . $prix . ' disponible=' . $disponible);

        header('Location: ' . BASE_URL . '/index.php?route=admin/plats');
        exit;
    }
}

if (isset($_GET['supprimer'])) {
    $succes = $platModele->supprimer((int) $_GET['supprimer']);

    if (!$succes) {
        $error = "Impossible de supprimer ce plat : il fait partie de commandes existantes. Vous pouvez le rendre indisponible à la place.";
    } else {
        journaliser_audit('plat.supprimer', 'id=' . (int) $_GET['supprimer']);
        header('Location: ' . BASE_URL . '/index.php?route=admin/plats');
        exit;
    }
}

if (isset($_GET['modifier'])) {
    $plat = $platModele->getParId((int) $_GET['modifier']);

    if ($plat) {
        $idModifier = $plat['id'];
        $category_id = $plat['category_id'];
        $nom = $plat['nom'];
        $nomEn = $plat['nom_en'] ?? '';
        $nomAr = $plat['nom_ar'] ?? '';
        $description = $plat['description'];
        $descriptionEn = $plat['description_en'] ?? '';
        $descriptionAr = $plat['description_ar'] ?? '';
        $prix = $plat['prix'];
        $image = $plat['image'];
        $disponible = $plat['disponible'];
    }
}

$plats = $platModele->getTous();
$categories = $categorieModele->getToutes();

require ROOT_PATH . '/vue/admin/plats.php';
