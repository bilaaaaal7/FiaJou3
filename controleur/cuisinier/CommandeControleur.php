<?php
/**
 * Contrôleur : Détail d'une commande (cuisinier)
 * Route : /cuisinier/commande?id=X
 */

exiger_role(ROLE_CUISINIER);

require_once ROOT_PATH . '/modele/CommandeModele.php';
require_once ROOT_PATH . '/modele/HistoriqueModele.php';
require_once ROOT_PATH . '/modele/NotificationModele.php';

$commandeModele = new CommandeModele();
$historiqueModele = new HistoriqueModele();
$cookId = (int) $_SESSION['user_id'];

$orderId = isset($_GET['id']) ? (int) $_GET['id'] : (int) ($_POST['id'] ?? 0);

if ($orderId <= 0) {
    rediriger_avec_erreur('cuisinier', "Identifiant de commande invalide.");
}

$commande = $commandeModele->getParId($orderId);

if (!$commande) {
    rediriger_avec_erreur('cuisinier', "Commande introuvable.");
}

if (!$commandeModele->estAccessibleParCuisinier($commande, $cookId)) {
    rediriger_avec_erreur('cuisinier', "Vous n'avez pas accès à cette commande.");
}

$error = isset($_GET['erreur']) ? $_GET['erreur'] : '';

if (isset($_POST['avancerStatut'])) {
    $nouveauStatut = trim($_POST['nouveau_statut'] ?? '');
    $commentaire = trim($_POST['commentaire'] ?? '');

    $resultat = $commandeModele->changerStatutParRole($orderId, $nouveauStatut, ROLE_CUISINIER, $cookId, $commentaire);

    if (!$resultat['succes']) {
        rediriger_avec_erreur('cuisinier/commande&id=' . $orderId, $resultat['erreur']);
    }

    $labels = ['en_preparation' => 'en préparation', 'prete' => 'prête'];
    $label = $labels[$nouveauStatut] ?? $nouveauStatut;
    (new NotificationModele())->creer(
        $resultat['commande']['user_id'],
        'Commande #' . $orderId,
        'Votre commande #' . $orderId . ' est ' . $label . '.'
    );
    journaliser_audit('commande.statut', 'order_id=' . $orderId . ' nouveau_statut="' . $nouveauStatut . '"');

    header('Location: ' . BASE_URL . '/index.php?route=cuisinier/commande&id=' . $orderId);
    exit;
}

// Recharge la commande au cas où le statut vient d'être modifié.
$commande = $commandeModele->getParId($orderId);
$items = $commandeModele->getItems($orderId);
$historique = $historiqueModele->getParOrder($orderId);

require ROOT_PATH . '/vue/cuisinier/commande.php';
