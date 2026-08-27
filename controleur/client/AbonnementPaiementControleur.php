<?php
/**
 * Contrôleur : Paiement de l'abonnement (client)
 * Route : /client/abonnement/paiement
 *
 * Aucun payment gateway réel (Stripe/CMI) n'est configuré : ce contrôleur
 * valide rigoureusement les champs côté serveur puis enregistre le paiement
 * en MODE SANDBOX (test local). Il ne prétend JAMAIS qu'une carte a été
 * réellement débitée. Les données bancaires sensibles ne sont jamais stockées
 * (seuls le masque — 4 derniers chiffres — et la marque le sont).
 */

exiger_role(ROLE_CLIENT);

require_once ROOT_PATH . '/modele/AbonnementModele.php';
require_once ROOT_PATH . '/modele/SubscriptionPaiementModele.php';
require_once ROOT_PATH . '/config/config.php';

$userId = (int) $_SESSION['user_id'];
$abonnementModele = new AbonnementModele();
$abonnementModele->desactiverExpires();

// Un abonnement déjà actif : pas de nouveau paiement.
if ($abonnementModele->estActif($userId)) {
    header('Location: ' . BASE_URL . '/index.php?route=client/abonnement');
    exit;
}

$prix = defined('PRIX_ABONNEMENT_MENSUEL') ? (float) PRIX_ABONNEMENT_MENSUEL : 500.00;
$erreurs = [];

// ---------- Validation serveur (en plus de la validation frontend) ----------
function valider_carte(array $d): array
{
    $erreurs = [];

    $nom = trim((string) ($d['nom_carte'] ?? ''));
    $numero = preg_replace('/\s+/', '', (string) ($d['numero_carte'] ?? ''));
    $expiration = trim((string) ($d['expiration'] ?? ''));
    $cvv = trim((string) ($d['cvv'] ?? ''));

    if ($nom === '') {
        $erreurs[] = 'Le nom sur la carte est obligatoire.';
    } elseif (mb_strlen($nom) > 60) {
        $erreurs[] = 'Le nom sur la carte est trop long (60 caractères maximum).';
    }

    if (!preg_match('/^\d{13,19}$/', $numero)) {
        $erreurs[] = 'Le numéro de carte est invalide.';
    } elseif (!luhn_valide($numero)) {
        $erreurs[] = 'Le numéro de carte est invalide (échec du contrôle de Luhn).';
    }

    if (!preg_match('/^(0[1-9]|1[0-2])\/(\d{2})$/', $expiration, $m)) {
        $erreurs[] = 'La date d\'expiration doit être au format MM/AA.';
    } else {
        $moisExp = (int) $m[1];
        $anneeExp = 2000 + (int) $m[2];
        $maintenant = new DateTime();
        $finMois = new DateTime("$anneeExp-$moisExp");
        $finMois->modify('last day of this month 23:59:59');
        if ($finMois < $maintenant) {
            $erreurs[] = 'La carte est expirée.';
        }
    }

    if (!preg_match('/^\d{3,4}$/', $cvv)) {
        $erreurs[] = 'Le CVV doit contenir 3 à 4 chiffres.';
    }

    return $erreurs;
}

/**
 * Algorithme de Luhn (validation du numéro de carte) — valide le format.
 */
function luhn_valide(string $numero): bool
{
    $somme = 0;
    $inverser = false;
    for ($i = strlen($numero) - 1; $i >= 0; $i--) {
        $chiffre = (int) $numero[$i];
        if ($inverser) {
            $chiffre *= 2;
            if ($chiffre > 9) {
                $chiffre -= 9;
            }
        }
        $somme += $chiffre;
        $inverser = !$inverser;
    }
    return $somme % 10 === 0;
}

if (isset($_POST['paiement'])) {
    $erreurs = valider_carte($_POST);

    if (empty($erreurs)) {
        $montant = $prix;
        $numero = preg_replace('/\s+/', '', (string) ($_POST['numero_carte'] ?? ''));
        $last4 = substr($numero, -4);
        $brand = detecter_marque($numero);

        // 1. Activer l'abonnement pour l'utilisateur.
        $abonnementModele->creer($userId);

        // 2. Enregistrer le paiement en mode sandbox (données masquées).
        $paiementModele = new SubscriptionPaiementModele();
        $paiementModele->enregistrerSandbox($userId, $montant, $last4, $brand);

        // 3. Préparer la page de confirmation (une seule consultation).
        unset($_SESSION['paiement_valide']);
        $_SESSION['paiement_valide'] = true;

        header('Location: ' . BASE_URL . '/index.php?route=client/abonnement/confirmation');
        exit;
    }
}

/**
 * Détection de la marque de la carte à partir du numéro (préfixe BIN).
 */
function detecter_marque(string $numero): string
{
    if (preg_match('/^4/', $numero)) return 'Visa';
    if (preg_match('/^(5[1-5]|2[2-7])/', $numero)) return 'Mastercard';
    if (preg_match('/^3[47]/', $numero)) return 'Amex';
    return 'carte';
}

require ROOT_PATH . '/vue/client/abonnement_paiement.php';
