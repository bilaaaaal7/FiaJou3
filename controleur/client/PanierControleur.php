<?php
/**
 * Contrôleur : Panier (client)
 * Route : /client/panier
 */

exiger_role(ROLE_CLIENT);

require_once ROOT_PATH . '/modele/PanierModele.php';

$panierModele = new PanierModele();

$retour = isset($_GET['retour']) ? trim($_GET['retour']) : '';
if ($retour !== '' && preg_match('#^client[a-z0-9/_-]*$#i', $retour)) {
    $cibleRetour = BASE_URL . '/index.php?route=' . $retour . '&panier=1';
} else {
    $cibleRetour = BASE_URL . '/index.php?route=client/panier';
}

if (isset($_GET['plus'])) {
    $panierModele->augmenter((int) $_GET['plus']);
    header('Location: ' . $cibleRetour);
    exit;
}

if (isset($_GET['moins'])) {
    $panierModele->diminuer((int) $_GET['moins']);
    header('Location: ' . $cibleRetour);
    exit;
}

if (isset($_GET['supprimer'])) {
    $panierModele->retirer((int) $_GET['supprimer']);
    header('Location: ' . $cibleRetour);
    exit;
}

if (isset($_GET['vider'])) {
    $panierModele->vider();
    header('Location: ' . $cibleRetour);
    exit;
}

$details = $panierModele->getDetails();
$panier = $details['articles'];
$total = $details['total'];
$dateLivraison = $panierModele->getDate();

require ROOT_PATH . '/vue/client/panier.php';
