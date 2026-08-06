<?php
/**
 * Modèle Utilisateur
 * Regroupe l'accès aux tables `users` et `profiles`.
 */

require_once __DIR__ . '/Database.php';

class UtilisateurModele
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findProfileByUserId(int $userId): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function getProfilComplet(int $userId): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT users.id, users.email, users.actif, profiles.prenom, profiles.nom,
                    profiles.telephone, profiles.adresse, profiles.ville, profiles.role
             FROM users
             INNER JOIN profiles ON users.id = profiles.user_id
             WHERE users.id = ?"
        );
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function creerCompte(array $donnees): int
    {
        $hashedPassword = password_hash($donnees['password'], PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->execute([$donnees['email'], $hashedPassword]);

        $userId = (int) $this->pdo->lastInsertId();

        $stmt = $this->pdo->prepare(
            "INSERT INTO profiles (user_id, prenom, nom, telephone, adresse, ville, role)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $userId,
            $donnees['prenom'],
            $donnees['nom'],
            $donnees['telephone'],
            $donnees['adresse'],
            $donnees['ville'],
            $donnees['role'] ?? ROLE_CLIENT,
        ]);

        return $userId;
    }

    public function creerComptePersonnel(array $donnees, string $role): int
    {
        $hashedPassword = password_hash($donnees['password'], PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->execute([$donnees['email'], $hashedPassword]);

        $userId = (int) $this->pdo->lastInsertId();

        $stmt = $this->pdo->prepare(
            "INSERT INTO profiles (user_id, prenom, nom, telephone, adresse, ville, role)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $userId,
            $donnees['prenom'],
            $donnees['nom'],
            $donnees['telephone'] ?? '',
            $donnees['adresse'] ?? '',
            $donnees['ville'] ?? '',
            $role,
        ]);

        return $userId;
    }

    public function getTousAvecProfil(): array
    {
        $stmt = $this->pdo->query(
            "SELECT users.id, users.email, users.actif, profiles.prenom, profiles.nom, profiles.role
             FROM users
             INNER JOIN profiles ON users.id = profiles.user_id
             ORDER BY profiles.nom"
        );
        return $stmt->fetchAll();
    }

    public function getParRole(string $role): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT users.id, users.email, users.actif, profiles.prenom, profiles.nom, profiles.telephone, profiles.role
             FROM users
             INNER JOIN profiles ON users.id = profiles.user_id
             WHERE profiles.role = ?
             ORDER BY profiles.nom"
        );
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    }

    public function getClients(): array
    {
        return $this->getParRole(ROLE_CLIENT);
    }

    public function getCuisiniers(): array
    {
        return $this->getParRole(ROLE_CUISINIER);
    }

    public function getLivreurs(): array
    {
        return $this->getParRole(ROLE_LIVREUR);
    }

    public function getByIdAvecProfil(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT users.id, users.email, users.actif, profiles.prenom, profiles.nom,
                    profiles.telephone, profiles.adresse, profiles.ville, profiles.role
             FROM users
             INNER JOIN profiles ON users.id = profiles.user_id
             WHERE users.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function mettreAJour(int $id, array $donnees): void
    {
        $stmt = $this->pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->execute([$donnees['email'], $id]);

        $stmt = $this->pdo->prepare(
            "UPDATE profiles
             SET prenom = ?, nom = ?, telephone = ?, adresse = ?, ville = ?, role = ?
             WHERE user_id = ?"
        );
        $stmt->execute([
            $donnees['prenom'],
            $donnees['nom'],
            $donnees['telephone'] ?? '',
            $donnees['adresse'] ?? '',
            $donnees['ville'] ?? '',
            $donnees['role'],
            $id,
        ]);
    }

    public function mettreAJourProfil(int $id, array $donnees): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE profiles
             SET prenom = ?, nom = ?, telephone = ?, adresse = ?, ville = ?
             WHERE user_id = ?"
        );
        $stmt->execute([
            $donnees['prenom'],
            $donnees['nom'],
            $donnees['telephone'] ?? '',
            $donnees['adresse'] ?? '',
            $donnees['ville'] ?? '',
            $id,
        ]);
    }

    public function changerMdp(int $id, string $nouveauMdp): void
    {
        $hashed = password_hash($nouveauMdp, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed, $id]);
    }

    /**
     * Met à jour l'email d'un compte. Retourne false si l'email est déjà
     * utilisé par un autre compte.
     */
    public function changerEmail(int $id, string $email): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);

        if ((int) $stmt->fetchColumn() > 0) {
            return false;
        }

        $stmt = $this->pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->execute([$email, $id]);

        return true;
    }

    public function setActif(int $id, bool $actif): void
    {
        $stmt = $this->pdo->prepare("UPDATE users SET actif = ? WHERE id = ?");
        $stmt->execute([$actif ? 1 : 0, $id]);
    }

    public function supprimer(int $id): bool
    {
        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("DELETE FROM profiles WHERE user_id = ?");
            $stmt->execute([$id]);

            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            if ($e->getCode() === '23000') {
                return false;
            }
            throw $e;
        }
    }

    public function compter(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }

    public function compterParRole(string $role): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM profiles WHERE role = ?"
        );
        $stmt->execute([$role]);
        return (int) $stmt->fetchColumn();
    }
}
