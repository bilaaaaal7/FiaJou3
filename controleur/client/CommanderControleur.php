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
require_once ROOT_PATH . '/modele/MenuSemaineModele.php';
require_once ROOT_PATH . '/modele/UtilisateurModele.php';

$panierModele = new PanierModele();

if ($panierModele->estVide()) {
    header('Location: ' . BASE_URL . '/index.php?route=client/panier');
    exit;
}

$erreurs = $panierModele->valider();
$zoneModele = new ZoneModele();
$zones = $zoneModele->getToutes();
$total = $panierModele->getTotal();
$menuSemaineModele = new MenuSemaineModele();
$utilisateurModele = new UtilisateurModele();
$profil = $utilisateurModele->getProfilComplet((int) $_SESSION['user_id']);

if (isset($_POST['commander'])) {
    $dateLivraison = $_POST['date_livraison'] ?? '';
    $heureLivraison = $_POST['heure_livraison'] ?? '';
    $zoneId = (int) ($_POST['zone_id'] ?? 0);

    $validationErreurs = [];

    [$dateOk, $dateErreur] = $menuSemaineModele->dateLivraisonValide($dateLivraison);
    if (!$dateOk) {
        $validationErreurs[] = $dateErreur;
    }

    if (empty($heureLivraison)) {
        $validationErreurs[] = "L'heure de livraison est obligatoire.";
    } elseif (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $heureLivraison)) {
        $validationErreurs[] = "L'heure de livraison est invalide.";
    }

    if ($zoneId <= 0) {
        $validationErreurs[] = "Veuillez sélectionner une zone de livraison.";
    }

    $contenuBrut = $panierModele->getContenuBrut();
    if (empty($contenuBrut)) {
        $validationErreurs[] = "Votre panier est vide.";
    }

    $platsDuJour = $dateOk ? $menuSemaineModele->getPlatsPourDate($dateLivraison) : [];
    $idsDuJour = [];
    foreach ($platsDuJour as $platDuJour) {
        $idsDuJour[] = (int) $platDuJour['id'];
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
        } elseif (!in_array((int) $platId, $idsDuJour, true)) {
            $validationErreurs[] = "Le plat \"{$plat['nom']}\" n'est pas au menu de la semaine pour la date de livraison choisie.";
        }
    }

    $pause = null;
    $pauseDebut = $_POST['pause_debut'] ?? '';
    $pauseFin = $_POST['pause_fin'] ?? '';
    if ($pauseDebut !== '' && $pauseFin !== '') {
        if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $pauseDebut) ||
            !preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $pauseFin)) {
            $validationErreurs[] = "Les heures de pause sont invalides.";
        } elseif ($pauseDebut >= $pauseFin) {
            $validationErreurs[] = "L'heure de début de pause doit précéder l'heure de fin.";
        } else {
            $pause = $pauseDebut . '-' . $pauseFin;
        }
    }

    if (!empty($validationErreurs)) {
        $erreurs = array_merge($erreurs, $validationErreurs);
    } else {
        $commandeModele = new CommandeModele();
        $priority = !empty($_POST['priority']) ? 1 : 0;

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
