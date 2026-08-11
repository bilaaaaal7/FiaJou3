<?php
/**
 * Modèle Password Reset
 * Gère les liens sécurisés de réinitialisation de mot de passe.
 * Même principe que PartenaireInvitationModele (dossier partenaire) :
 * chaque demande porte un jeton unique aléatoire avec une date d'expiration.
 *
 * Sécurité :
 *   - token = bin2hex(random_bytes(32)) : 64 caractères hexadécimaux,
 *     impossible à deviner ; le lien envoyé par email ne contient AUCUN
 *     identifiant, uniquement ce jeton (aucune énumération possible) ;
 *   - expiration (expire_le) : lien temporaire ;
 *   - usage unique (utilise) : le lien est invalidé après réinitialisation ;
 *   - une seule demande active à la fois par compte (les précédentes sont
 *     invalidées avant d'en créer une nouvelle).
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../config/email.php';

class PasswordResetModele
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Crée une demande de réinitialisation pour un utilisateur donné.
     *
     * @return array{token: string, expire_le: string}|false
     */
    public function creer(int $userId, string $email): array|false
    {
        $token = bin2hex(random_bytes(32));
        $expireLe = date('Y-m-d H:i:s', time() + PASSWORD_RESET_DUREE);

        $stmt = $this->pdo->prepare(
            "INSERT INTO password_reset_tokens (user_id, email, token, expire_le)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$userId, $email, $token, $expireLe]);

        return ['token' => $token, 'expire_le' => $expireLe];
    }

    /**
     * Recherche une demande par son jeton.
     */
    public function trouverParToken(string $token): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM password_reset_tokens WHERE token = ?");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    /**
     * Invalide les demandes encore actives d'un même compte (on n'en garde
     * qu'une seule à la fois), même principe que pour les invitations
     * partenaire.
     */
    public function invaliderAnterieures(int $userId): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE password_reset_tokens SET utilise = 1, expire_le = NOW()
             WHERE user_id = ? AND utilise = 0"
        );
        $stmt->execute([$userId]);
    }

    /**
     * Marque une demande comme utilisée : le jeton ne peut plus être rejoué.
     */
    public function marquerUtilise(int $id): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE password_reset_tokens SET utilise = 1 WHERE id = ?"
        );
        $stmt->execute([$id]);
    }

    /**
     * Supprime les demandes expirées (nettoyage).
     */
    public function nettoyerExpirees(): int
    {
        $stmt = $this->pdo->prepare("DELETE FROM password_reset_tokens WHERE expire_le < NOW()");
        $stmt->execute();
        return $stmt->rowCount();
    }
}
