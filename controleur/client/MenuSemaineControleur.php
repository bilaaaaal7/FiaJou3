<?php
/**
 * Contrôleur : Menu de la semaine (client)
 * Route : /client/menu-semaine
 */

exiger_role(ROLE_CLIENT);

require_once ROOT_PATH . '/modele/MenuSemaineModele.php';

$menuSemaineModele = new MenuSemaineModele();
$menu = $menuSemaineModele->getPublie();
$itemsParJour = [];

if ($menu) {
    $itemsParJour = $menuSemaineModele->getItemsParJour((int) $menu['id']);
}

require ROOT_PATH . '/vue/client/menu_semaine.php';
