<?php
/**
 * Modèle Partenaire Invitation
 * Gère les liens sécurisés de complétion de dossier partenaire (cuisinier /
 * livreur). Chaque invitation est liée à un email + un rôle et porte un jeton
 * unique aléatoire avec une date d'expiration.
 *
 * Sécurité :
 *   - token = bin2hex(random_bytes(32)) : 64 caractères hexadécimaux,
 *     impossible à deviner ; le lien ne contient AUCUN identifiant,
 *     uniquement ce jeton (aucune modification d'URL possible) ;
 *   - expiration (expire_le) : lien temporaire ;
 *   - usage unique (utilise) : le lien est invalidé après complétion.
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../config/email.php';

class PartenaireInvitationModele
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Crée une invitation pour un email et un rôle donnés.
     *
     * @return array{token: string, expire_le: string}|false
     */
    public function creer(string $email, string $role): array|false
    {
        $token = bin2hex(random_bytes(32));
        $expireLe = date('Y-m-d H:i:s', time() + PARTENAIRE_INVITATION_DUREE);

        $stmt = $this->pdo->prepare(
            "INSERT INTO partenaire_invitations (email, role, token, expire_le)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$email, $role, $token, $expireLe]);

        return ['token' => $token, 'expire_le' => $expireLe];
    }

    /**
     * Recherche une invitation par son jeton.
     */
    public function trouverParToken(string $token): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM partenaire_invitations WHERE token = ?");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    /**
     * Invalide les invitations encore actives d'un même email (on n'en garde
     * qu'une seule à la fois).
     */
    public function invaliderAnterieures(string $email): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE partenaire_invitations SET utilise = 1, expire_le = NOW()
             WHERE email = ? AND utilise = 0"
        );
        $stmt->execute([$email]);
    }

    /**
     * Marque une invitation comme utilisée et la rattache au compte créé.
     */
    public function marquerUtilisee(int $id, int $userId): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE partenaire_invitations SET utilise = 1, user_id = ? WHERE id = ?"
        );
        $stmt->execute([$userId, $id]);
    }

    /**
     * Supprime les invitations expirées (nettoyage).
     */
    public function nettoyerExpirees(): int
    {
        $stmt = $this->pdo->prepare("DELETE FROM partenaire_invitations WHERE expire_le < NOW()");
        $stmt->execute();
        return $stmt->rowCount();
    }
}
