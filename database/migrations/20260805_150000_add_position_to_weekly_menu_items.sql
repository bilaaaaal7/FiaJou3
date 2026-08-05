-- Migration FiaJou3
-- Date : 2026-08-05
-- Objet : Menu de la semaine - plusieurs plats par jour.
-- Ajoute une colonne `position` pour ordonner les plats d'un même jour
-- (les plats sont triés par position au sein du jour). Les lignes existantes
-- reçoivent la valeur par défaut 0 : leur ordre reste inchangé (tri hérité
-- par catégorie), aucune donnée existante n'est modifiée.

ALTER TABLE `weekly_menu_items`
    ADD COLUMN `position` INT NOT NULL DEFAULT 0 AFTER `jour`;
