<?php
exiger_role(ROLE_CUISINIER);

require_once ROOT_PATH . '/modele/CommandeModele.php';
require_once ROOT_PATH . '/modele/HistoriqueModele.php';

$commandeModele = new CommandeModele();
$historiqueModele = new HistoriqueModele();
$cookId = (int) $_SESSION['user_id'];

if (isset($_POST['avancerStatut'])) {
    $id = (int) $_POST['id'];
    $nouveauStatut = $_POST['nouveau_statut'];
    $commentaire = trim($_POST['commentaire'] ?? '');

    $commande = $commandeModele->getParId($id);
    $ancienStatut = $commande ? $commande['statut'] : null;

    $commandeModele->mettreAJourStatut($id, $nouveauStatut);
    $historiqueModele->ajouter($id, $ancienStatut, $nouveauStatut, $commentaire, $cookId);

    header('Location: ' . BASE_URL . '/index.php?route=cuisinier/historique');
    exit;
}

$commandesLivrees = $commandeModele->getParCuisinier($cookId);
$commandesLivrees = array_filter($commandesLivrees, function($c) {
    return in_array($c['statut'], ['livree', 'prete', 'en_livraison']);
});

$activite = $historiqueModele->getParUser($cookId);

require ROOT_PATH . '/vue/cuisinier/historique.php';
