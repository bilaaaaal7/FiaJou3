<?php
/**
 * Contrôleur : Abonnement mensuel (client)
 * Route : /client/abonnement
 */

exiger_role(ROLE_CLIENT);

require_once ROOT_PATH . '/modele/AbonnementModele.php';

$abonnementModele = new AbonnementModele();
$userId = (int) $_SESSION['user_id'];

// Désactiver les abonnements expirés
$abonnementModele->desactiverExpires();

$abonnement = $abonnementModele->getActif($userId);
$abonnements = $abonnementModele->getParUtilisateur($userId);
$prix = defined('PRIX_ABONNEMENT_MENSUEL') ? PRIX_ABONNEMENT_MENSUEL : 500.00;
$erreur = '';
$succes = '';

// La souscription passe désormais par le parcours de paiement
// (route client/abonnement/paiement) : le bouton « Souscrire maintenant »
// redirige vers le formulaire de paiement au lieu de créer directement
// un abonnement sans paiement.

if (isset($_POST['annuler'])) {
    $subId = (int) ($_POST['subscription_id'] ?? 0);
    if ($abonnementModele->annuler($subId, $userId)) {
        $succes = 'Votre abonnement a été annulé.';
        $abonnement = $abonnementModele->getActif($userId);
        $abonnements = $abonnementModele->getParUtilisateur($userId);
    } else {
        $erreur = 'Impossible d\'annuler cet abonnement.';
    }
}

require ROOT_PATH . '/vue/client/abonnement.php';
