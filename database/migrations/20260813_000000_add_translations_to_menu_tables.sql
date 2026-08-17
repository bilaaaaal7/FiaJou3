-- Migration FiaJou3
-- Date : 2026-08-13
-- Objet : Gestion multilingue réelle des données métier (FR / EN / AR).
--
-- Les colonnes `nom` / `description` existantes restent la valeur de base
-- (français) : aucune donnée existante n'est modifiée. Les nouvelles colonnes
-- `*_en` / `*_ar` contiennent les traductions optionnelles ; une traduction
-- vide ou absente retombe sur la valeur de base (voir localiser()).

ALTER TABLE `plats`
    ADD COLUMN `nom_en` VARCHAR(150) NULL AFTER `nom`,
    ADD COLUMN `nom_ar` VARCHAR(150) NULL AFTER `nom_en`,
    ADD COLUMN `description_en` TEXT NULL AFTER `description`,
    ADD COLUMN `description_ar` TEXT NULL AFTER `description_en`;

ALTER TABLE `categories`
    ADD COLUMN `nom_en` VARCHAR(100) NULL AFTER `nom`,
    ADD COLUMN `nom_ar` VARCHAR(100) NULL AFTER `nom_en`,
    ADD COLUMN `description_en` TEXT NULL AFTER `description`,
    ADD COLUMN `description_ar` TEXT NULL AFTER `description_en`;

-- Instantanés (snapshots) du menu de la semaine : chaque semaine garde aussi
-- ses propres traductions, indépendantes du plat réutilisable.
ALTER TABLE `weekly_menu_items`
    ADD COLUMN `nom_en` VARCHAR(150) NULL AFTER `nom`,
    ADD COLUMN `nom_ar` VARCHAR(150) NULL AFTER `nom_en`,
    ADD COLUMN `description_en` TEXT NULL AFTER `description`,
    ADD COLUMN `description_ar` TEXT NULL AFTER `description_en`;
