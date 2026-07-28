<?php
/**
 * Contrôleur : Gestion des catégories (admin)
 * Route : /admin/categories
 */

exiger_role(ROLE_ADMIN);

require_once ROOT_PATH . '/modele/CategorieModele.php';

$categorieModele = new CategorieModele();

$nom = "";
$description = "";
$image = "";
$idModifier = "";
$error = "";

if (isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    $categorieModele->creer($nom, $description, $image);

    header('Location: ' . BASE_URL . '/index.php?route=admin/categories');
    exit;
}

if (isset($_POST['modifier'])) {
    $id = (int) $_POST['id'];
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $image = "";

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];

        move_uploaded_file($tmp, ROOT_PATH . "/uploads/" . $image);
    }   

    if (!empty($image)) {
        move_uploaded_file($tmp, ROOT_PATH . '/uploads/' . $image);
    } else {
        $categorie = $categorieModele->getParId($id);
        $image = $categorie['image'];
    }

    $categorieModele->mettreAJour($id, $nom, $description, $image);

    header('Location: ' . BASE_URL . '/index.php?route=admin/categories');
    exit;
}

if (isset($_GET['supprimer'])) {
    $succes = $categorieModele->supprimer((int) $_GET['supprimer']);

    if (!$succes) {
        $error = "Impossible de supprimer cette catégorie : elle contient encore des plats. Supprimez ou déplacez d'abord ses plats.";
    } else {
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
