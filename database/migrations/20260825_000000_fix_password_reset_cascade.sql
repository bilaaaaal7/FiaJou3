-- Migration FiaJou3
-- Date : 2026-08-25
-- Objet : Supprimer le dernier ON DELETE CASCADE (password_reset_tokens).
-- Remplace ON DELETE CASCADE par ON DELETE SET NULL sur user_id afin de
-- respecter la contrainte globale du projet (aucun CASCADE autorisé).

ALTER TABLE `password_reset_tokens`
    DROP FOREIGN KEY `fk_password_reset_tokens_user`,
    ADD CONSTRAINT `fk_password_reset_tokens_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
