<?php
exiger_role(ROLE_ADMIN);

require_once ROOT_PATH . '/modele/CategorieModele.php';
require_once ROOT_PATH . '/modele/PlatModele.php';
require_once ROOT_PATH . '/modele/UtilisateurModele.php';
require_once ROOT_PATH . '/modele/CommandeModele.php';
require_once ROOT_PATH . '/modele/ZoneModele.php';
require_once ROOT_PATH . '/modele/MenuSemaineModele.php';

$nbCategories = (new CategorieModele())->compter();
$nbPlats = (new PlatModele())->compter();
$nbUsers = (new UtilisateurModele())->compter();
$nbOrders = (new CommandeModele())->compter();
$nbClients = (new UtilisateurModele())->compterParRole(ROLE_CLIENT);
$nbCuisiniers = (new UtilisateurModele())->compterParRole(ROLE_CUISINIER);
$nbLivreurs = (new UtilisateurModele())->compterParRole(ROLE_LIVREUR);
$nbZones = (new ZoneModele())->compter();

$commandeModele = new CommandeModele();
$chiffreAffaires = $commandeModele->totalChiffreAffaires();
$commandesEnAttente = $commandeModele->compterParStatut('en_attente');
$commandesEnPreparation = $commandeModele->compterParStatut('en_preparation');
$commandesEnLivraison = $commandeModele->compterParStatut('en_livraison');
$commandesLivrees = $commandeModele->compterParStatut('livree');
$commandesAujourdHui = $commandeModele->commandesDuJour();
$produitsPopulaires = $commandeModele->produitsPlusCommandes(5);
$stats7Jours = $commandeModele->statistiquesParJour(7);
$prochainesLivraisons = $commandeModele->prochainesLivraisons(8);
$commandesEnRetard = $commandeModele->commandesEnRetard();

$statutRepartition = [];
foreach (STATUTS_COMMANDE as $cle => $label) {
    $statutRepartition[$cle] = $commandeModele->compterParStatut($cle);
}
$totalCommandesTousStatuts = array_sum($statutRepartition);

$menuActif = (new MenuSemaineModele())->getActif();

require ROOT_PATH . '/vue/admin/dashboard.php';
