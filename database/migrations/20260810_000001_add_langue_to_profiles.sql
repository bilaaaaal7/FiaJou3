-- Migration FiaJou3
-- Date : 2026-08-10
-- Objet : Persistance de la langue (i18n) par compte utilisateur.
--
-- Le sélecteur de langue (FR / EN / AR) peut être choisi dès les pages
-- publiques (connexion, inscription, mot de passe oublié, dossier partenaire)
-- puis sur la page Paramètres une fois connecté. La préférence d'un compte
-- est conservée dans profiles.langue : elle est chargée dans la session à la
-- connexion et fait foi sur tout appareil. Les visiteurs anonymes restent
-- gérés par le cookie « fiajou3_lang » / localStorage.
--
-- Colonne additive : NULL tant que le compte n'a jamais choisi de langue
-- (la valeur par défaut reste le français).

ALTER TABLE `profiles`
    ADD COLUMN `langue` VARCHAR(5) NULL DEFAULT NULL AFTER `role`;
