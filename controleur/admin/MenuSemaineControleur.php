<?php
exiger_role(ROLE_ADMIN);

require_once ROOT_PATH . '/modele/MenuSemaineModele.php';
require_once ROOT_PATH . '/modele/PlatModele.php';

$menuModele = new MenuSemaineModele();
$platModele = new PlatModele();

if (isset($_POST['creer'])) {
    $nom = trim($_POST['nom']);
    $weekStart = trim($_POST['week_start'] ?? '');
    $weekEnd = trim($_POST['week_end'] ?? '');

    if (empty($nom)) {
        header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine&erreur=nom');
        exit;
    }
    if ($weekStart !== '' && $weekEnd !== '' && $weekStart > $weekEnd) {
        header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine&erreur=dates');
        exit;
    }
    if ($weekStart !== '' && $weekEnd !== '') {
        $stmt = $menuModele->checkerChevauchement($weekStart, $weekEnd);
        if ($stmt) {
            header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine&erreur=chevauchement');
            exit;
        }
    }

    $menuModele->creer($nom, $weekStart !== '' ? $weekStart : null, $weekEnd !== '' ? $weekEnd : null);
    journaliser_audit('menu_semaine.creer', 'nom="' . $nom . '" semaine=' . ($weekStart ?: '-') . ' -> ' . ($weekEnd ?: '-'));
    header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine');
    exit;
}

if (isset($_GET['publier'])) {
    $menuModele->mettreAJourStatut((int) $_GET['publier'], 'publie');
    journaliser_audit('menu_semaine.publier', 'id=' . (int) $_GET['publier']);
    header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine');
    exit;
}

if (isset($_GET['archiver'])) {
    $menuModele->mettreAJourStatut((int) $_GET['archiver'], 'archive');
    journaliser_audit('menu_semaine.archiver', 'id=' . (int) $_GET['archiver']);
    header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine');
    exit;
}

if (isset($_GET['supprimer'])) {
    $menuModele->supprimer((int) $_GET['supprimer']);
    journaliser_audit('menu_semaine.supprimer', 'id=' . (int) $_GET['supprimer']);
    header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine');
    exit;
}

if (isset($_POST['ajouter_item'])) {
    $menuId = (int) $_POST['menu_id'];
    $productId = (int) $_POST['product_id'];
    $jour = $_POST['jour'];

    $platDejaPresent = $menuModele->platPresent($menuId, $productId);

    if ($platDejaPresent) {
        header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine&voir=' . $menuId . '&erreur=duplicat');
        exit;
    }

    $menuModele->ajouterItem($menuId, $productId, $jour);
    journaliser_audit('menu_semaine.ajouter_plat', 'menu_id=' . $menuId . ' product_id=' . $productId . ' jour="' . $jour . '"');
    header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine&voir=' . $menuId);
    exit;
}

if (isset($_GET['deplacer_item'])) {
    $menuId = (int) ($_GET['menu_id'] ?? 0);
    $decalage = (($_GET['direction'] ?? '') === 'descendre') ? 1 : -1;
    $menuModele->deplacerItem((int) $_GET['deplacer_item'], $decalage);
    journaliser_audit('menu_semaine.deplacer_plat', 'item_id=' . (int) $_GET['deplacer_item'] . ' direction=' . ($decalage > 0 ? 'descendre' : 'monter') . ' menu_id=' . $menuId);
    header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine&voir=' . $menuId);
    exit;
}

if (isset($_GET['supprimer_item'])) {
    $menuId = $_GET['menu_id'] ?? '';
    $menuModele->supprimerItem((int) $_GET['supprimer_item']);
    journaliser_audit('menu_semaine.supprimer_plat', 'item_id=' . (int) $_GET['supprimer_item'] . ' menu_id=' . ($menuId ?: '-'));
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
