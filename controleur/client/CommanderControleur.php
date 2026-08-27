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
require_once ROOT_PATH . '/modele/SocieteModele.php';

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

// Calculer la remise si la commande couvre toute la semaine
$couvreSemaine = $menuSemaineModele->commandeCouvreSemaine($panierModele->getContenuBrut());
$remiseMontant = 0;
if ($couvreSemaine && defined('REMISE_SEMAINE_POURCENT')) {
    $remiseMontant = round($total * REMISE_SEMAINE_POURCENT / 100, 2);
}

// Détermination automatique de la zone de livraison à partir des coordonnées
// GPS transmises par le navigateur (recalculées et validées côté serveur pour
// empêcher toute falsification de la zone ou du prix côté client).
$zoneDetectee = false;
if (isset($_POST['lat'], $_POST['lng']) && $_POST['lat'] !== '' && $_POST['lng'] !== '') {
    $latPost = (float) $_POST['lat'];
    $lngPost = (float) $_POST['lng'];
    $zoneDetectee = $zoneModele->getZoneParCoordonnees($latPost, $lngPost);
}

$fraisLivraisonDefaut = 0;
$zoneIdDetecte = 0;
if ($zoneDetectee) {
    $zoneIdDetecte = (int) $zoneDetectee['id'];
    $fraisLivraisonDefaut = (float) $zoneDetectee['prix_livraison'];
}
$totalPayer = $total + $fraisLivraisonDefaut - $remiseMontant;

if (isset($_POST['commander'])) {
    $heureLivraison = $_POST['heure_livraison'] ?? '';
    $societeNom = trim((string) ($_POST['societe_nom'] ?? ''));

    $validationErreurs = [];

    if (empty($heureLivraison)) {
        $validationErreurs[] = "L'heure de livraison est obligatoire.";
    } elseif (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $heureLivraison)) {
        $validationErreurs[] = "L'heure de livraison est invalide.";
    }

    // La zone n'est JAMAIS acceptée depuis le client : elle est recalculée à
    // partir des coordonnées GPS reçues, ce qui rend le prix impossible à falsifier.
    $latPost = isset($_POST['lat']) && $_POST['lat'] !== '' ? (float) $_POST['lat'] : null;
    $lngPost = isset($_POST['lng']) && $_POST['lng'] !== '' ? (float) $_POST['lng'] : null;

    if ($latPost === null || $lngPost === null) {
        $validationErreurs[] = "Votre position géographique n'a pas pu être déterminée. Autorisez la localisation et réessayez.";
    } else {
        $zoneRecalculee = $zoneModele->getZoneParCoordonnees($latPost, $lngPost);
        if (!$zoneRecalculee) {
            $validationErreurs[] = "Aucune zone de livraison ne couvre votre position actuelle.";
            $zoneId = 0;
        } else {
            $zoneId = (int) $zoneRecalculee['id'];
        }
    }

    if ($societeNom === '') {
        $validationErreurs[] = "Veuillez indiquer le nom de votre société.";
    } elseif (mb_strlen($societeNom) > 150) {
        $validationErreurs[] = "Le nom de la société est trop long (150 caractères maximum).";
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

        // Vérifier si la commande couvre toute la semaine (lundi à vendredi = 5 jours)
        // pour appliquer la remise samedi
        $couvreSemaine = $menuSemaineModele->commandeCouvreSemaine($contenuBrut);

        $orderId = $commandeModele->creerDepuisPanier(
            (int) $_SESSION['user_id'],
            $zoneId,
            $heureLivraison,
            $_POST['commentaire'] ?? '',
            $contenuBrut,
            $priority,
            $pause,
            $couvreSemaine,
            0,
            $societeNom
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
