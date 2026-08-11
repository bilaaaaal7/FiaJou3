# Connexion avec Google (OAuth 2.0 / OpenID Connect) — FiaJou3

## Analyse préalable

- **Login classique** : `controleur/auth/LoginControleur.php` (email +
  `password_verify`), rendu par `vue/auth/login.php`. Rien n'a été réécrit :
  ce fichier a seulement été complété (variable `$googleActif`, lecture de
  `?erreur=`).
- **Inscription classique** : `controleur/auth/RegisterControleur.php` +
  `modele/UtilisateurModele::creerCompte()`. Inchangés — le flux Google est
  entièrement séparé, dans de nouveaux fichiers.
- **Utilisateurs** : `modele/UtilisateurModele.php`, tables `users`
  (email, password) et `profiles` (prenom, nom, telephone, adresse, ville,
  role, langue), jointes par `user_id`.
- **Sessions** : `assets/inc/session.php` (un seul `session_start()`) +
  `assets/inc/auth_guard.php` (`est_connecte()`, `exiger_role()`,
  `retour_connexion_valide()`...). Le flux Google réutilise ces mêmes
  fonctions et les mêmes clés `$_SESSION` (`user_id`, `prenom`, `role`,
  `email`, `langue`) que le login classique — aucune duplication.
- **Routes** : `urlRewrite.php`, switch `$route` -> `require` d'un
  contrôleur. Le projet n'utilise pas Composer (pas de `composer.json`) :
  le flux OAuth est donc implémenté avec cURL directement, sans
  bibliothèque tierce (`google/apiclient`, `league/oauth2-google`...).
- **Mot de passe** : `password_hash()` / `password_verify()` (PHP natif).
  Le mot de passe Google n'est **jamais** demandé ni transmis à
  l'application : Google ne fournit qu'un jeton d'accès limité, jamais le
  mot de passe du compte.

## 1. Fichiers créés

| Fichier | Rôle |
|---|---|
| `config/env.php` | Chargeur `.env` minimal (aucune dépendance Composer dans ce projet) |
| `config/google_oauth.php` | Constantes OAuth (Client ID/Secret/Redirect URI depuis `.env`, endpoints Google, scopes) |
| `modele/GoogleOAuthModele.php` | Appels HTTP vers Google (URL d'autorisation, échange code -> jeton, récupération du profil userinfo) via cURL |
| `controleur/auth/GoogleControleur.php` | Route `auth/google` : génère le `state` anti-CSRF et redirige vers Google |
| `controleur/auth/GoogleCallbackControleur.php` | Route `auth/google/callback` : vérifie `state`, échange le code, connecte/associe/crée le compte |
| `controleur/auth/GoogleCompleteControleur.php` | Route `auth/google/complete` : demande le téléphone (seule information obligatoire que Google ne fournit pas) pour un nouveau compte |
| `vue/auth/google_complete.php` | Formulaire de complément (prénom/nom/email en lecture seule, téléphone requis, adresse/ville optionnels) |
| `database/migrations/20260811_010000_add_google_id_to_users.sql` | Ajoute `users.google_id` (unique, nullable) et rend `users.password` nullable |
| `.env.example` | Modèle des variables d'environnement à copier en `.env` |

## 2. Fichiers modifiés

| Fichier | Modification |
|---|---|
| `modele/UtilisateurModele.php` | Ajout de `findByGoogleId()`, `associerGoogleId()`, `creerCompteGoogle()` — le reste (login classique, inscription classique) est inchangé |
| `controleur/auth/LoginControleur.php` | Expose `$googleActif` (bouton affiché seulement si `.env` est configuré) et lit `?erreur=` (même convention que les autres contrôleurs, ex. `cuisinier/CommandeControleur.php`) |
| `vue/auth/login.php` | Ajout du bouton « Continuer avec Google » sous le formulaire classique, avec le param `retour` propagé s'il était présent |
| `assets/css/auth.css` | Styles `.btn-google` / `.divider-diamond--or` (thème noir/or existant, dark mode, responsive) |
| `assets/js/i18n.js` | Ajout des clés `login.orDivider` / `login.googleBtn` en FR/EN/AR |
| `urlRewrite.php` | Ajout des 3 routes `auth/google`, `auth/google/callback`, `auth/google/complete` |
| `.gitignore` | Ajout de `.env` |
| `.htaccess` | Blocage de l'accès direct au fichier `.env` |

**Aucun fichier du login/de l'inscription classiques n'a été supprimé ou
réécrit** — le formulaire email + mot de passe fonctionne exactement comme
avant.

## 3. Modification SQL nécessaire

Exécuter la migration :
```sql
database/migrations/20260811_010000_add_google_id_to_users.sql
```
Elle :
- ajoute `users.google_id` (`VARCHAR(255) NULL`, `UNIQUE`) — un compte
  classique existant garde `google_id = NULL` tant qu'il ne se connecte pas
  via Google ;
- rend `users.password` nullable — un compte créé **directement** via
  Google (email inconnu) n'a pas de mot de passe FiaJou3 tant que
  l'utilisateur n'en définit pas un lui-même (ex. via « Mot de passe
  oublié »). Les comptes existants gardent leur `password` inchangé.

Aucune ligne existante n'est supprimée ni modifiée.

## 4. Variables `.env` nécessaires

Copier `.env.example` en `.env` à la racine du projet, puis renseigner :

```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://localhost/FiaJou3/index.php?route=auth/google/callback
```

`.env` est ignoré par Git (`.gitignore`) et son accès direct par navigateur
est bloqué (`.htaccess`). Tant que `GOOGLE_CLIENT_ID` /
`GOOGLE_CLIENT_SECRET` sont vides, le bouton Google reste **masqué** sur la
page de connexion (`google_oauth_configure()` dans
`config/google_oauth.php`) : le site continue de fonctionner normalement
sans configuration Google.

## 5. Créer les identifiants OAuth Google (étapes exactes)

1. Aller sur [Google Cloud Console](https://console.cloud.google.com/).
2. Créer un projet (ou en sélectionner un existant).
3. Menu **API et services** > **Écran de consentement OAuth** :
   - Type d'utilisateur : *Externe* (ou *Interne* si Google Workspace).
   - Renseigner le nom de l'application (« FiaJou3 »), l'email de support
     et l'email de contact développeur.
   - Scopes : laisser les scopes par défaut (`openid`, `email`, `profile`
     suffisent — ne rien ajouter d'autre).
   - En mode *Test*, ajouter les emails Google qui pourront se connecter
     pendant les tests (sinon Google refuse la connexion).
4. Menu **API et services** > **Identifiants** > **Créer des
   identifiants** > **ID client OAuth** :
   - Type d'application : **Application Web**.
   - Nom : « FiaJou3 - Local » (ou ce que vous voulez).
   - **URI de redirection autorisés** : ajouter exactement l'URL utilisée
     par `GOOGLE_REDIRECT_URI` (voir section 6 ci-dessous).
   - Cliquer sur **Créer** : Google affiche le **Client ID** et le
     **Client Secret**.
5. Copier ces deux valeurs dans `.env` (`GOOGLE_CLIENT_ID`,
   `GOOGLE_CLIENT_SECRET`). Ne jamais les coller ailleurs ni les commiter.

## 6. Redirect URI à configurer dans Google Cloud

L'URI doit correspondre **au caractère près** (schéma, hôte, port, chemin)
à celle utilisée par le serveur — c'est le même chemin que la route
`auth/google/callback` :

- **Local (XAMPP/WAMP/MAMP, projet dans `htdocs/FiaJou3`)** :
  ```
  http://localhost/FiaJou3/index.php?route=auth/google/callback
  ```
- **Production (exemple)** :
  ```
  https://www.fiajou3.com/index.php?route=auth/google/callback
  ```

Vous pouvez déclarer plusieurs URI de redirection dans le même Client ID
(une pour le local, une pour la production) : Google Cloud Console accepte
une liste, pas une seule valeur.

## 7. Tester en local avec localhost

1. Servir le projet via Apache/XAMPP à l'adresse
   `http://localhost/FiaJou3/` (mod_rewrite déjà actif, voir `.htaccess`).
2. Créer `.env` à partir de `.env.example` avec les vraies valeurs
   (`GOOGLE_REDIRECT_URI=http://localhost/FiaJou3/index.php?route=auth/google/callback`).
3. Exécuter la migration SQL (section 3).
4. Ouvrir `http://localhost/FiaJou3/index.php?route=connexion` : le bouton
   « Continuer avec Google » doit apparaître sous le formulaire.
5. Cliquer dessus : vous êtes redirigé vers l'écran de consentement Google
   (utiliser un compte de test ajouté à l'étape 3 de la section 5 si
   l'écran de consentement est encore en mode *Test*).
6. Après consentement :
   - **Email déjà inscrit sur FiaJou3** (créé via le formulaire classique)
     -> connexion directe, `google_id` associé automatiquement à ce
     compte (pas de doublon).
   - **Email inconnu** -> redirection vers `auth/google/complete` pour
     renseigner le téléphone, puis création du compte et connexion.
   - **Reconnexion Google ultérieure** (`google_id` déjà en base) ->
     connexion directe.
7. Vérifier dans `logs/audit_AAAA-MM-JJ.log` les lignes
   `connexion.google.reussie` / `connexion.google.associee` /
   `inscription.google`.

## 8. Sécurité — points vérifiés

- OAuth 2.0 / OpenID Connect officiel de Google, flux *Authorization Code*.
- `state` aléatoire (`random_bytes(32)`), vérifié avec `hash_equals()` sur
  le callback — protection CSRF.
- L'email est accepté uniquement si `email_verified = true` dans la
  réponse Google (sinon rejeté, sans créer ni connecter aucun compte).
- Le Client Secret n'existe que côté serveur (`config/google_oauth.php`,
  utilisé uniquement dans `GoogleOAuthModele::echangerCodeContreJetons()`)
  — jamais transmis au navigateur.
- Le mot de passe Google n'est jamais demandé ni reçu par l'application.
- Les erreurs (annulation, callback invalide, token invalide, email
  indisponible, erreur API Google) redirigent vers `/connexion` avec un
  message générique (`rediriger_avec_erreur()`) — aucun détail technique
  affiché à l'utilisateur ; le détail est journalisé via `error_log()`.

## Remarque indépendante (sécurité existante)

`config/email.php` contient actuellement une adresse Gmail et un mot de
passe d'application **en clair dans le code source**. Ce fichier n'a pas
été modifié dans le cadre de cette fonctionnalité, mais la même logique
`.env` mise en place ici (`config/env.php`) peut être réutilisée pour en
faire autant avec `EMAIL_SMTP_USER` / `EMAIL_SMTP_PASS` — recommandé,
surtout si ce dépôt est ou a été partagé/poussé sur un Git distant (dans ce
cas, penser aussi à régénérer ce mot de passe d'application Google).
