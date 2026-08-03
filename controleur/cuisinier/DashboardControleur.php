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

<<<<<<< HEAD
    $commandeModele->mettreAJourStatut($id, $nouveauStatut);

    if ($nouveauStatut === 'en_preparation') {
        $commandeModele->affecterCuisinier($id, $cookId);
    }

    $historiqueModele->ajouter($id, $ancienStatut, $nouveauStatut, $commentaire ?: null, $cookId);

    if ($commande) {
        require_once ROOT_PATH . '/modele/NotificationModele.php';
        $notifModele = new NotificationModele();
        $labels = ['en_preparation' => 'en préparation', 'prete' => 'prête'];
        $label = $labels[$nouveauStatut] ?? $nouveauStatut;
        $notifModele->creer($commande['user_id'], 'Commande #' . $id, 'Votre commande #' . $id . ' est ' . $label . '.');
=======
    if (!$resultat['succes']) {
        rediriger_avec_erreur('cuisinier', $resultat['erreur']);
>>>>>>> a248b96b5b6991e846b092f7ae1bc06940314f43
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

require ROOT_PATH . '/vue/cuisinier/dashboard.php';
