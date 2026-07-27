<?php
exiger_role(ROLE_ADMIN);

require_once ROOT_PATH . '/modele/MenuSemaineModele.php';
require_once ROOT_PATH . '/modele/PlatModele.php';

$menuModele = new MenuSemaineModele();
$platModele = new PlatModele();

if (isset($_POST['creer'])) {
    $nom = trim($_POST['nom']);
    if (!empty($nom)) {
        $menuModele->creer($nom);
    }
    header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine');
    exit;
}

if (isset($_GET['publier'])) {
    $menuModele->mettreAJourStatut((int) $_GET['publier'], 'publie');
    header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine');
    exit;
}

if (isset($_GET['archiver'])) {
    $menuModele->mettreAJourStatut((int) $_GET['archiver'], 'archive');
    header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine');
    exit;
}

if (isset($_GET['supprimer'])) {
    $menuModele->supprimer((int) $_GET['supprimer']);
    header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine');
    exit;
}

if (isset($_POST['ajouter_item'])) {
    $menuModele->ajouterItem((int) $_POST['menu_id'], (int) $_POST['product_id'], $_POST['jour']);
    header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine&voir=' . $_POST['menu_id']);
    exit;
}

if (isset($_GET['supprimer_item'])) {
    $menuId = $_GET['menu_id'] ?? '';
    $menuModele->supprimerItem((int) $_GET['supprimer_item']);
    if ($menuId) {
        header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine&voir=' . $menuId);
    } else {
        header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine');
    }
    exit;
}

$menus = $menuModele->getTous();
$plats = $platModele->getTous();
$menuActuel = null;
$itemsParJour = [];

if (isset($_GET['voir'])) {
    $menuActuel = $menuModele->getParId((int) $_GET['voir']);
    if ($menuActuel) {
        $itemsParJour = $menuModele->getItemsParJour($menuActuel['id']);
    }
}

require ROOT_PATH . '/vue/admin/menu_semaine.php';
