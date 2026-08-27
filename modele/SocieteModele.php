<?php
/**
 * Modèle Societe
 * Accès à la table `societes`.
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../assets/inc/langue.php';

class SocieteModele
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Toutes les sociétés actives, triées par nom.
     */
    public function getToutesActives(): array
    {
        $stmt = $this->pdo->query(
            "SELECT * FROM societes WHERE active = 1 ORDER BY nom"
        );
        $lignes = $stmt->fetchAll();
        $langue = langue_actuelle();
        if ($langue !== 'fr') {
            foreach ($lignes as &$ligne) {
                $cleLocale = 'nom_' . $langue;
                if (!empty($ligne[$cleLocale])) {
                    $ligne['nom'] = $ligne[$cleLocale];
                }
            }
            unset($ligne);
        }
        return $lignes;
    }

    public function getParId(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM societes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
