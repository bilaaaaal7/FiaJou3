<?php
exiger_role(ROLE_LIVREUR);

require_once ROOT_PATH . '/modele/CommandeModele.php';
require_once ROOT_PATH . '/modele/HistoriqueModele.php';

$commandeModele = new CommandeModele();
$historiqueModele = new HistoriqueModele();
$driverId = (int) $_SESSION['user_id'];

$commandesLivrees = $commandeModele->getParLivreurEtStatut($driverId, 'livree');

require ROOT_PATH . '/vue/livreur/historique.php';
