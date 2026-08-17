<?php
/**
 * Contrôleur : Page d'accueil publique
 * Route : /accueil
 * Accessible sans connexion. Les cuisiniers et livreurs connectés sont
 * renvoyés directement vers leur espace ; le client connecté et
 * l'administrateur (bouton « Retour à l'accueil » de la sidebar Admin)
 * peuvent consulter l'accueil public.
 */

if (est_connecte() && !in_array(utilisateur_role(), [ROLE_CLIENT, ROLE_ADMIN], true)) {
    header('Location: ' . BASE_URL . '/index.php?route=' . route_par_defaut_pour_role(utilisateur_role()));
    exit;
}

require_once ROOT_PATH . '/modele/MenuSemaineModele.php';
require_once ROOT_PATH . '/modele/PanierModele.php';

$panierNb = 0;
if (est_connecte() && utilisateur_role() === ROLE_CLIENT) {
    $panierNb = (new PanierModele())->nombreArticles();
}

$menuSemaineModele = new MenuSemaineModele();
$menu = $menuSemaineModele->getPublie();
$itemsParJour = [];

if ($menu) {
    $itemsParJour = $menuSemaineModele->getItemsParJour((int) $menu['id']);
}

require ROOT_PATH . '/vue/accueil.php';
