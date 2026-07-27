<?php
/**
 * Contrôleur : Notifications
 * Route : /client/notifications
 * Accessible à tout utilisateur connecté (pas seulement les clients) :
 * chaque rôle reçoit des notifications (assignation, statut, etc.)
 * et le lien "Notifications" de la barre de navigation est commun à tous les rôles.
 */

exiger_connexion();

require_once ROOT_PATH . '/modele/NotificationModele.php';

$notifModele = new NotificationModele();
$userId = (int) $_SESSION['user_id'];

if (isset($_GET['marquer_tout_lu'])) {
    $notifModele->marquerToutLu($userId);
    header('Location: ' . BASE_URL . '/index.php?route=client/notifications');
    exit;
}

if (isset($_GET['marquer_lu'])) {
    $notifModele->marquerLu((int) $_GET['marquer_lu'], $userId);
    header('Location: ' . BASE_URL . '/index.php?route=client/notifications');
    exit;
}

$notifications = $notifModele->getParUser($userId);
$nbNonLues = $notifModele->compterNonLues($userId);

require ROOT_PATH . '/vue/client/notifications.php';
