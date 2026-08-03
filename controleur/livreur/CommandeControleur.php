<?php
/**
 * Contrôleur : Détail d'une commande (livreur)
 * Route : /livreur/commande?id=X
 */

exiger_role(ROLE_LIVREUR);

require_once ROOT_PATH . '/modele/CommandeModele.php';
require_once ROOT_PATH . '/modele/HistoriqueModele.php';
require_once ROOT_PATH . '/modele/NotificationModele.php';

$commandeModele = new CommandeModele();
$historiqueModele = new HistoriqueModele();
$driverId = (int) $_SESSION['user_id'];

$orderId = isset($_GET['id']) ? (int) $_GET['id'] : (int) ($_POST['id'] ?? 0);

if ($orderId <= 0) {
    rediriger_avec_erreur('livreur', "Identifiant de commande invalide.");
}

$commande = $commandeModele->getParId($orderId);

if (!$commande) {
    rediriger_avec_erreur('livreur', "Commande introuvable.");
}

if (!$commandeModele->estAccessibleParLivreur($commande, $driverId)) {
    rediriger_avec_erreur('livreur', "Vous n'avez pas accès à cette commande.");
}

$error = isset($_GET['erreur']) ? $_GET['erreur'] : '';

if (isset($_POST['demarrerLivraison'])) {
    $resultat = $commandeModele->changerStatutParRole($orderId, 'en_livraison', ROLE_LIVREUR, $driverId, 'Livraison démarrée');

    if (!$resultat['succes']) {
        rediriger_avec_erreur('livreur/commande&id=' . $orderId, $resultat['erreur']);
    }

    (new NotificationModele())->creer(
        $resultat['commande']['user_id'],
        'Commande #' . $orderId . ' en livraison',
        'Votre commande #' . $orderId . ' est en route vers vous.'
    );
    journaliser_audit('commande.statut', 'order_id=' . $orderId . ' nouveau_statut="en_livraison"');

    header('Location: ' . BASE_URL . '/index.php?route=livreur/commande&id=' . $orderId);
    exit;
}

if (isset($_POST['confirmerLivraison'])) {
    $commentaire = trim($_POST['commentaire'] ?? '');
    $resultat = $commandeModele->changerStatutParRole($orderId, 'livree', ROLE_LIVREUR, $driverId, $commentaire ?: 'Livrée');

    if (!$resultat['succes']) {
        rediriger_avec_erreur('livreur/commande&id=' . $orderId, $resultat['erreur']);
    }

    (new NotificationModele())->creer(
        $resultat['commande']['user_id'],
        'Commande #' . $orderId . ' livrée',
        'Votre commande #' . $orderId . ' a été livrée avec succès.'
    );
    journaliser_audit('commande.statut', 'order_id=' . $orderId . ' nouveau_statut="livree"');

    header('Location: ' . BASE_URL . '/index.php?route=livreur/commande&id=' . $orderId);
    exit;
}

// Recharge la commande au cas où le statut vient d'être modifié.
$commande = $commandeModele->getParId($orderId);
$items = $commandeModele->getItems($orderId);
$historique = $historiqueModele->getParOrder($orderId);

require ROOT_PATH . '/vue/livreur/commande.php';
