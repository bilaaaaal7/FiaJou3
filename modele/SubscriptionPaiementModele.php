<?php
/**
 * Modèle Paiement d'abonnement
 * Accès à la table `subscription_payments`.
 *
 * Sécurité : aucune donnée bancaire sensible n'est stockée en clair.
 * Seules la référence de transaction, le mode ('sandbox'), les 4 derniers
 * chiffres et la marque de la carte sont conservés. Le numéro complet,
 * la date d'expiration et le CVV ne sont jamais envoyés vers la base.
 *
 * Aucun payment gateway (Stripe/CMI) n'est configuré pour l'instant : le
 * mode 'sandbox' permet de tester le parcours de paiement sans simuler un
 * vrai débit bancaire. On peut brancher un gateway réel ici plus tard.
 */

require_once __DIR__ . '/Database.php';

class SubscriptionPaiementModele
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Enregistre un paiement d'abonnement en mode sandbox (test local).
     * Ne conserve que la référence, le mode, et le masque de la carte
     * (4 derniers chiffres + marque) — jamais le numéro complet ni le CVV.
     */
    public function enregistrerSandbox(int $userId, float $montant, string $cardLast4, string $cardBrand): int
    {
        $reference = 'ABO-' . strtoupper(bin2hex(random_bytes(8)));
        $last4 = substr(preg_replace('/\D+/', '', $cardLast4), -4);

        $stmt = $this->pdo->prepare(
            "INSERT INTO subscription_payments (user_id, montant, statut, reference, mode, card_last4, card_brand)
             VALUES (?, ?, 'sandbox', ?, 'sandbox', ?, ?)"
        );
        $stmt->execute([$userId, $montant, $reference, $last4, $cardBrand]);

        return (int) $this->pdo->lastInsertId();
    }
}
