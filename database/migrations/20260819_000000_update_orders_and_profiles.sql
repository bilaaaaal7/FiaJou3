-- Migration FiaJou3
-- Date : 2026-08-19
-- Objet : Mise a jour schema commandes et profils.
--
-- 1. Rendre date_livraison nullable dans orders
-- 2. Ajouter la colonne remise dans orders
-- 3. Ajouter la colonne societe dans profiles
-- 4. Creer la table subscriptions (abonnement mensuel)
-- 5. Supprimer les DELETE CASCADE sur les cles etrangeres

-- ============================================================
-- 1. ORDERS : rendre date_livraison nullable + ajouter remise
-- ============================================================
ALTER TABLE `orders`
    MODIFY COLUMN `date_livraison` DATE NULL,
    ADD COLUMN `remise` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `total`;

-- ============================================================
-- 2. PROFILES : ajouter le champ societe (facultatif)
-- ============================================================
ALTER TABLE `profiles`
    ADD COLUMN `societe` VARCHAR(150) NULL AFTER `ville`;

-- ============================================================
-- 3. SUBSCRIPTIONS : abonnement mensuel
-- ============================================================
CREATE TABLE IF NOT EXISTS `subscriptions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `date_debut` DATE NOT NULL,
    `date_fin` DATE NOT NULL,
    `prix` DECIMAL(10,2) NOT NULL,
    `statut` VARCHAR(20) NOT NULL DEFAULT 'active',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_sub_user` (`user_id`),
    INDEX `idx_sub_statut` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 4. SUPPRIMER LES DELETE CASCADE
--    On convertit en RESTRICT ou SET NULL selon le contexte.
-- ============================================================

-- profiles.user_id : SET NULL (supprimer un user met le profil orphelin)
ALTER TABLE `profiles`
    DROP FOREIGN KEY `profiles_ibfk_1`,
    ADD CONSTRAINT `profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- order_items.order_id : RESTRICT (empecher suppression commande avec items)
ALTER TABLE `order_items`
    DROP FOREIGN KEY `order_items_ibfk_1`,
    ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE RESTRICT;

-- order_status_history.order_id : RESTRICT
ALTER TABLE `order_status_history`
    DROP FOREIGN KEY `order_status_history_ibfk_1`,
    ADD CONSTRAINT `order_status_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE RESTRICT;

-- order_status_history.user_id : SET NULL
ALTER TABLE `order_status_history`
    DROP FOREIGN KEY `order_status_history_ibfk_2`,
    ADD CONSTRAINT `order_status_history_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- orders.user_id : RESTRICT
ALTER TABLE `orders`
    DROP FOREIGN KEY `orders_ibfk_1`,
    ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

-- orders.zone_id : RESTRICT
ALTER TABLE `orders`
    DROP FOREIGN KEY `orders_ibfk_2`,
    ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`zone_id`) REFERENCES `delivery_zones` (`id`) ON DELETE RESTRICT;

-- weekly_menu_items.weekly_menu_id : RESTRICT
ALTER TABLE `weekly_menu_items`
    DROP FOREIGN KEY `weekly_menu_items_ibfk_1`,
    ADD CONSTRAINT `weekly_menu_items_ibfk_1` FOREIGN KEY (`weekly_menu_id`) REFERENCES `weekly_menus` (`id`) ON DELETE RESTRICT;

-- weekly_menu_items.product_id : RESTRICT
ALTER TABLE `weekly_menu_items`
    DROP FOREIGN KEY `weekly_menu_items_ibfk_2`,
    ADD CONSTRAINT `weekly_menu_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `plats` (`id`) ON DELETE RESTRICT;

-- plats.category_id : RESTRICT
ALTER TABLE `plats`
    DROP FOREIGN KEY `plats_ibfk_1`,
    ADD CONSTRAINT `plats_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT;

-- notifications.user_id : SET NULL
ALTER TABLE `notifications`
    DROP FOREIGN KEY `notifications_ibfk_1`,
    ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- partenaire_invitations.user_id : already SET NULL (verify)
-- password_reset_tokens : traité dans la migration 20260825 (ON DELETE SET NULL)
