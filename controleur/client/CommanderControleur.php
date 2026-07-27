<?php
/**
 * Contrôleur : Passer commande (client)
 * Route : /client/commander
 */

exiger_role(ROLE_CLIENT);

require_once ROOT_PATH . '/modele/PanierModele.php';
require_once ROOT_PATH . '/modele/ZoneModele.php';
require_once ROOT_PATH . '/modele/CommandeModele.php';
require_once ROOT_PATH . '/modele/HistoriqueModele.php';
require_once ROOT_PATH . '/modele/NotificationModele.php';

$panierModele = new PanierModele();

if ($panierModele->estVide()) {
    header('Location: ' . BASE_URL . '/index.php?route=client/panier');
    exit;
}

$erreurs = $panierModele->valider();
$zoneModele = new ZoneModele();
$zones = $zoneModele->getToutes();
$total = $panierModele->getTotal();

if (isset($_POST['commander'])) {
    $dateLivraison = $_POST['date_livraison'] ?? '';
    $heureLivraison = $_POST['heure_livraison'] ?? '';
    $zoneId = (int) ($_POST['zone_id'] ?? 0);

    $validationErreurs = [];

    if (empty($dateLivraison)) {
        $validationErreurs[] = "La date de livraison est obligatoire.";
    } elseif ($dateLivraison < date('Y-m-d')) {
        $validationErreurs[] = "La date de livraison ne peut pas être dans le passé.";
    }

    if (empty($heureLivraison)) {
        $validationErreurs[] = "L'heure de livraison est obligatoire.";
    }

    if ($dateLivraison === date('Y-m-d')) {
        $heureActuelle = date('H:i');
        if ($heureLivraison <= $heureActuelle) {
            $validationErreurs[] = "L'heure de livraison doit être dans le futur pour une commande aujourd'hui.";
        }
    }

    if ($dateLivraison === date('Y-m-d', strtotime('+1 day')) && $heureLivraison < '10:00') {
        $validationErreurs[] = "Les commandes pour demain doivent être passées avant 10h.";
    }

    if ($zoneId <= 0) {
        $validationErreurs[] = "Veuillez sélectionner une zone de livraison.";
    }

    $contenuBrut = $panierModele->getContenuBrut();
    if (empty($contenuBrut)) {
        $validationErreurs[] = "Votre panier est vide.";
    }

    $platModele = new PlatModele();
    foreach ($contenuBrut as $platId => $quantite) {
        $plat = $platModele->getParId((int) $platId);
        if (!$plat) {
            $validationErreurs[] = "Le plat #$platId n'existe plus.";
        } elseif (!$plat['disponible']) {
            $validationErreurs[] = "Le plat \"{$plat['nom']}\" n'est plus disponible.";
        } elseif ($quantite <= 0) {
            $validationErreurs[] = "Quantité invalide pour \"{$plat['nom']}\".";
        } elseif ($quantite > 20) {
            $validationErreurs[] = "Quantité maximale de 20 par plat pour \"{$plat['nom']}\".";
        }
    }

    if (!empty($validationErreurs)) {
        $erreurs = array_merge($erreurs, $validationErreurs);
    } else {
        $commandeModele = new CommandeModele();
        $priority = isset($_POST['priority']) ? 1 : 0;
        $pause = !empty($_POST['pause']) ? trim($_POST['pause']) : null;

        $orderId = $commandeModele->creerDepuisPanier(
            (int) $_SESSION['user_id'],
            $zoneId,
            $dateLivraison,
            $heureLivraison,
            $_POST['commentaire'] ?? '',
            $contenuBrut,
            $priority,
            $pause
        );

        $historiqueModele = new HistoriqueModele();
        $historiqueModele->ajouter($orderId, null, 'en_attente', 'Commande créée', (int) $_SESSION['user_id']);

        $notifModele = new NotificationModele();
        $notifModele->creer((int) $_SESSION['user_id'], 'Commande confirmée', 'Votre commande #' . $orderId . ' a été enregistrée avec succès.');

        $panierModele->vider();

        header('Location: ' . BASE_URL . '/index.php?route=client/detail-commande&id=' . $orderId);
        exit;
    }
}

require ROOT_PATH . '/vue/client/commander.php';
