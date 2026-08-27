-- Migration FiaJou3
-- Date : 2026-08-27
-- Objet :
--   1. Géolocalisation des zones de livraison (centre + rayon) pour la
--      détermination automatique de la zone à partir du GPS du client.
--   2. Champ `societe_nom` (texte libre) dans `orders` pour saisir le nom de
--      la société directement, sans dépendre d'une société prédéfinie.
--   3. Coordonnées par défaut pour les zones existantes (quartiers de Marrakech).

-- ============================================================
-- 1. DELIVERY_ZONES : ajouter les coordonnées GPS et le rayon
-- ============================================================
ALTER TABLE `delivery_zones`
    ADD COLUMN `lat` DECIMAL(10,7) NULL AFTER `prix_livraison`,
    ADD COLUMN `lng` DECIMAL(10,7) NULL AFTER `lat`,
    ADD COLUMN `rayon_km` DECIMAL(10,3) NULL DEFAULT 3.000 AFTER `lng`;

-- ============================================================
-- 2. ORDERS : ajouter le nom de société en texte libre (nullable)
--    Les commandes existantes restent inchangées (societe_id conservé).
-- ============================================================
ALTER TABLE `orders`
    ADD COLUMN `societe_nom` VARCHAR(150) NULL AFTER `societe_id`;

-- ============================================================
-- 3. Données par défaut pour les zones existantes
--    (centres approximatifs des quartiers de Marrakech)
-- ============================================================
UPDATE `delivery_zones` SET
    lat = 31.6295000, lng = -7.9811000, rayon_km = 1.600 WHERE id = 1;
UPDATE `delivery_zones` SET
    lat = 31.6292000, lng = -7.9960000, rayon_km = 2.500 WHERE id = 2;
UPDATE `delivery_zones` SET
    lat = 31.6323000, lng = -8.0281000, rayon_km = 3.200 WHERE id = 3;
UPDATE `delivery_zones` SET
    lat = 31.6356000, lng = -8.0084000, rayon_km = 3.000 WHERE id = 4;
