<?php
/**
 * Contrôleur : Tableau de bord client
 * Route : /client/dashboard
 */

exiger_role(ROLE_CLIENT);

require_once ROOT_PATH . '/modele/CommandeModele.php';

$userId = (int) $_SESSION['user_id'];
$commandeModele = new CommandeModele();

$stats = $commandeModele->statsClient($userId);
$prochaineLivraison = $commandeModele->prochaineLivraison($userId);
$commandesRecentes = $commandeModele->getParUtilisateur($userId);
$commandesRecentes = array_slice($commandesRecentes, 0, 5);

require ROOT_PATH . '/vue/client/dashboard.php';
