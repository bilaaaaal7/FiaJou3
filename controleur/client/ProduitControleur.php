<?php
/**
 * Contrôleur : Fiche produit (client)
 * Route : /client/produit?id=X
 */

exiger_role(ROLE_CLIENT);

require_once ROOT_PATH . '/modele/PlatModele.php';
require_once ROOT_PATH . '/modele/CategorieModele.php';
require_once ROOT_PATH . '/modele/PanierModele.php';

$platModele = new PlatModele();
$categorieModele = new CategorieModele();
$panierModele = new PanierModele();

$platId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$plat = $platId > 0 ? $platModele->getParId($platId) : false;

if (!$plat) {
    http_response_code(404);
    require ROOT_PATH . '/vue/errors/404.php';
    exit;
}

$categorie = $categorieModele->getParId((int) $plat['category_id']);
$nombreArticles = $panierModele->nombreArticles();

require ROOT_PATH . '/vue/client/produit.php';
