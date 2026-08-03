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
$quantites = $commandeModele->quantitesAProduire();
$nbAPreparer = count($commandesEnAttente);
$nbEnPreparation = count($commandesEnPreparation);

$itemsParCommande = [];
$allCommandes = array_merge($commandesEnAttente, $commandesEnPreparation);
foreach ($allCommandes as $cmd) {
    $itemsParCommande[$cmd['id']] = $commandeModele->getItems($cmd['id']);
}

require ROOT_PATH . '/vue/cuisinier/dashboard.php';
