<?php
/**
 * Modèle Zone
 * Accès à la table `delivery_zones`.
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../assets/inc/langue.php';

class ZoneModele
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getToutes(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM delivery_zones ORDER BY nom");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getParId(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM delivery_zones WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Distance en kilomètres entre deux points GPS (formule de Haversine).
     */
    public static function distanceKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $rayonTerre = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) * sin($dLng / 2);
        return $rayonTerre * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * Détermine la zone de livraison correspondant à des coordonnées GPS.
     *
     * Parcourt les zones disposant d'un centre et d'un rayon et retourne la
     * zone la plus proche dont le rayon couvre la position donnée. Retourne
     * false si aucune zone ne couvre les coordonnées.
     */
    public function getZoneParCoordonnees(float $lat, float $lng): array|false
    {
        $zones = $this->getToutes();
        $meilleureZone = false;
        $distanceMin = PHP_FLOAT_MAX;

        foreach ($zones as $zone) {
            $zoneLat = (float) ($zone['lat'] ?? 0);
            $zoneLng = (float) ($zone['lng'] ?? 0);
            $rayon = (float) ($zone['rayon_km'] ?? 0);

            if ($zoneLat == 0 && $zoneLng == 0) {
                continue;
            }

            $distance = self::distanceKm($lat, $lng, $zoneLat, $zoneLng);
            if ($distance <= $rayon && $distance < $distanceMin) {
                $distanceMin = $distance;
                $meilleureZone = $zone;
            }
        }

        return $meilleureZone;
    }

    public function creer(string $nom, float $prixLivraison, ?string $nomEn = null, ?string $nomAr = null, ?float $lat = null, ?float $lng = null, ?float $rayonKm = null): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO delivery_zones (nom, nom_en, nom_ar, prix_livraison, lat, lng, rayon_km) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$nom, self::ouNull($nomEn), self::ouNull($nomAr), $prixLivraison, $lat, $lng, $rayonKm]);
    }

    public function mettreAJour(int $id, string $nom, float $prixLivraison, ?string $nomEn = null, ?string $nomAr = null, ?float $lat = null, ?float $lng = null, ?float $rayonKm = null): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE delivery_zones SET nom = ?, nom_en = ?, nom_ar = ?, prix_livraison = ?, lat = ?, lng = ?, rayon_km = ? WHERE id = ?"
        );
        $stmt->execute([$nom, self::ouNull($nomEn), self::ouNull($nomAr), $prixLivraison, $lat, $lng, $rayonKm, $id]);
    }

    private static function ouNull(?string $valeur): ?string
    {
        return ($valeur === null || trim($valeur) === '') ? null : trim($valeur);
    }

    public function supprimer(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM delivery_zones WHERE id = ?");
        try {
            $stmt->execute([$id]);
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                return false;
            }
            throw $e;
        }
    }

    public function compter(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM delivery_zones")->fetchColumn();
    }
}
