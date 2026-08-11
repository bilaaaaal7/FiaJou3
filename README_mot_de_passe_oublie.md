# Mot de passe oublié / Réinitialisation — FiaJou3

## Analyse préalable

- **FiaJou3** : MVC PHP maison (routeur `urlRewrite.php`, PDO, `password_hash`/`password_verify`).
- **SyoManager** : projet Laravel (backend séparé) — architecture différente,
  repris uniquement pour le *principe* de sécurité (token temporaire, usage
  unique, message générique), pas pour le code.
- FiaJou3 avait déjà un flux quasi identique pour les invitations
  partenaires (`PartenaireInvitationModele`, `PartenaireDemandeControleur`,
  `PartenaireControleur`, `MailerModele`, `RateLimiterModele`). Ce flux a été
  repris à l'identique pour la réinitialisation de mot de passe.
- Une ébauche `MotDePasseOublieControleur.php` existait déjà (token en
  session, pas d'email, message révélant l'existence du compte) : elle a été
  entièrement réécrite.

## 1. Fichiers modifiés

| Fichier | Modification |
|---|---|
| `controleur/auth/MotDePasseOublieControleur.php` | Réécrit entièrement : rate limiting, token en BDD (au lieu de session), envoi d'email réel, message générique anti-énumération, gestion GET (ouverture du lien) et POST (demande + reset) |
| `vue/auth/mot_de_passe_oublie.php` | Adaptée au nouveau flux par token (plus de dépendance à `$_SESSION`), même thème noir/or, mêmes classes CSS |
| `controleur/auth/LoginControleur.php` | Ajout de la lecture d'un message flash de succès (posé après réinitialisation) |
| `vue/auth/login.php` | Affichage du message flash de succès |
| `config/email.php` | Ajout de `PASSWORD_RESET_DUREE` (durée de validité du lien : 1h) |
| `assets/js/i18n.js` | Ajout des clés `mdp.resetTitle` / `mdp.resetSubtitle` en FR/EN/AR |

## 2. Fichiers créés

| Fichier | Rôle |
|---|---|
| `modele/PasswordResetModele.php` | Gestion des tokens (création, recherche, invalidation, marquage "utilisé"), calqué sur `PartenaireInvitationModele` |
| `database/migrations/20260811_000000_create_password_reset_tokens.sql` | Table dédiée aux tokens de réinitialisation |

## 3. Modification SQL nécessaire

Exécuter la migration :
```sql
database/migrations/20260811_000000_create_password_reset_tokens.sql
```
Elle crée la table `password_reset_tokens` (token unique 64 caractères,
expiration, usage unique, FK vers `users`, `ON DELETE CASCADE`). Aucune table
existante n'est modifiée.

## 4. Configuration email nécessaire

Le système d'email **existant** (`modele/MailerModele.php`) est réutilisé tel
quel — aucun second système créé.

- En développement (par défaut, `EMAIL_ENABLED = false` dans
  `config/email.php`) : les emails ne sont pas envoyés réellement, ils sont
  journalisés dans `logs/emails/AAAA-MM-JJ.log`. Le flux est donc testable
  de bout en bout sans serveur SMTP.
- En production : passer `EMAIL_ENABLED = true` dans `config/email.php`, et
  soit configurer `sendmail` sur le serveur, soit activer
  `EMAIL_UTILISER_SMTP = true` et renseigner `EMAIL_SMTP_HOST` /
  `EMAIL_SMTP_PORT` / `EMAIL_SMTP_USER` / `EMAIL_SMTP_PASS`.
- `PASSWORD_RESET_DUREE` (nouvelle constante, `config/email.php`) contrôle la
  durée de validité du lien (3600 secondes = 1h par défaut).

## 5. Sécurité mise en place

- Token = `bin2hex(random_bytes(32))` → 64 caractères hexadécimaux,
  impossible à deviner.
- Expiration stockée en base (`expire_le`), vérifiée à chaque usage.
- Token à usage unique (`utilise`), invalidé après réinitialisation.
- Une seule demande active à la fois par compte (les précédentes sont
  invalidées avant d'en créer une nouvelle).
- Réponse **toujours identique** après une demande, que l'email corresponde
  ou non à un compte : *« Si cette adresse email correspond à un compte, un
  lien de réinitialisation vous sera envoyé. »*
- Rate limiting par IP sur la demande (`RateLimiterModele`, 5 tentatives /
  15 min, blocage 5 min), même mécanisme que la connexion et les demandes
  partenaires.
- Mot de passe re-haché avec `password_hash()` (même fonction que le reste
  de l'app, via `UtilisateurModele::changerMdp()` — inchangée).
- Le système de connexion/session existant n'est pas touché.

## 6. Parcours utilisateur final

1. Page **Connexion** → lien « Mot de passe oublié ? » (déjà présent dans
   `vue/auth/login.php`).
2. Page **Mot de passe oublié** → formulaire Email → « Envoyer le lien de
   réinitialisation ».
3. Email reçu avec un bouton « Réinitialiser mon mot de passe » → lien
   contenant uniquement le token (`/index.php?route=mot-de-passe-oublie&token=...`).
4. Ouverture du lien → formulaire **Nouveau mot de passe** + **Confirmation**
   (si le token est valide, non expiré, non utilisé).
5. Soumission → mot de passe mis à jour (hashé), token invalidé, message de
   succès, redirection automatique vers **Connexion**.

## 7. Vérification effectuée

Le code réel (`PasswordResetModele`, `UtilisateurModele`, `MailerModele`) a
été exécuté dans un harnais de test (SQLite en remplacement de MySQL) pour
valider bout en bout : génération/format du token, journalisation de
l'email, validité/expiration/usage unique du token, hachage et vérification
du nouveau mot de passe, non-révélation d'un email inconnu. Les 14
vérifications sont passées avec succès. `php -l` a également été exécuté
sans erreur sur tous les fichiers modifiés/créés.

**Aucune route existante n'a été modifiée** : `mot-de-passe-oublie` existait
déjà dans `urlRewrite.php` et pointe toujours vers le même contrôleur.
