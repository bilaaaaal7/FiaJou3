<?php
exiger_role(ROLE_CUISINIER);

require_once ROOT_PATH . '/modele/CommandeModele.php';
require_once ROOT_PATH . '/modele/NotificationModele.php';

$commandeModele = new CommandeModele();
$cookId = (int) $_SESSION['user_id'];

if (isset($_POST['avancerStatut'])) {
    $id = (int) $_POST['id'];
    $nouveauStatut = trim($_POST['nouveau_statut'] ?? '');
    $commentaire = trim($_POST['commentaire'] ?? '');

    $resultat = $commandeModele->changerStatutParRole($id, $nouveauStatut, ROLE_CUISINIER, $cookId, $commentaire);

    if (!$resultat['succes']) {
        rediriger_avec_erreur('cuisinier', $resultat['erreur']);
    }

    $labels = ['en_preparation' => 'en préparation', 'prete' => 'prête'];
    $label = $labels[$nouveauStatut] ?? $nouveauStatut;
    (new NotificationModele())->creer(
        $resultat['commande']['user_id'],
        'Commande #' . $id,
        'Votre commande #' . $id . ' est ' . $label . '.'
    );

    header('Location: ' . BASE_URL . '/index.php?route=cuisinier');
    exit;
}

$commandesEnAttente = array_values(array_filter(
    $commandeModele->getParStatut('en_attente'),
    fn($c) => empty($c['assigned_cook_id']) || (int) $c['assigned_cook_id'] === $cookId
));
$commandesEnPreparation = array_values(array_filter(
    $commandeModele->getParStatut('en_preparation'),
    fn($c) => (int) $c['assigned_cook_id'] === $cookId
));

usort($commandesEnAttente, fn($a, $b) => [$b['priority'] ?? 0, $a['date_livraison'], $a['heure_livraison']]
    <=> [$a['priority'] ?? 0, $b['date_livraison'], $b['heure_livraison']]);
usort($commandesEnPreparation, fn($a, $b) => [$b['priority'] ?? 0, $a['date_livraison'], $a['heure_livraison']]
    <=> [$a['priority'] ?? 0, $b['date_livraison'], $b['heure_livraison']]);
$quantites = $commandeModele->quantitesAProduire();
$nbAPreparer = count($commandesEnAttente);
$nbEnPreparation = count($commandesEnPreparation);

$itemsParCommande = [];
$allCommandes = array_merge($commandesEnAttente, $commandesEnPreparation);
foreach ($allCommandes as $cmd) {
    $itemsParCommande[$cmd['id']] = $commandeModele->getItems($cmd['id']);
}

// Statistiques du cuisinier (commandes qui le concernent, aujourd'hui)
$commandesAujourdHui = $commandeModele->commandesAPreparerAujourdHui($cookId);
$nbCommandesAujourdHui = count($commandesAujourdHui);
$commandesPassees = $commandeModele->getParCuisinier($cookId);
$nbPreteesHistorique = count(array_filter(
    $commandesPassees,
    fn($c) => in_array($c['statut'], ['prete', 'en_livraison', 'livree'])
));

// État des commandes du jour (répartition)
$repartitionJour = ['en_attente' => 0, 'confirmee' => 0, 'en_preparation' => 0, 'prete' => 0];
foreach ($commandesAujourdHui as $c) {
    if (isset($repartitionJour[$c['statut']])) {
        $repartitionJour[$c['statut']]++;
    }
}

// Notifications + activité récente
require_once ROOT_PATH . '/modele/HistoriqueModele.php';
$historiqueModele = new HistoriqueModele();
$activiteRecente = $historiqueModele->getParUser($cookId, 5);
$notifications = (new NotificationModele())->getParUser($cookId);
$notificationsRecentes = array_slice($notifications, 0, 5);
$nbNotifsNonLues = (new NotificationModele())->compterNonLues($cookId);

require ROOT_PATH . '/vue/cuisinier/dashboard.php';
