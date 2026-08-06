<?php
/**
 * Contrôleur : Détail d'une commande (client)
 * Route : /client/detail-commande?id=X
 */

exiger_role(ROLE_CLIENT);

require_once ROOT_PATH . '/modele/CommandeModele.php';
require_once ROOT_PATH . '/modele/HistoriqueModele.php';

$commandeModele = new CommandeModele();
$historiqueModele = new HistoriqueModele();

$orderId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($orderId <= 0) {
    header('Location: ' . BASE_URL . '/index.php?route=client/mes-commandes');
    exit;
}

$commande = $commandeModele->getParId($orderId);

if (!$commande || (int) $commande['user_id'] !== (int) $_SESSION['user_id']) {
    header('Location: ' . BASE_URL . '/index.php?route=client/mes-commandes');
    exit;
}

$items = $commandeModele->getItems($orderId);
$historique = $historiqueModele->getParOrder($orderId);

require ROOT_PATH . '/vue/client/detail_commande.php';