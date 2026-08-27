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
    $succes = $panierModele->ajouter((int) $_GET['ajouter']);

    $params = 'route=client';
    if ($succes === 'ok') {
        $params .= '&panier=1';
    } else {
        $params .= '&erreur=' . urlencode($succes);
    }
    header('Location: ' . BASE_URL . '/index.php?' . $params);
    exit;
}

$platModele = new PlatModele();
$plats = $platModele->getMenu();
$nombreArticles = $panierModele->nombreArticles();

$menu = $menuSemaineModele->getActif();
$itemsParJour = [];
if ($menu) {
    $itemsParJour = $menuSemaineModele->getItemsParJour((int) $menu['id']);
}

$semaineRef = MenuSemaineModele::semaineReference($menu);
$ouvertParJour = [];
foreach (JOURS_LIVRAISON as $jour) {
    $date = MenuSemaineModele::datePourJour($jour, $semaineRef);
    $ouvertParJour[$jour] = $date ? $menuSemaineModele->dateLivraisonValide($date)[0] : false;
}

$libelleSemaine = '';
if ($menu && $menu['week_start'] && $menu['week_end']) {
    $libelleSemaine = MenuSemaineModele::libelleSemaine(
        $menu['week_start'],
        $menu['week_end'],
        $menu['numero'] !== null ? (int) $menu['numero'] : null
    );
}

$dateCommandeParPlat = [];
foreach ($plats as $plat) {
    $dateCommandeParPlat[$plat['id']] = $menuSemaineModele->getDateCommandePourPlat((int) $plat['id']);
}

require ROOT_PATH . '/vue/client/menu.php';
