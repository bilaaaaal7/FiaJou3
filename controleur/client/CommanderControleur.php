<?php
/**
 * Contrôleur : Passer commande (client)
 * Route : /client/commander
 */

exiger_role(ROLE_CLIENT);

require_once ROOT_PATH . '/modele/PanierModele.php';
require_once ROOT_PATH . '/modele/ZoneModele.php';
require_once ROOT_PATH . '/modele/CommandeModele.php';

$panierModele = new PanierModele();

if ($panierModele->estVide()) {
    header('Location: ' . BASE_URL . '/index.php?route=client/panier');
    exit;
}

$zoneModele = new ZoneModele();
$zones = $zoneModele->getToutes();
$total = $panierModele->getTotal();

if (isset($_POST['commander'])) {
    $commandeModele = new CommandeModele();

    $commandeModele->creerDepuisPanier(
        (int) $_SESSION['user_id'],
        (int) $_POST['zone_id'],
        $_POST['date_livraison'],
        $_POST['heure_livraison'],
        $_POST['commentaire'],
        $panierModele->getContenuBrut()
    );

    $panierModele->vider();

    header('Location: ' . BASE_URL . '/index.php?route=client/mes-commandes');
    exit;
}

require ROOT_PATH . '/vue/client/commander.php';
