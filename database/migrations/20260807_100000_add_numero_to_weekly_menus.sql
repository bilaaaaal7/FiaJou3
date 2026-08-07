-- Migration FiaJou3
-- Date : 2026-08-07
-- Objet : Planification hebdomadaire dynamique - numérotation propre au menu.
-- Ajoute une colonne `numero` à `weekly_menus` : chaque semaine possède son
-- propre numéro (1, 2, 3, ...) indépendant du numéro ISO. La numérotation
-- commence à 1 pour la première semaine du système (03/08/2026) et chaque
-- semaine suivante est incrémentée automatiquement à la création.
-- Les menus hérités sans période (week_start NULL) gardent numero NULL.
-- Aucune donnée existante n'est supprimée.

ALTER TABLE `weekly_menus`
    ADD COLUMN `numero` INT NULL AFTER `id`;

-- Rétro-remplissage : numéro = (semaine écoulée depuis la semaine 1) + 1.
-- La première semaine du système (03/08/2026 → 09/08/2026) reçoit le numéro 1.
UPDATE `weekly_menus`
SET `numero` = FLOOR(DATEDIFF(`week_start`, DATE('2026-08-03')) / 7) + 1
WHERE `week_start` IS NOT NULL AND `week_end` IS NOT NULL;
