-- Migration FiaJou3
-- Date : 2026-08-05
-- Objet : Menu de la semaine - indépendance des semaines.
-- Ajoute à chaque ligne du menu un "instantané" (snapshot) des attributs du
-- plat tels qu'affichés dans CETTE semaine : nom, description, prix et
-- catégorie. L'admin peut ainsi modifier l'entrée d'une semaine sans jamais
-- toucher au plat réutilisable (`plats`), qui reste partagé entre toutes les
-- semaines. Les lignes existantes gardent NULL : elles retombent sur les
-- valeurs du plat (aucune donnée existante n'est modifiée).

ALTER TABLE `weekly_menu_items`
    ADD COLUMN `nom` VARCHAR(150) NULL AFTER `position`,
    ADD COLUMN `description` TEXT NULL AFTER `nom`,
    ADD COLUMN `prix` DECIMAL(10,2) NULL AFTER `description`,
    ADD COLUMN `category_id` INT NULL AFTER `prix`;
