<?php
/**
 * Contrôleur : Espace cuisinier
 * Route : /cuisinier
 * (Le fichier original cuisinier/index.php était vide. Il est implémenté
 *  ici avec le strict minimum : voir et faire avancer les commandes en
 *  cuisine, en réutilisant le champ `statut` déjà défini dans le projet.)
 */

exiger_role(ROLE_CUISINIER);

require_once ROOT_PATH . '/modele/CommandeModele.php';

$commandeModele = new CommandeModele();

if (isset($_POST['avancerStatut'])) {
    $id = (int) $_POST['id'];
    $nouveauStatut = $_POST['nouveau_statut'];
    $commandeModele->mettreAJourStatut($id, $nouveauStatut);

    header('Location: ' . BASE_URL . '/index.php?route=cuisinier');
    exit;
}

$commandesEnAttente = $commandeModele->getParStatut('En attente');
$commandesEnPreparation = $commandeModele->getParStatut('En préparation');

require ROOT_PATH . '/vue/cuisinier/dashboard.php';
