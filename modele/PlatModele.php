<?php
/**
 * Modele Plat
 * Acces a la table `plats` de la base de donnees (PDO).
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../assets/inc/langue.php';

class PlatModele
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getTous(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM plats ORDER BY id");
        $plats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($plats as &$plat) {
            $plat['prix'] = (float) $plat['prix'];
        }
        unset($plat);

        return $plats;
    }

    /**
     * Menu client : plats + nom de la categorie, tries par categorie puis nom.
     * Les champs `nom`, `description` et `categorie` sont rendus dans la
     * langue active (voir localiser()) ; la base française sert de repli.
     */
    public function getMenu(): array
    {
        $stmt = $this->pdo->query(
            "SELECT p.id, p.nom, p.nom_en, p.nom_ar,
                    p.description, p.description_en, p.description_ar,
                    p.prix, p.image, p.disponible,
                    c.nom AS categorie, c.nom_en AS categorie_en, c.nom_ar AS categorie_ar
             FROM plats p
             INNER JOIN categories c ON c.id = p.category_id
             ORDER BY c.nom, p.nom"
        );

        $menu = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($menu as &$plat) {
            $plat['id'] = (int) $plat['id'];
            $plat['prix'] = (float) $plat['prix'];
            $plat['disponible'] = (int) $plat['disponible'];

            $plat['nom'] = localiser($plat, 'nom');
            $plat['description'] = localiser($plat, 'description');
            $plat['categorie'] = localiser([
                'categorie' => $plat['categorie'] ?? null,
                'categorie_en' => $plat['categorie_en'] ?? null,
                'categorie_ar' => $plat['categorie_ar'] ?? null,
            ], 'categorie');
            unset($plat['nom_en'], $plat['nom_ar'],
                  $plat['description_en'], $plat['description_ar'],
                  $plat['categorie_en'], $plat['categorie_ar']);
        }
        unset($plat);

        return $menu;
    }

    public function getParId(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM plats WHERE id = ?");
        $stmt->execute([$id]);

        $plat = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($plat) {
            $plat['prix'] = (float) $plat['prix'];
        }

        return $plat;
    }

    public function creer(array $donnees): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO plats (category_id, nom, nom_en, nom_ar, description, description_en, description_ar, prix, image, disponible)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            (int) $donnees['category_id'],
            $donnees['nom'],
            self::ouNull($donnees['nom_en'] ?? null),
            self::ouNull($donnees['nom_ar'] ?? null),
            $donnees['description'] ?? null,
            self::ouNull($donnees['description_en'] ?? null),
            self::ouNull($donnees['description_ar'] ?? null),
            (float) $donnees['prix'],
            $donnees['image'] ?? null,
            (int) ($donnees['disponible'] ?? 1),
        ]);
    }

    public function mettreAJour(int $id, array $donnees): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE plats
             SET category_id = ?, nom = ?, nom_en = ?, nom_ar = ?,
                 description = ?, description_en = ?, description_ar = ?,
                 prix = ?, image = ?, disponible = ?
             WHERE id = ?"
        );
        $stmt->execute([
            (int) $donnees['category_id'],
            $donnees['nom'],
            self::ouNull($donnees['nom_en'] ?? null),
            self::ouNull($donnees['nom_ar'] ?? null),
            $donnees['description'] ?? null,
            self::ouNull($donnees['description_en'] ?? null),
            self::ouNull($donnees['description_ar'] ?? null),
            (float) $donnees['prix'],
            $donnees['image'] ?? null,
            (int) ($donnees['disponible'] ?? 1),
            $id,
        ]);
    }

    /**
     * NULL si la chaîne est vide : une traduction effacée retombe sur la base.
     */
    private static function ouNull(?string $valeur): ?string
    {
        return ($valeur === null || trim($valeur) === '') ? null : trim($valeur);
    }

    public function supprimer(int $id): bool
    {
        // Refuse la suppression si le plat fait partie de commandes existantes
        // (meme comportement que l'ancienne contrainte de cle etrangere).
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM order_items WHERE product_id = ?");
        $stmt->execute([$id]);

        if ((int) $stmt->fetchColumn() > 0) {
            return false;
        }

        try {
            $stmt = $this->pdo->prepare("DELETE FROM plats WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            // Contrainte de cle etrangere (ex : plat present dans un menu de la semaine).
            if ($e->getCode() === '23000') {
                return false;
            }
            throw $e;
        }
    }

    public function compter(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM plats")->fetchColumn();
    }

    /**
     * Enregistre l'image uploadee d'un plat dans /uploads et retourne son nom
     * de fichier genere, ou '' si aucun fichier n'a ete envoye.
     * Delegue a UploadModele (nom genere, verif du type reel, taille max).
     */
    public function enregistrerImage(array $fichier): string
    {
        require_once __DIR__ . '/UploadModele.php';
        return UploadModele::enregistrer($fichier) ?? '';
    }
}
