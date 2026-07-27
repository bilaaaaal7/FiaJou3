<?php
exiger_role(ROLE_ADMIN);

require_once ROOT_PATH . '/modele/CommandeModele.php';
require_once ROOT_PATH . '/modele/NotificationModele.php';

$commandeModele = new CommandeModele();
$notifModele = new NotificationModele();
$message = '';

if (isset($_POST['assigner'])) {
    $orderId = (int) $_POST['order_id'];
    $commande = $commandeModele->getParId($orderId);

    if (!empty($_POST['cook_id'])) {
        $commandeModele->affecterCuisinier($orderId, (int) $_POST['cook_id']);
        $notifModele->creer((int) $_POST['cook_id'], 'Nouvelle commande assignée', 'La commande #' . $orderId . ' vous a été assignée.');
    }
    if (!empty($_POST['driver_id'])) {
        $commandeModele->affecterLivreur($orderId, (int) $_POST['driver_id']);
        $notifModele->creer((int) $_POST['driver_id'], 'Nouvelle livraison assignée', 'La commande #' . $orderId . ' vous a été assignée pour livraison.');
    }
    if ($commande) {
        $notifModele->creer($commande['user_id'], 'Commande #' . $orderId . ' assignée', 'Votre commande #' . $orderId . ' est en cours de traitement.');
    }

    header('Location: ' . BASE_URL . '/index.php?route=admin/assignation');
    exit;
}

$commandes = $commandeModele->getToutesAvecClient();
$cuisiniers = $commandeModele->getCuisiniersDisponibles();
$livreurs = $commandeModele->getLivreursDisponibles();

require ROOT_PATH . '/vue/admin/assignation.php';
