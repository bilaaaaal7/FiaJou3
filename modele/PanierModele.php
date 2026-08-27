<?php
/**
 * Modèle Panier
 * Le panier est stocké en session ($_SESSION['panier'] = [plat_id => quantite]).
 * Ce modèle centralise toutes les opérations dessus.
 */

require_once __DIR__ . '/PlatModele.php';

class PanierModele
{
    private function initialiser(): void
    {
        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }
    }

    /**
     * Ajoute un plat au panier, restreint au menu de la semaine (cahier des charges).
     *
     * @param int      $platId
     * @param string|null $date Date de livraison souhaitée (Y-m-d). Ignorée si absente.
     *
     * @return string 'ok' | 'indisponible' | 'horsmenu' | 'quantite_max' | 'fermee'
     */
    public function ajouter(int $platId, ?string $date = null): string
    {
        $this->initialiser();

        $platModele = new PlatModele();
        $plat = $platModele->getParId($platId);

        if (!$plat || !$plat['disponible']) {
            return 'indisponible';
        }

        $quantiteActuelle = $_SESSION['panier'][$platId] ?? 0;
        if ($quantiteActuelle >= 20) {
            return 'quantite_max';
        }

        require_once __DIR__ . '/MenuSemaineModele.php';
        $menuModele = new MenuSemaineModele();

        if (!$menuModele->estPlatAuMenu($platId)) {
            return 'horsmenu';
        }

        if (isset($_SESSION['panier'][$platId])) {
            $_SESSION['panier'][$platId]++;
        } else {
            $_SESSION['panier'][$platId] = 1;
        }
        return 'ok';
    }

    public function augmenter(int $platId): bool
    {
        $this->initialiser();

        if (isset($_SESSION['panier'][$platId])) {
            if ($_SESSION['panier'][$platId] >= 20) {
                return false;
            }
            $_SESSION['panier'][$platId]++;
            return true;
        }
        return false;
    }

    public function diminuer(int $platId): void
    {
        $this->initialiser();

        if (isset($_SESSION['panier'][$platId])) {
            $_SESSION['panier'][$platId]--;

            if ($_SESSION['panier'][$platId] <= 0) {
                unset($_SESSION['panier'][$platId]);
            }
        }
    }

    public function retirer(int $platId): void
    {
        $this->initialiser();
        unset($_SESSION['panier'][$platId]);
    }

    public function vider(): void
    {
        unset($_SESSION['panier']);
        unset($_SESSION['panier_date']);
    }

    /**
     * Date de livraison enregistrée pour le panier (Y-m-d), si définie.
     */
    public function getDate(): ?string
    {
        return $_SESSION['panier_date'] ?? null;
    }

    public function setDate(?string $date): void
    {
        if ($date !== null && $date !== '') {
            $_SESSION['panier_date'] = $date;
        } else {
            unset($_SESSION['panier_date']);
        }
    }

    public function estVide(): bool
    {
        return empty($_SESSION['panier']);
    }

    public function nombreArticles(): int
    {
        if (empty($_SESSION['panier'])) {
            return 0;
        }
        return array_sum($_SESSION['panier']);
    }

    public function getContenuBrut(): array
    {
        return $_SESSION['panier'] ?? [];
    }

    /**
     * Retourne le contenu détaillé du panier (infos plat + quantité + sous-total)
     * ainsi que le total général.
     */
    public function getDetails(): array
    {
        $platModele = new PlatModele();
        $panier = [];
        $total = 0;

        if (!empty($_SESSION['panier'])) {
            foreach ($_SESSION['panier'] as $id => $quantite) {
                $plat = $platModele->getParId((int) $id);

                if ($plat) {
                    $plat['quantite'] = $quantite;
                    $plat['sous_total'] = $plat['prix'] * $quantite;
                    $total += $plat['sous_total'];
                    $panier[] = $plat;
                }
            }
        }

        return ['articles' => $panier, 'total' => $total];
    }

    /**
     * Total du panier sans le détail (utilisé sur la page de commande).
     */
    public function getTotal(): float
    {
        $platModele = new PlatModele();
        $total = 0;

        if (!empty($_SESSION['panier'])) {
            foreach ($_SESSION['panier'] as $id => $quantite) {
                $plat = $platModele->getParId((int) $id);
                if ($plat) {
                    $total += $plat['prix'] * $quantite;
                }
            }
        }

        return $total;
    }

    /**
     * Valide le panier (disponibilité, quantités, conformité au menu de la
     * semaine). Les articles invalides sont retirés du panier.
     */
    public function valider(): array
    {
        $platModele = new PlatModele();
        $erreurs = [];

        if (!empty($_SESSION['panier'])) {
            foreach ($_SESSION['panier'] as $id => $quantite) {
                $plat = $platModele->getParId((int) $id);
                if (!$plat) {
                    $erreurs[] = "Le plat #$id n'existe plus.";
                    unset($_SESSION['panier'][$id]);
                } elseif (!$plat['disponible']) {
                    $erreurs[] = "Le plat \"{$plat['nom']}\" n'est plus disponible.";
                    unset($_SESSION['panier'][$id]);
                }
            }
        }

        return $erreurs;
    }
}
