<?php
/**
 * Modèle GoogleOAuth
 * Implémentation « maison » du flux OAuth 2.0 / OpenID Connect Authorization
 * Code de Google, sans bibliothèque externe (le projet n'utilise pas
 * Composer). Trois responsabilités :
 *
 *   1. genererUrlAutorisation() : construit l'URL vers laquelle rediriger
 *      l'utilisateur pour qu'il autorise l'application sur son compte Google.
 *   2. echangerCodeContreJetons() : échange le "code" reçu sur le callback
 *      contre un access_token (appel serveur à serveur, avec le Client
 *      Secret — jamais exposé au frontend).
 *   3. recupererProfil() : appelle le endpoint OpenID Connect userinfo de
 *      Google avec l'access_token pour récupérer sub/email/prénom/nom/photo.
 *      On ne décode jamais nous-mêmes l'id_token : le profil est demandé
 *      directement à Google sur une connexion HTTPS, ce qui garantit son
 *      authenticité sans avoir à vérifier une signature JWT manuellement.
 *
 * Sécurité :
 *   - Le Client Secret ne sort jamais de ce fichier (jamais envoyé au
 *     navigateur, jamais dans une URL visible).
 *   - Le paramètre "state" (anti-CSRF) est généré et vérifié par les
 *     contrôleurs (GoogleControleur / GoogleCallbackControleur), pas ici.
 *   - Le mot de passe Google n'est à aucun moment demandé ni transmis à
 *     cette application : Google ne nous donne jamais accès au mot de passe
 *     du compte, seulement un jeton d'accès limité aux scopes demandés.
 */

require_once __DIR__ . '/../config/google_oauth.php';

class GoogleOAuthModele
{
    /**
     * Construit l'URL d'autorisation Google vers laquelle rediriger
     * l'utilisateur (étape 1 du flux Authorization Code).
     */
    public function genererUrlAutorisation(string $state): string
    {
        $params = [
            'client_id'              => GOOGLE_CLIENT_ID,
            'redirect_uri'           => GOOGLE_REDIRECT_URI,
            'response_type'          => 'code',
            'scope'                  => GOOGLE_OAUTH_SCOPES,
            'state'                  => $state,
            // Réaffiche l'écran de consentement Google et garantit un
            // email vérifié à jour à chaque connexion.
            'prompt'                 => 'select_account',
            'include_granted_scopes' => 'true',
        ];

        return GOOGLE_OAUTH_AUTH_URL . '?' . http_build_query($params);
    }

    /**
     * Échange le "code" d'autorisation contre un access_token, en appel
     * serveur à serveur (le Client Secret n'est utilisé qu'ici).
     *
     * @return array{access_token:string,id_token?:string}|false
     */
    public function echangerCodeContreJetons(string $code): array|false
    {
        $donnees = [
            'code'          => $code,
            'client_id'     => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'grant_type'    => 'authorization_code',
        ];

        $reponse = $this->requetePost(GOOGLE_OAUTH_TOKEN_URL, $donnees);

        if ($reponse === false || !isset($reponse['access_token'])) {
            return false;
        }

        return $reponse;
    }

    /**
     * Récupère le profil (sub, email, prénom, nom, photo) depuis le
     * endpoint OpenID Connect userinfo de Google, avec l'access_token.
     *
     * @return array{sub:string,email:string,email_verified:bool,given_name?:string,family_name?:string,name?:string,picture?:string}|false
     */
    public function recupererProfil(string $accessToken): array|false
    {
        $reponse = $this->requeteGet(GOOGLE_OAUTH_USERINFO_URL, $accessToken);

        if ($reponse === false || empty($reponse['sub']) || empty($reponse['email'])) {
            return false;
        }

        return $reponse;
    }

    /**
     * Requête POST x-www-form-urlencoded, réponse JSON attendue.
     */
    private function requetePost(string $url, array $donnees): array|false
    {
        if (!function_exists('curl_init')) {
            error_log('GoogleOAuthModele : extension cURL indisponible.');
            return false;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($donnees),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $corps = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $erreurCurl = curl_error($ch);
        curl_close($ch);

        if ($corps === false || $erreurCurl !== '') {
            error_log('GoogleOAuthModele (token) : erreur cURL — ' . $erreurCurl);
            return false;
        }

        $json = json_decode($corps, true);

        if ($code !== 200 || !is_array($json)) {
            error_log('GoogleOAuthModele (token) : réponse HTTP ' . $code . ' — ' . substr((string) $corps, 0, 300));
            return false;
        }

        return $json;
    }

    /**
     * Requête GET authentifiée par Bearer token, réponse JSON attendue.
     */
    private function requeteGet(string $url, string $accessToken): array|false
    {
        if (!function_exists('curl_init')) {
            error_log('GoogleOAuthModele : extension cURL indisponible.');
            return false;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $accessToken],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $corps = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $erreurCurl = curl_error($ch);
        curl_close($ch);

        if ($corps === false || $erreurCurl !== '') {
            error_log('GoogleOAuthModele (userinfo) : erreur cURL — ' . $erreurCurl);
            return false;
        }

        $json = json_decode($corps, true);

        if ($code !== 200 || !is_array($json)) {
            error_log('GoogleOAuthModele (userinfo) : réponse HTTP ' . $code . ' — ' . substr((string) $corps, 0, 300));
            return false;
        }

        return $json;
    }
}
