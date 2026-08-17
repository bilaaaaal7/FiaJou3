-- Migration FiaJou3
-- Date : 2026-08-17
-- Objet : Completer le systeme multilingue complet (FR / EN / AR).
--
-- 1. Ajoute des colonnes de traduction a delivery_zones
-- 2. Complete les traductions manquantes dans plats, categories, weekly_menu_items
-- 3. Complete les traductions manquantes pour les delivery_zones
-- Aucune donnee existante n'est supprimee.

-- ============================================================
-- 1. DELIVERY ZONES : ajouter nom_en, nom_ar
-- ============================================================
ALTER TABLE `delivery_zones`
    ADD COLUMN `nom_en` VARCHAR(100) NULL AFTER `nom`,
    ADD COLUMN `nom_ar` VARCHAR(100) NULL AFTER `nom_en`;

-- Remplir les traductions des zones (noms propres marocains)
UPDATE `delivery_zones` SET `nom_en` = 'Medina', `nom_ar` = 'المدينة القديمة' WHERE `nom` = 'medina';
UPDATE `delivery_zones` SET `nom_en` = 'Gueliz', `nom_ar` = 'گueliz' WHERE `nom` = 'Gueliz';
UPDATE `delivery_zones` SET `nom_en` = 'Mehamid', `nom_ar` = 'مهاميد' WHERE `nom` = 'Mehamid';
UPDATE `delivery_zones` SET `nom_en` = 'Massira', `nom_ar` = 'المسيرة' WHERE `nom` = 'massira';

-- ============================================================
-- 2. PLATS : compléter les traductions manquantes par ID
-- ============================================================
-- id=26: Coca-Cola
UPDATE `plats` SET
    `nom_en` = 'Coca-Cola',
    `nom_ar` = 'كوكا كولا',
    `description_en` = '33 cl can.',
    `description_ar` = 'علبة 33 سم'
WHERE `id` = 26 AND (`nom_en` IS NULL OR `nom_ar` IS NULL);

-- id=27: Sprite
UPDATE `plats` SET
    `nom_en` = 'Sprite',
    `nom_ar` = 'سبرايت',
    `description_en` = '33 cl can.',
    `description_ar` = 'علبة 33 سم'
WHERE `id` = 27 AND (`nom_en` IS NULL OR `nom_ar` IS NULL);

-- id=28: Jus d'Orange
UPDATE `plats` SET
    `nom_en` = 'Fresh Orange Juice',
    `nom_ar` = 'عصير البرتقال الطازج',
    `description_en` = 'Fresh orange juice.',
    `description_ar` = 'عصير برتقال طازج.'
WHERE `id` = 28 AND (`nom_en` IS NULL OR `nom_ar` IS NULL);

-- id=29: Eau Minérale
UPDATE `plats` SET
    `nom_en` = 'Mineral Water',
    `nom_ar` = 'مياه معدنية',
    `description_en` = '50 cl bottle.',
    `description_ar` = 'زجاجة 50 سم.'
WHERE `id` = 29 AND (`nom_en` IS NULL OR `nom_ar` IS NULL);

-- id=30: Lben
UPDATE `plats` SET
    `nom_en` = 'Lben',
    `nom_ar` = 'لبن',
    `description_en` = 'Traditional Moroccan buttermilk.',
    `description_ar` = 'لبن مغربي تقليدي.'
WHERE `id` = 30 AND (`nom_en` IS NULL OR `nom_ar` IS NULL);

-- id=32: Tajine poissons
UPDATE `plats` SET
    `nom_en` = 'Fish Tagine',
    `nom_ar` = 'طاجين السمك',
    `description_en` = 'Fish tagine with tomato sauce.',
    `description_ar` = 'طاجين السمك مع صلصة الطماطم.'
WHERE `id` = 32 AND (`nom_en` IS NULL OR `nom_ar` IS NULL);

-- Compléter les descriptions EN/AR manquantes pour les plats existants
UPDATE `plats` SET
    `description_en` = 'Royal couscous with chicken, lamb, and vegetables.',
    `description_ar` = 'كسكس ملكي بالدجاج واللحم والخضار.'
WHERE `id` = 8 AND `description_en` IS NULL;

UPDATE `plats` SET
    `description_en` = 'Traditional couscous with meat and vegetables.',
    `description_ar` = 'كسكس تقليدي باللحم والخضار.'
WHERE `id` = 9 AND `description_en` IS NULL;

UPDATE `plats` SET
    `description_en` = 'Mixed grill platter with assorted meats.',
    `description_ar` = 'طبق مشاوي مشكلة بأنواع اللحوم.'
WHERE `id` = 10 AND `description_en` IS NULL;

UPDATE `plats` SET
    `description_en` = 'Marinated chicken skewers.',
    `description_ar` = 'أسياخ دجاج متبلة.'
WHERE `id` = 11 AND `description_en` IS NULL;

UPDATE `plats` SET
    `description_en` = 'Chicken tagine with preserved lemons and olives.',
    `description_ar` = 'طاجين الدجاج بالحامض المصير والزيتون.'
WHERE `id` = 12 AND `description_en` IS NULL;

UPDATE `plats` SET
    `description_en` = 'Kefta meatballs in tomato sauce.',
    `description_ar` = 'كفتة بالصلصة الطماطم.'
WHERE `id` = 13 AND `description_en` IS NULL;

UPDATE `plats` SET
    `description_en` = 'Traditional mini chicken pastilla.',
    `description_ar` = 'بسطيلة دجاج تقليدية صغيرة.'
WHERE `id` = 14 AND `description_en` IS NULL;

UPDATE `plats` SET
    `description_en` = 'Seafood pastilla.',
    `description_ar` = 'بسطيلة المأكولات البحرية.'
WHERE `id` = 15 AND `description_en` IS NULL;

UPDATE `plats` SET
    `description_en` = 'Chicken Caesar salad with parmesan and croutons.',
    `description_ar` = 'سلطة سيزر بالدجاج والبارميزان والكروتون.'
WHERE `id` = 24 AND `description_en` IS NULL;

UPDATE `plats` SET
    `description_en` = 'Moroccan salad with tomatoes, cucumber, and peppers.',
    `description_ar` = 'سلطة مغربية بالطماطم والخيار والفليفلة.'
WHERE `id` = 25 AND `description_en` IS NULL;

UPDATE `plats` SET
    `description_en` = 'Prune tagine with meat.',
    `description_ar` = 'طاجين البرقوق باللحم.'
WHERE `id` = 31 AND `description_en` IS NULL;

-- ============================================================
-- 3. CATEGORIES : compléter les descriptions manquantes
-- ============================================================
UPDATE `categories` SET
    `description_en` = 'Gourmet burgers made with fresh ingredients.',
    `description_ar` = 'برغر شهي مُحضّر بمكونات طازجة.'
WHERE `id` = 7 AND `description_en` IS NULL;

UPDATE `categories` SET
    `description_en` = 'Generous tacos with various toppings.',
    `description_ar` = 'تاكوس سخي بأنواع مختلفة من الحشوات.'
WHERE `id` = 8 AND `description_en` IS NULL;

UPDATE `categories` SET
    `description_en` = 'Artisanal pizzas baked in a traditional oven.',
    `description_ar` = 'بيتزا حرفية تُخبز في فرن تقليدي.'
WHERE `id` = 9 AND `description_en` IS NULL;

-- ============================================================
-- 4. WEEKLY_MENU_ITEMS : hériter les traductions des plats si NULL
-- ============================================================
UPDATE `weekly_menu_items` wmi
    INNER JOIN `plats` p ON p.id = wmi.product_id
SET
    wmi.nom_en = COALESCE(NULLIF(wmi.nom_en, ''), p.nom_en),
    wmi.nom_ar = COALESCE(NULLIF(wmi.nom_ar, ''), p.nom_ar),
    wmi.description_en = COALESCE(NULLIF(wmi.description_en, ''), p.description_en),
    wmi.description_ar = COALESCE(NULLIF(wmi.description_ar, ''), p.description_ar)
WHERE wmi.nom_en IS NULL OR wmi.nom_ar IS NULL
   OR wmi.description_en IS NULL OR wmi.description_ar IS NULL;
