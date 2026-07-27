<?php
/**
 * Contrôleur : Gestion des plats (admin)
 * Route : /admin/plats
 */

exiger_role(ROLE_ADMIN);

require_once ROOT_PATH . '/modele/PlatModele.php';
require_once ROOT_PATH . '/modele/CategorieModele.php';

$platModele = new PlatModele();
$categorieModele = new CategorieModele();

$nom = "";
$description = "";
$prix = "";
$image = "";
$disponible = 1;
$category_id = "";
$idModifier = "";
$error = "";

if (isset($_POST['ajouter'])) {
    $category_id = $_POST['category_id'];
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $disponible = $_POST['disponible'];

    $ancienne_image = $_POST['ancienne_image'];

    if ($_FILES['image']['name'] != "") {
        $image = $platModele->enregistrerImage($_FILES['image']);
    } else {
        $image = $ancienne_image;
    }

    $platModele->creer([
        'category_id' => $category_id,
        'nom' => $nom,
        'description' => $description,
        'prix' => $prix,
        'image' => $image,
        'disponible' => $disponible,
    ]);

    header('Location: ' . BASE_URL . '/index.php?route=admin/plats');
    exit;
}

if (isset($_POST['modifier'])) {
    $id = (int) $_POST['id'];
    $category_id = $_POST['category_id'];
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $disponible = $_POST['disponible'];

    $image = $platModele->enregistrerImage($_FILES['image']);

    $platModele->mettreAJour($id, [
        'category_id' => $category_id,
        'nom' => $nom,
        'description' => $description,
        'prix' => $prix,
        'image' => $image,
        'disponible' => $disponible,
    ]);

    header('Location: ' . BASE_URL . '/index.php?route=admin/plats');
    exit;
}

if (isset($_GET['supprimer'])) {
    $succes = $platModele->supprimer((int) $_GET['supprimer']);

    if (!$succes) {
        $error = "Impossible de supprimer ce plat : il fait partie de commandes existantes. Vous pouvez le rendre indisponible à la place.";
    } else {
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
        $description = $plat['description'];
        $prix = $plat['prix'];
        $image = $plat['image'];
        $disponible = $plat['disponible'];
    }
}

$plats = $platModele->getTous();
$categories = $categorieModele->getToutes();

require ROOT_PATH . '/vue/admin/plats.php';
