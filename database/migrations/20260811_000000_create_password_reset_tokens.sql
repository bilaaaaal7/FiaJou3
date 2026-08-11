-- Migration FiaJou3
-- Date : 2026-08-11
-- Objet : Mot de passe oublié / Réinitialisation du mot de passe.
-- Reprend le même principe que `partenaire_invitations` (lien sécurisé,
-- temporaire, à usage unique, envoyé par email) appliqué à la
-- réinitialisation de mot de passe.
--
-- Colonnes :
--   - user_id   : compte concerné (FK vers users.id) ;
--   - email     : email utilisé pour la demande (traçabilité, cohérent avec
--                 partenaire_invitations) ;
--   - token     : jeton aléatoire (64 caractères hexadécimaux =
--                 bin2hex(random_bytes(32))), impossible à deviner ; le lien
--                 envoyé par email ne contient que ce jeton ;
--   - expire_le : date d'expiration du lien (temporaire) ;
--   - utilise   : 1 après que le mot de passe a été réinitialisé (jeton à
--                 usage unique, ne peut plus être rejoué) ;
--   - cree_le   : date de création, pour traçabilité/nettoyage.
-- Aucune donnée existante n'est modifiée : table purement additive.

CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `email` VARCHAR(190) NOT NULL,
    `token` VARCHAR(64) NOT NULL,
    `expire_le` DATETIME NOT NULL,
    `utilise` TINYINT(1) NOT NULL DEFAULT 0,
    `cree_le` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_token` (`token`),
    KEY `idx_email` (`email`),
    CONSTRAINT `fk_password_reset_tokens_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
