<?php
/**
 * Contrôleur : Espace livreur
 * Route : /livreur
 * (Le fichier original livreur/index.php était vide. Il est implémenté
 *  ici avec le strict minimum : voir et clôturer les livraisons en cours.)
 */

exiger_role(ROLE_LIVREUR);

require_once ROOT_PATH . '/modele/CommandeModele.php';

$commandeModele = new CommandeModele();

if (isset($_POST['livrer'])) {
    $id = (int) $_POST['id'];
    $commandeModele->mettreAJourStatut($id, 'Livrée');

    header('Location: ' . BASE_URL . '/index.php?route=livreur');
    exit;
}

$commandesEnLivraison = $commandeModele->getParStatut('En livraison');

require ROOT_PATH . '/vue/livreur/dashboard.php';
