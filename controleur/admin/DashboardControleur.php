<?php
/**
 * Contrôleur : Tableau de bord admin
 * Route : /admin
 */

exiger_role(ROLE_ADMIN);

require_once ROOT_PATH . '/modele/CategorieModele.php';
require_once ROOT_PATH . '/modele/PlatModele.php';
require_once ROOT_PATH . '/modele/UtilisateurModele.php';
require_once ROOT_PATH . '/modele/CommandeModele.php';

$nbCategories = (new CategorieModele())->compter();
$nbPlats = (new PlatModele())->compter();
$nbUsers = (new UtilisateurModele())->compter();
$nbOrders = (new CommandeModele())->compter();

require ROOT_PATH . '/vue/admin/dashboard.php';
