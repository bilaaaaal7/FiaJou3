<?php
/**
 * Contrôleur : Gestion des catégories (admin)
 * Route : /admin/categories
 */

exiger_role(ROLE_ADMIN);

require_once ROOT_PATH . '/modele/CategorieModele.php';
require_once ROOT_PATH . '/modele/UploadModele.php';

$categorieModele = new CategorieModele();

$nom = "";
$description = "";
$image = "";
$idModifier = "";
$error = "";

if (isset($_POST['ajouter'])) {
    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $image = trim($_POST['image'] ?? '');

    if (empty($error)) {
        $categorieModele->creer($nom, $description, $image);
        journaliser_audit('categorie.creer', 'nom="' . $nom . '"');

        header('Location: ' . BASE_URL . '/index.php?route=admin/categories');
        exit;
    }
}

if (isset($_POST['modifier'])) {
    $id = (int) $_POST['id'];
    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $categorie = $categorieModele->getParId($id);
    $image = $categorie['image'] ?? "";

    try {
        $nouvelleImage = UploadModele::enregistrer($_FILES['image'] ?? []);
    } catch (RuntimeException $e) {
        $error = $e->getMessage();
        $idModifier = $id;
    }

    if (empty($error)) {
        if ($nouvelleImage !== null) {
            UploadModele::supprimer($image);
            $image = $nouvelleImage;
        }

        $categorieModele->mettreAJour($id, $nom, $description, $image);
        journaliser_audit('categorie.modifier', 'id=' . $id . ' nom="' . $nom . '"');

        header('Location: ' . BASE_URL . '/index.php?route=admin/categories');
        exit;
    }
}

if (isset($_GET['supprimer'])) {
    $succes = $categorieModele->supprimer((int) $_GET['supprimer']);

    if (!$succes) {
        $error = "Impossible de supprimer cette catégorie : elle contient encore des plats. Supprimez ou déplacez d'abord ses plats.";
    } else {
        journaliser_audit('categorie.supprimer', 'id=' . (int) $_GET['supprimer']);
        header('Location: ' . BASE_URL . '/index.php?route=admin/categories');
        exit;
    }
}

if (isset($_GET['modifier'])) {
    $categorie = $categorieModele->getParId((int) $_GET['modifier']);

    if ($categorie) {
        $idModifier = $categorie['id'];
        $nom = $categorie['nom'];
        $description = $categorie['description'];
        $image = $categorie['image'];
    }
}

$categories = $categorieModele->getToutes();

require ROOT_PATH . '/vue/admin/categories.php';
