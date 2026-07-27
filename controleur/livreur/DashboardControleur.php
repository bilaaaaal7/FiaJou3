<?php
exiger_role(ROLE_LIVREUR);

require_once ROOT_PATH . '/modele/CommandeModele.php';
require_once ROOT_PATH . '/modele/HistoriqueModele.php';
require_once ROOT_PATH . '/modele/NotificationModele.php';

$commandeModele = new CommandeModele();
$historiqueModele = new HistoriqueModele();
$driverId = (int) $_SESSION['user_id'];

if (isset($_POST['demarrerLivraison'])) {
    $id = (int) $_POST['id'];
    $commande = $commandeModele->getParId($id);
    $ancienStatut = $commande ? $commande['statut'] : null;
    $commandeModele->mettreAJourStatut($id, 'en_livraison');
    $historiqueModele->ajouter($id, $ancienStatut, 'en_livraison', 'Livraison démarrée', $driverId);

    if ($commande) {
        $notifModele = new NotificationModele();
        $notifModele->creer($commande['user_id'], 'Commande #' . $id . ' en livraison', 'Votre commande #' . $id . ' est en route vers vous.');
    }

    header('Location: ' . BASE_URL . '/index.php?route=livreur');
    exit;
}

if (isset($_POST['confirmerLivraison'])) {
    $id = (int) $_POST['id'];
    $commentaire = trim($_POST['commentaire'] ?? '');
    $commande = $commandeModele->getParId($id);
    $ancienStatut = $commande ? $commande['statut'] : null;
    $commandeModele->mettreAJourStatut($id, 'livree');
    $historiqueModele->ajouter($id, $ancienStatut, 'livree', $commentaire ?: 'Livrée', $driverId);

    if ($commande) {
        $notifModele = new NotificationModele();
        $notifModele->creer($commande['user_id'], 'Commande #' . $id . ' livrée', 'Votre commande #' . $id . ' a été livrée avec succès.');
    }

    header('Location: ' . BASE_URL . '/index.php?route=livreur');
    exit;
}

if (isset($_POST['signalerProbleme'])) {
    $id = (int) $_POST['id'];
    $commentaire = trim($_POST['commentaire_probleme'] ?? '');
    if (!empty($commentaire)) {
        $commande = $commandeModele->getParId($id);
        $statutActuel = $commande ? $commande['statut'] : 'en_livraison';
        $historiqueModele->ajouter($id, $statutActuel, $statutActuel, 'Problème signalé : ' . $commentaire, $driverId);
    }
    header('Location: ' . BASE_URL . '/index.php?route=livreur');
    exit;
}

$commandesAPretee = $commandeModele->getParLivreurEtStatut($driverId, 'prete');
$commandesEnLivraison = $commandeModele->getParLivreurEtStatut($driverId, 'en_livraison');
$commandesLivreesAujourdHui = $commandeModele->commandesLivreesAujourdHui($driverId);

$nbAPretee = count($commandesAPretee);
$nbEnLivraison = count($commandesEnLivraison);
$nbLivrees = count($commandesLivreesAujourdHui);

$itemsParCommande = [];
$allCommandes = array_merge($commandesAPretee, $commandesEnLivraison);
foreach ($allCommandes as $cmd) {
    $itemsParCommande[$cmd['id']] = $commandeModele->getItems($cmd['id']);
}

$itemsLivrees = [];
foreach ($commandesLivreesAujourdHui as $cmd) {
    $itemsLivrees[$cmd['id']] = $commandeModele->getItems($cmd['id']);
}

require ROOT_PATH . '/vue/livreur/dashboard.php';
