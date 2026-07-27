<?php
/**
 * Contrôleur : Page d'accueil publique
 * Route : /accueil
 * Accessible sans connexion. Si l'utilisateur est déjà connecté,
 * on le renvoie directement vers son espace.
 */

if (est_connecte()) {
    header('Location: ' . BASE_URL . '/index.php?route=' . route_par_defaut_pour_role(utilisateur_role()));
    exit;
}

require_once ROOT_PATH . '/modele/MenuSemaineModele.php';

$menuSemaineModele = new MenuSemaineModele();
$menu = $menuSemaineModele->getPublie();
$itemsParJour = [];

if ($menu) {
    $itemsParJour = $menuSemaineModele->getItemsParJour((int) $menu['id']);
}

require ROOT_PATH . '/vue/accueil.php';
