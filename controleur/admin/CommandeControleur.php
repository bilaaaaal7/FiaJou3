<?php
/**
 * Contrôleur : Gestion des commandes (admin)
 * Route : /admin/commandes
 */

exiger_role(ROLE_ADMIN);

require_once ROOT_PATH . '/modele/CommandeModele.php';

$commandeModele = new CommandeModele();

$statut = "";
$idModifier = "";

if (isset($_GET['modifier'])) {
    $commande = $commandeModele->getParId((int) $_GET['modifier']);

    if ($commande) {
        $idModifier = $commande['id'];
        $statut = $commande['statut'];
    }
}

if (isset($_POST['modifierStatut'])) {
    $id = (int) $_POST['id'];
    $statut = $_POST['statut'];

    $commandeModele->mettreAJourStatut($id, $statut);

    header('Location: ' . BASE_URL . '/index.php?route=admin/commandes');
    exit;
}

$commandes = $commandeModele->getToutesAvecClient();

require ROOT_PATH . '/vue/admin/commandes.php';
