<?php
/**
 * Contrôleur : Callback Google
 * Route : /auth/google/callback
 *
 * Reçoit le retour de Google après consentement (ou refus) de
 * l'utilisateur, avec les paramètres GET habituels du flux Authorization
 * Code : soit ?code=...&state=..., soit ?error=... (utilisateur ayant
 * annulé, ou toute autre erreur Google).
 *
 * Aucune page n'est rendue ici : ce contrôleur redirige toujours, soit
 * vers l'espace de l'utilisateur (connexion/inscription réussie), soit
 * vers /auth/google/complete (informations obligatoires manquantes), soit
 * vers /connexion avec un message d'erreur générique (jamais de détail
 * technique affiché à l'utilisateur — voir logs pour le détail).
 *
 * Réutilise entièrement le système de session existant (mêmes clés
 * $_SESSION que LoginControleur / RegisterControleur) : aucune duplication
 * du mécanisme d'authentification.
 */

require_once ROOT_PATH . '/config/google_oauth.php';
require_once ROOT_PATH . '/modele/GoogleOAuthModele.php';
require_once ROOT_PATH . '/modele/UtilisateurModele.php';
require_once ROOT_PATH . '/assets/inc/langue.php';

// Le "state" est à usage unique quel que soit le résultat, pour ne jamais
// pouvoir être rejoué.
$stateAttendu = $_SESSION['google_oauth_state'] ?? null;
unset($_SESSION['google_oauth_state']);

// --- 1) Utilisateur ayant annulé / erreur renvoyée directement par Google
if (isset($_GET['error'])) {
    rediriger_avec_erreur('connexion', "Connexion avec Google annulée.");
}

// --- 2) Callback invalide : state manquant/incorrect, ou code absent
$stateRecu = $_GET['state'] ?? null;
$code = $_GET['code'] ?? null;

if (!is_string($code) || $code === ''
    || !is_string($stateRecu) || $stateAttendu === null
    || !hash_equals($stateAttendu, $stateRecu)
) {
    rediriger_avec_erreur('connexion', "La connexion avec Google a échoué. Merci de réessayer.");
}

// --- 3) Échange du code contre un access_token (appel serveur à serveur)
$googleOAuth = new GoogleOAuthModele();
$jetons = $googleOAuth->echangerCodeContreJetons($code);

if ($jetons === false) {
    rediriger_avec_erreur('connexion', "La connexion avec Google a échoué. Merci de réessayer.");
}

// --- 4) Récupération du profil (sub, email, prénom, nom, photo)
$profilGoogle = $googleOAuth->recupererProfil($jetons['access_token']);

if ($profilGoogle === false) {
    rediriger_avec_erreur('connexion', "Impossible de récupérer votre profil Google. Merci de réessayer.");
}

$googleId = (string) $profilGoogle['sub'];
$email = filter_var((string) $profilGoogle['email'], FILTER_VALIDATE_EMAIL) ? (string) $profilGoogle['email'] : null;
$emailVerifie = filter_var($profilGoogle['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN);

if ($email === null || !$emailVerifie) {
    // On n'authentifie jamais avec un email non vérifié par Google : on ne
    // peut pas garantir qu'il appartient réellement à l'utilisateur.
    rediriger_avec_erreur('connexion', "Votre adresse email Google doit être vérifiée pour vous connecter.");
}

$prenom = trim((string) ($profilGoogle['given_name'] ?? ''));
$nom = trim((string) ($profilGoogle['family_name'] ?? ''));
if ($prenom === '' && $nom === '' && !empty($profilGoogle['name'])) {
    // Certains comptes Google ne renvoient pas given_name/family_name
    // séparément : on retombe sur le nom complet comme prénom.
    $prenom = trim((string) $profilGoogle['name']);
}

$utilisateurModele = new UtilisateurModele();

/**
 * Connecte l'utilisateur $user (déjà résolu) exactement comme
 * LoginControleur : mêmes clés de session, même gestion de la langue, du
 * retour mémorisé et de la route par défaut selon le rôle.
 */
$connecterUtilisateur = function (array $user) use ($utilisateurModele): void {
    $profile = $utilisateurModele->findProfileByUserId((int) $user['id']);

    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['prenom']  = $profile['prenom'];
    $_SESSION['role']    = $profile['role'];
    $_SESSION['email']   = $user['email'];

    if (langue_valide($profile['langue'] ?? null)) {
        $_SESSION['langue'] = $profile['langue'];
    } elseif (langue_valide($_COOKIE['fiajou3_lang'] ?? null)) {
        $_SESSION['langue'] = $_COOKIE['fiajou3_lang'];
    }

    $retour = $_SESSION['google_oauth_retour'] ?? null;
    unset($_SESSION['google_oauth_retour']);

    $cible = retour_connexion_valide($retour);
    if ($cible !== null) {
        header('Location: ' . BASE_URL . '/' . $cible);
        exit;
    }

    header('Location: ' . BASE_URL . '/index.php?route=' . route_par_defaut_pour_role($profile['role']));
    exit;
};

// --- 5) Compte déjà lié à ce Google ID : connexion directe
$userParGoogleId = $utilisateurModele->findByGoogleId($googleId);
if ($userParGoogleId) {
    if (!$userParGoogleId['actif']) {
        rediriger_avec_erreur('connexion', "Votre compte a été désactivé. Contactez l'administrateur.");
    }

    journaliser_audit('connexion.google.reussie', 'email="' . $email . '"');
    $connecterUtilisateur($userParGoogleId);
}

// --- 6) Email déjà utilisé par un compte email + mot de passe existant :
//        on associe Google à ce compte au lieu de créer un doublon.
$userParEmail = $utilisateurModele->findByEmail($email);
if ($userParEmail) {
    if (!$userParEmail['actif']) {
        rediriger_avec_erreur('connexion', "Votre compte a été désactivé. Contactez l'administrateur.");
    }

    $utilisateurModele->associerGoogleId((int) $userParEmail['id'], $googleId);
    journaliser_audit('connexion.google.associee', 'email="' . $email . '"');

    // Recharge le compte pour repartir avec google_id à jour (non
    // strictement nécessaire ici, mais évite toute divergence future).
    $userParEmail['google_id'] = $googleId;
    $connecterUtilisateur($userParEmail);
}

// --- 7) Aucun compte trouvé : nouvel utilisateur. Google ne fournit jamais
//        le téléphone (obligatoire dans profiles) : on mémorise le profil
//        Google en session (jamais de mot de passe dedans) et on redirige
//        vers l'étape de complétion.
$_SESSION['google_pending'] = [
    'google_id' => $googleId,
    'email'     => $email,
    'prenom'    => $prenom !== '' ? $prenom : 'Utilisateur',
    'nom'       => $nom,
];

header('Location: ' . BASE_URL . '/index.php?route=auth/google/complete');
exit;
