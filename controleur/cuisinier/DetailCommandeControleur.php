<?php
/**
 * Contrôleur : Détail d'une commande (cuisinier)
 * Route : /cuisinier/detail-commande?id=X
 */

exiger_role(ROLE_CUISINIER);

require_once ROOT_PATH . '/modele/CommandeModele.php';
require_once ROOT_PATH . '/modele/HistoriqueModele.php';

$commandeModele = new CommandeModele();
$historiqueModele = new HistoriqueModele();
$cookId = (int) $_SESSION['user_id'];

$orderId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($orderId <= 0) {
    header('Location: ' . BASE_URL . '/index.php?route=cuisinier');
    exit;
}

$commande = $commandeModele->getParId($orderId);

if (!$commande) {
    header('Location: ' . BASE_URL . '/index.php?route=cuisinier');
    exit;
}

$assigned = $commande['assigned_cook_id'] ?? null;
$accessible = empty($assigned)
    || (int) $assigned === $cookId
    || in_array($commande['statut'], ['en_preparation', 'prete', 'en_livraison', 'livree'], true);

if (!$accessible) {
    header('Location: ' . BASE_URL . '/index.php?route=cuisinier');
    exit;
}

$items = $commandeModele->getItems($orderId);
$historique = $historiqueModele->getParOrder($orderId);

require ROOT_PATH . '/vue/cuisinier/detail_commande.php';
