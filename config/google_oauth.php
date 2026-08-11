<?php
/**
 * Configuration OAuth 2.0 / OpenID Connect pour « Continuer avec Google ».
 *
 * Les identifiants sont lus depuis les variables d'environnement (fichier
 * .env à la racine, voir .env.example) — jamais codés en dur ici, et jamais
 * commités dans Git (.env est ignoré, voir .gitignore).
 *
 * GOOGLE_REDIRECT_URI doit correspondre EXACTEMENT (schéma, hôte, chemin)
 * à l'un des "URI de redirection autorisés" déclarés dans Google Cloud
 * Console pour ce Client ID. Voir README_google_oauth.md pour la procédure
 * complète.
 */

require_once __DIR__ . '/env.php';

define('GOOGLE_CLIENT_ID', env('GOOGLE_CLIENT_ID', ''));
define('GOOGLE_CLIENT_SECRET', env('GOOGLE_CLIENT_SECRET', ''));
define('GOOGLE_REDIRECT_URI', env('GOOGLE_REDIRECT_URI', BASE_URL . '/index.php?route=auth/google/callback'));

// Endpoints officiels Google (OpenID Connect Discovery — valeurs stables,
// documentées sur https://accounts.google.com/.well-known/openid-configuration)
define('GOOGLE_OAUTH_AUTH_URL', 'https://accounts.google.com/o/oauth2/v2/auth');
define('GOOGLE_OAUTH_TOKEN_URL', 'https://oauth2.googleapis.com/token');
define('GOOGLE_OAUTH_USERINFO_URL', 'https://openidconnect.googleapis.com/v1/userinfo');

// Scopes minimaux nécessaires : identité + email (aucun accès Drive, Agenda...)
define('GOOGLE_OAUTH_SCOPES', 'openid email profile');

/**
 * Le bouton Google ne doit être proposé que si les identifiants sont
 * configurés — évite un bouton cassé sur un environnement où .env n'a pas
 * encore été renseigné.
 */
function google_oauth_configure(): bool
{
    return GOOGLE_CLIENT_ID !== '' && GOOGLE_CLIENT_SECRET !== '';
}
