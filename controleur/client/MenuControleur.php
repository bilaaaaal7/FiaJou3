<?php
/**
 * Contrôleur : Menu (client)
 * Route : /client
 */

exiger_role(ROLE_CLIENT);

require_once ROOT_PATH . '/modele/PlatModele.php';
require_once ROOT_PATH . '/modele/PanierModele.php';

$panierModele = new PanierModele();

if (isset($_GET['ajouter'])) {
    $succes = $panierModele->ajouter((int) $_GET['ajouter']);

    header('Location: ' . BASE_URL . '/index.php?route=client' . ($succes ? '' : '&erreur=indisponible'));
    exit;
}

$platModele = new PlatModele();
$plats = $platModele->getMenu();
$nombreArticles = $panierModele->nombreArticles();

require ROOT_PATH . '/vue/client/menu.php';
