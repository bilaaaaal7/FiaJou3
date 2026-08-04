<?php
/**
 * Contrôleur : Détail d'une commande (livreur)
 * Route : /livreur/detail-commande?id=X
 */

exiger_role(ROLE_LIVREUR);

require_once ROOT_PATH . '/modele/CommandeModele.php';
require_once ROOT_PATH . '/modele/HistoriqueModele.php';

$commandeModele = new CommandeModele();
$historiqueModele = new HistoriqueModele();
$driverId = (int) $_SESSION['user_id'];

$orderId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($orderId <= 0) {
    header('Location: ' . BASE_URL . '/index.php?route=livreur');
    exit;
}

$commande = $commandeModele->getParId($orderId);

if (!$commande || (int) ($commande['assigned_driver_id'] ?? 0) !== $driverId) {
    header('Location: ' . BASE_URL . '/index.php?route=livreur');
    exit;
}

$items = $commandeModele->getItems($orderId);
$historique = $historiqueModele->getParOrder($orderId);

require ROOT_PATH . '/vue/livreur/detail_commande.php';
