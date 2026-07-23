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

if (isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $image = $_POST['image'];

    $categorieModele->creer($nom, $description, $image);

    header('Location: ' . BASE_URL . '/index.php?route=admin/categories');
    exit;
}

if (isset($_POST['modifier'])) {
    $id = (int) $_POST['id'];
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $image = $_POST['image'];

    $categorieModele->mettreAJour($id, $nom, $description, $image);

    header('Location: ' . BASE_URL . '/index.php?route=admin/categories');
    exit;
}

if (isset($_GET['supprimer'])) {
    $categorieModele->supprimer((int) $_GET['supprimer']);

    header('Location: ' . BASE_URL . '/index.php?route=admin/categories');
    exit;
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
