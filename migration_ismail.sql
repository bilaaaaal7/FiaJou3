-- ============================================================
-- Migration FIAJOU3 - Module Admin / Cuisinier / Livreur (Ismail)
-- A exécuter UNE FOIS sur la base "fiajou3" (après fiajou3.sql)
-- ============================================================

-- 1) Colonne "actif" sur users (activer/désactiver un compte)
ALTER TABLE `users`
  ADD COLUMN `actif` TINYINT(1) NOT NULL DEFAULT 1 AFTER `password`;

-- 2) Affectation d'une commande à un cuisinier et/ou un livreur
ALTER TABLE `orders`
  ADD COLUMN `assigned_cook_id` INT(11) DEFAULT NULL AFTER `statut`,
  ADD COLUMN `assigned_driver_id` INT(11) DEFAULT NULL AFTER `assigned_cook_id`;

ALTER TABLE `orders`
  ADD CONSTRAINT `fk_order_cook` FOREIGN KEY (`assigned_cook_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_order_driver` FOREIGN KEY (`assigned_driver_id`) REFERENCES `users` (`id`);

-- 3) Le statut des commandes doit correspondre aux valeurs de l'ENUM
--    (le formulaire client envoyait auparavant "En attente" au lieu de
--    "en_attente", ce qui vidait la colonne -> on corrige les anciennes lignes)
UPDATE `orders`
SET `statut` = 'en_attente'
WHERE `statut` = '' OR `statut` IS NULL
   OR `statut` NOT IN ('en_attente','confirmee','en_preparation','prete','en_livraison','livree','annulee');

-- 4) Nettoyage des commentaires vides ("    ")
UPDATE `orders` SET `commentaire` = NULL WHERE TRIM(`commentaire`) = '';

-- 5) Champ commentaire sur l'historique des statuts (remarques cuisinier/livreur/admin)
ALTER TABLE `order_status_history`
  ADD COLUMN `commentaire` TEXT DEFAULT NULL AFTER `nouveau_statut`;

-- 6) Les comptes déjà en base restent "actif = 1" par défaut, rien à faire.
