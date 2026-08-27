<?php
/**
 * Contrôleur : Confirmation du paiement d'abonnement (client)
 * Route : /client/abonnement/confirmation
 *
 * Page de confirmation affichée UNIQUEMENT après un paiement (sandbox)
 * validé. Sans le repère de session posé par le contrôleur de paiement,
 * on redirige vers la page d'abonnement.
 */

exiger_role(ROLE_CLIENT);

require_once ROOT_PATH . '/modele/AbonnementModele.php';

$userId = (int) $_SESSION['user_id'];
$abonnementModele = new AbonnementModele();
$abonnementModele->desactiverExpires();

if (empty($_SESSION['paiement_valide'])) {
    header('Location: ' . BASE_URL . '/index.php?route=client/abonnement');
    exit;
}

// Repère consommé : la page de confirmation n'est consultable qu'une fois.
unset($_SESSION['paiement_valide']);

$abonnement = $abonnementModele->getActif($userId);

require ROOT_PATH . '/vue/client/abonnement_confirmation.php';
