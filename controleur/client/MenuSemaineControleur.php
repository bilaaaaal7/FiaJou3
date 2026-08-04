<?php
/**
 * Contrôleur : Menu de la semaine (client)
 * Route : /client/menu-semaine
 */

exiger_role(ROLE_CLIENT);

require_once ROOT_PATH . '/modele/MenuSemaineModele.php';

$menuSemaineModele = new MenuSemaineModele();
$menu = $menuSemaineModele->getActif();
$itemsParJour = [];

if ($menu) {
    $itemsParJour = $menuSemaineModele->getItemsParJour((int) $menu['id']);
}

$datesParJour = [];
$ouvertParJour = [];
foreach (JOURS_LIVRAISON as $jour) {
    $date = $menuSemaineModele->prochaineDatePourJour($jour);
    $datesParJour[$jour] = $date;
    [$ouvertParJour[$jour]] = $date ? $menuSemaineModele->dateLivraisonValide($date) : [false];
}

$itemsSamedi = [];
if ($menu) {
    foreach (JOURS_MENU as $jour) {
        foreach (($itemsParJour[$jour] ?? []) as $item) {
            $itemsSamedi[$item['product_id']] = $item;
        }
    }
}

require ROOT_PATH . '/vue/client/menu_semaine.php';
