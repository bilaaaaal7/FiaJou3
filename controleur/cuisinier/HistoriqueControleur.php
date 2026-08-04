<?php
exiger_role(ROLE_CUISINIER);

require_once ROOT_PATH . '/modele/CommandeModele.php';

$commandeModele = new CommandeModele();
$cookId = (int) $_SESSION['user_id'];

$commandesLivrees = $commandeModele->getParCuisinier($cookId);
$commandesLivrees = array_filter($commandesLivrees, function($c) {
    return in_array($c['statut'], ['livree', 'prete', 'en_livraison']);
});

$activite = $historiqueModele->getParUser($cookId);

require ROOT_PATH . '/vue/cuisinier/historique.php';
