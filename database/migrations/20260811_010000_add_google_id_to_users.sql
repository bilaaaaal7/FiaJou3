-- Migration FiaJou3
-- Date : 2026-08-11
-- Objet : Connexion "Continuer avec Google" (OAuth 2.0 / OpenID Connect).
--
-- Colonnes ajoutées à `users` :
--   - google_id : identifiant Google unique (claim "sub" du profil OpenID
--                 Connect). NULL pour tous les comptes créés en email +
--                 mot de passe qui n'ont jamais utilisé Google. Unique :
--                 empêche qu'un même compte Google soit lié à deux comptes
--                 FiaJou3 différents.
--
-- Modification de `users.password` :
--   - passe de NOT NULL à NULL : un compte créé directement via Google n'a
--     jamais de mot de passe FiaJou3 (le mot de passe Google n'est jamais
--     demandé ni stocké). Le login classique (LoginControleur) continue de
--     fonctionner à l'identique pour tous les comptes qui ont un mot de
--     passe : password_verify() n'est simplement jamais appelé sur un
--     compte Google pur (google_id renseigné, password NULL).
--
-- Compatibilité avec les comptes existants (email + mot de passe) :
--   - Aucune ligne existante n'est supprimée ni modifiée par cette
--     migration ; les comptes existants gardent leur mot de passe et
--     `google_id` reste NULL tant qu'ils ne se connectent pas via Google.
--   - Si un utilisateur se connecte avec Google en utilisant le même email
--     qu'un compte existant, l'application associe google_id à ce compte
--     (voir UtilisateurModele::associerGoogleId) au lieu d'en créer un
--     nouveau : aucun doublon.

ALTER TABLE `users`
    ADD COLUMN `google_id` VARCHAR(255) NULL DEFAULT NULL AFTER `password`,
    ADD UNIQUE KEY `uniq_google_id` (`google_id`),
    MODIFY COLUMN `password` VARCHAR(255) NULL DEFAULT NULL;
