<?php
/**
 * Contrôleur : Menu (client)
 * Route : /client
 */

exiger_role(ROLE_CLIENT);

require_once ROOT_PATH . '/modele/PlatModele.php';
require_once ROOT_PATH . '/modele/PanierModele.php';
require_once ROOT_PATH . '/modele/MenuSemaineModele.php';

$panierModele = new PanierModele();
$menuSemaineModele = new MenuSemaineModele();

if (isset($_GET['ajouter'])) {
    $date = isset($_GET['date']) ? trim($_GET['date']) : null;
    $succes = $panierModele->ajouter((int) $_GET['ajouter'], $date);

    $params = 'route=client';
    if ($succes === 'ok') {
        $params .= '&panier=1';
    } else {
        $params .= '&erreur=' . urlencode($succes);
    }
    header('Location: ' . BASE_URL . '/index.php?' . $params);
    exit;
}

if (isset($_GET['date'])) {
    $date = trim($_GET['date']);
    [$dateOk, $dateErreur] = $menuSemaineModele->dateLivraisonValide($date);
    if ($dateOk) {
        $panierModele->setDate($date);
    }
}

$platModele = new PlatModele();
$plats = $platModele->getMenu();
$nombreArticles = $panierModele->nombreArticles();

$menu = $menuSemaineModele->getActif();
$itemsParJour = [];
if ($menu) {
    $itemsParJour = $menuSemaineModele->getItemsParJour((int) $menu['id']);
}

$dateCommandeParPlat = [];
foreach ($plats as $plat) {
    $dateCommandeParPlat[$plat['id']] = $menuSemaineModele->getDateCommandePourPlat((int) $plat['id']);
}

require ROOT_PATH . '/vue/client/menu.php';
