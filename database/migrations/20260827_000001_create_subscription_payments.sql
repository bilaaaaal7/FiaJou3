-- Migration FiaJou3
-- Date : 2026-08-27
-- Objet : Table des paiements d'abonnement.
--
-- IMPORTANT (sécurité) : aucune donnée bancaire sensible n'est stockée en
-- clair. Seules sont conservées :
--   - une référence de transaction (identifiant généré, jamais la carte) ;
--   - le mode de paiement ('sandbox' en développement local) ;
--   - le masque de carte : uniquement les 4 derniers chiffres + la marque.
-- Le numéro complet, la date d'expiration et le CVV ne sont JAMAIS écrits
-- en base. L'architecture est conçue pour brancher un vrai payment gateway
-- (Stripe/CMI) : le mode 'sandbox' remplace l'appel réel tant qu'aucun
-- gateway n'est configuré.

CREATE TABLE IF NOT EXISTS `subscription_payments` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`    INT UNSIGNED NOT NULL,
    `montant`    DECIMAL(10,2) NOT NULL,
    `statut`     VARCHAR(20) NOT NULL DEFAULT 'sandbox',
    `reference`  VARCHAR(64) NOT NULL,
    `mode`       VARCHAR(20) NOT NULL DEFAULT 'sandbox',
    `card_last4` VARCHAR(4)  NULL,
    `card_brand` VARCHAR(16) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_sp_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
