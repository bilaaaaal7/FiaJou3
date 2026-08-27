-- Migration FiaJou3
-- Date : 2026-08-25
-- Objet : Ajouter la gestion des sociétés pour les commandes.
--
-- 1. Créer la table `societes` (liste des sociétés disponibles)
-- 2. Ajouter `societe_id` dans `orders` (clé étrangère vers societes)
-- 3. Insérer quelques sociétés par défaut

-- ============================================================
-- 1. SOCIETES : liste des sociétés
-- ============================================================
CREATE TABLE IF NOT EXISTS `societes` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nom` VARCHAR(150) NOT NULL,
    `nom_en` VARCHAR(150) NULL,
    `nom_ar` VARCHAR(150) NULL,
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_societe_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 2. ORDERS : ajouter societe_id (nullable pour les commandes existantes)
-- ============================================================
ALTER TABLE `orders`
    ADD COLUMN `societe_id` INT UNSIGNED NULL AFTER `zone_id`;

ALTER TABLE `orders`
    ADD CONSTRAINT `orders_ibfk_societe` FOREIGN KEY (`societe_id`)
    REFERENCES `societes` (`id`) ON DELETE SET NULL;

-- ============================================================
-- 3. SOCIETES PAR DEFAUT
-- ============================================================
INSERT INTO `societes` (`nom`, `nom_en`, `nom_ar`, `active`) VALUES
    ('Personnel', 'Personal', 'شخصي', 1),
    ('Entreprise A', 'Company A', 'شركة أ', 1),
    ('Entreprise B', 'Company B', 'شركة ب', 1),
    ('Entreprise C', 'Company C', 'شركة ج', 1);
