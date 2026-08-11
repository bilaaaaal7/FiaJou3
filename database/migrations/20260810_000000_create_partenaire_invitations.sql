-- Migration FiaJou3
-- Date : 2026-08-10
-- Objet : Inscription partenaire (cuisinier / livreur) en 2 temps.
-- Le bouton "Je m'inscris" de la section "Rejoignez FiaJou3" ne pointe plus
-- vers le Register classique : il demande un email, envoie un lien sécurisé
-- et temporaire, puis le candidat complète son dossier via ce lien.
--
-- Cette table stocke les invitations / liens de complétion de dossier :
--   - email  : adresse utilisée pour demander le lien ;
--   - role   : type de partenariat sélectionné (cuisinier | livreur) ;
--   - token  : jeton aléatoire (64 caractères hexadécimaux) qui rend le lien
--              impossible à deviner et empêche toute modification d'URL ;
--   - expire_le : date d'expiration du lien (temporaire) ;
--   - utilise : 1 après que le dossier a été complété (lien à usage unique) ;
--   - user_id  : compte créé / rattaché quand le dossier est complété.
-- Aucune donnée existante n'est modifiée : table purement additive.

CREATE TABLE IF NOT EXISTS `partenaire_invitations` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(190) NOT NULL,
    `role` ENUM('cuisinier', 'livreur') NOT NULL,
    `token` VARCHAR(64) NOT NULL,
    `expire_le` DATETIME NOT NULL,
    `utilise` TINYINT(1) NOT NULL DEFAULT 0,
    `cree_le` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `user_id` INT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_token` (`token`),
    KEY `idx_email` (`email`),
    CONSTRAINT `fk_partenaire_invitations_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
