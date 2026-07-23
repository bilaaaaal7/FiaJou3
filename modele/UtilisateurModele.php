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

    public function findProfileByUserId(int $userId): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM profiles WHERE user_id = ?");
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
            ROLE_CLIENT,
        ]);

        return $userId;
    }

    public function getTousAvecProfil(): array
    {
        $stmt = $this->pdo->query(
            "SELECT users.id, users.email, profiles.prenom, profiles.nom, profiles.role
             FROM users
             INNER JOIN profiles ON users.id = profiles.user_id"
        );
        return $stmt->fetchAll();
    }

    public function getByIdAvecProfil(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT users.id, users.email, profiles.prenom, profiles.nom,
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
            $donnees['telephone'],
            $donnees['adresse'],
            $donnees['ville'],
            $donnees['role'],
            $id,
        ]);
    }

    public function supprimer(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM profiles WHERE user_id = ?");
        $stmt->execute([$id]);

        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function compter(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }
}
