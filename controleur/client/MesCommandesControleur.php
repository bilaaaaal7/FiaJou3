<?php
/**
 * Contrôleur : Mes commandes (client)
 * Route : /client/mes-commandes
 * (Le fichier original client/mes_commandes.php était vide ; il est
 *  implémenté ici pour que la commande passée soit consultable, comme
 *  le prévoyait déjà la redirection dans client/commander.php.)
 */

exiger_role(ROLE_CLIENT);

require_once ROOT_PATH . '/modele/CommandeModele.php';

$commandeModele = new CommandeModele();
$commandes = $commandeModele->getParUtilisateur((int) $_SESSION['user_id']);

require ROOT_PATH . '/vue/client/mes_commandes.php';
