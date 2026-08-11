<?php
/**
 * Contrôleur : Démarrage de la connexion Google
 * Route : /auth/google
 *
 * Aucune page n'est rendue ici : génère un jeton anti-CSRF ("state"),
 * le mémorise en session, puis redirige immédiatement vers l'écran de
 * consentement Google (OAuth 2.0 / OpenID Connect, Authorization Code).
 *
 * Le "retour" mémorisé (bouton "Commander" -> connexion -> retour) est
 * conservé exactement comme pour le login classique : voir
 * url_connexion_avec_retour() / retour_connexion_valide() dans
 * assets/inc/auth_guard.php.
 */

require_once ROOT_PATH . '/config/google_oauth.php';
require_once ROOT_PATH . '/modele/GoogleOAuthModele.php';

// Déjà connecté : inutile de repartir sur Google, comportement identique à
// une visite de /connexion déjà authentifiée.
if (est_connecte()) {
    header('Location: ' . BASE_URL . '/index.php?route=' . route_par_defaut_pour_role(utilisateur_role()));
    exit;
}

if (!google_oauth_configure()) {
    // Configuration absente (GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET non
    // renseignés dans .env) : on ne casse pas la page de connexion, on y
    // revient avec un message clair pour l'administrateur du site.
    rediriger_avec_erreur('connexion', "La connexion Google n'est pas encore configurée sur ce site.");
}

// Jeton anti-CSRF, vérifié à l'identique sur le callback (state).
$state = bin2hex(random_bytes(32));
$_SESSION['google_oauth_state'] = $state;

// Mémorise la destination de retour éventuelle (?retour=...) pour la
// restituer après le callback Google, comme le fait déjà le login classique.
$retourDemande = $_GET['retour'] ?? null;
if (is_string($retourDemande) && $retourDemande !== '') {
    $_SESSION['google_oauth_retour'] = $retourDemande;
} else {
    unset($_SESSION['google_oauth_retour']);
}

$googleOAuth = new GoogleOAuthModele();
header('Location: ' . $googleOAuth->genererUrlAutorisation($state));
exit;
