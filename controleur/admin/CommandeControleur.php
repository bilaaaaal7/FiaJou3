<?php
/**
 * Contrôleur : Gestion des commandes (admin)
 * Route : /admin/commandes
 */

exiger_role(ROLE_ADMIN);

require_once ROOT_PATH . '/modele/CommandeModele.php';
require_once ROOT_PATH . '/modele/NotificationModele.php';

$commandeModele = new CommandeModele();
$notifModele = new NotificationModele();

$statut = "";
$idModifier = "";
$commandeModifier = null;

if (isset($_GET['modifier'])) {
    $commandeModifier = $commandeModele->getParId((int) $_GET['modifier']);

    if ($commandeModifier) {
        $idModifier = $commandeModifier['id'];
        $statut = $commandeModifier['statut'];
    }
}

if (isset($_POST['modifierStatut'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $statut = $_POST['statut'] ?? '';
    $commandeActuelle = $commandeModele->getParId($id);

    if ($id <= 0 || !$commandeActuelle || !isset(STATUTS_COMMANDE[$statut])) {
        rediriger_avec_erreur('admin/commandes', 'Commande ou statut invalide.');
    }

    // Un passage « En préparation » exige un cuisinier, « En livraison » un livreur.
    if ($statut === 'en_preparation' && empty($_POST['cook_id'])) {
        header('Location: ' . BASE_URL . '/index.php?route=admin/commandes&modifier=' . $id . '&erreur=cook_obligatoire');
        exit;
    }
    if ($statut === 'en_livraison' && empty($_POST['driver_id'])) {
        header('Location: ' . BASE_URL . '/index.php?route=admin/commandes&modifier=' . $id . '&erreur=livreur_obligatoire');
        exit;
    }

    $nouveauCook = isset($_POST['cook_id']) ? (int) $_POST['cook_id'] : 0;
    $nouveauDriver = isset($_POST['driver_id']) ? (int) $_POST['driver_id'] : 0;

    if ($nouveauCook > 0 && (int) $commandeActuelle['assigned_cook_id'] !== $nouveauCook) {
        $commandeModele->affecterCuisinier($id, $nouveauCook);
        $notifModele->creer($nouveauCook, 'Nouvelle commande assignée', 'La commande #' . $id . ' vous a été assignée.');
    }
    if ($nouveauDriver > 0 && (int) $commandeActuelle['assigned_driver_id'] !== $nouveauDriver) {
        $commandeModele->affecterLivreur($id, $nouveauDriver);
        $notifModele->creer($nouveauDriver, 'Nouvelle livraison assignée', 'La commande #' . $id . ' vous a été assignée pour livraison.');
    }

    if ($statut !== $commandeActuelle['statut']) {
        $commandeModele->mettreAJourStatut($id, $statut);
        if ($statut === 'en_preparation' || $statut === 'en_livraison') {
            $notifModele->creer($commandeActuelle['user_id'], 'Commande #' . $id . ' en cours de traitement', 'Votre commande #' . $id . ' est en cours de traitement.');
        }
    }

    journaliser_audit('commande.statut', 'id=' . $id . ' nouveau_statut="' . $statut . '" cook_id=' . ($_POST['cook_id'] ?? '-') . ' driver_id=' . ($_POST['driver_id'] ?? '-'));

    header('Location: ' . BASE_URL . '/index.php?route=admin/commandes');
    exit;
}

$commandes = $commandeModele->getToutesAvecClient();
$cuisiniers = $commandeModele->getCuisiniersDisponibles();
$livreurs = $commandeModele->getLivreursDisponibles();

require ROOT_PATH . '/vue/admin/commandes.php';
