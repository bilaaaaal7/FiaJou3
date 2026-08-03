<?php
/**
 * Contrôleur : Panier (client)
 * Route : /client/panier
 */

exiger_role(ROLE_CLIENT);

require_once ROOT_PATH . '/modele/PanierModele.php';

$panierModele = new PanierModele();

if (isset($_GET['plus'])) {
    $panierModele->augmenter((int) $_GET['plus']);
    header('Location: ' . BASE_URL . '/index.php?route=client/panier');
    exit;
}

if (isset($_GET['moins'])) {
    $panierModele->diminuer((int) $_GET['moins']);
    header('Location: ' . BASE_URL . '/index.php?route=client/panier');
    exit;
}

if (isset($_GET['supprimer'])) {
    $panierModele->retirer((int) $_GET['supprimer']);
    header('Location: ' . BASE_URL . '/index.php?route=client/panier');
    exit;
}

if (isset($_GET['vider'])) {
    $panierModele->vider();
    header('Location: ' . BASE_URL . '/index.php?route=client/panier');
    exit;
}

$details = $panierModele->getDetails();
$panier = $details['articles'];
$total = $details['total'];
$dateLivraison = $panierModele->getDate();

require ROOT_PATH . '/vue/client/panier.php';
