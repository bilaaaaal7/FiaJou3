<?php
/**
 * Modèle Rate Limiter
 * Limite le nombre de tentatives d'une action sensible (connexion, inscription)
 * par adresse IP, avec blocage temporaire. Les compteurs sont stockés dans un
 * fichier JSON sous logs/ (fonctionne sans base de données ni cache externe).
 */

class RateLimiterModele
{
    private string $fichier;
    private array $donnees;

    private int $fenetre;      // secondes pendant lesquelles les tentatives comptent
    private int $maxTentatives;
    private int $dureeBlocage; // secondes de blocage après trop de tentatives

    public function __construct(string $cle, int $fenetre = 900, int $maxTentatives = 5, int $dureeBlocage = 300)
    {
        $nom = preg_replace('/[^a-z0-9_\-]/i', '', $cle);
        $this->fichier = ROOT_PATH . '/logs/' . ($nom !== '' ? $nom : 'defaut') . '.json';
        $this->fenetre = $fenetre;
        $this->maxTentatives = $maxTentatives;
        $this->dureeBlocage = $dureeBlocage;

        $this->charger();
    }

    private function charger(): void
    {
        if (is_file($this->fichier)) {
            $contenu = file_get_contents($this->fichier);
            $donnees = json_decode($contenu ?: '', true);
            $this->donnees = is_array($donnees) ? $donnees : ['tentatives' => [], 'bloque_jusqua' => 0];
        } else {
            $this->donnees = ['tentatives' => [], 'bloque_jusqua' => 0];
        }
    }

    private function sauvegarder(): void
    {
        $dir = dirname($this->fichier);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        @file_put_contents($this->fichier, json_encode($this->donnees), LOCK_EX);
    }

    /**
     * Purge les tentatives plus anciennes que la fenêtre.
     */
    private function purger(): void
    {
        $seuil = time() - $this->fenetre;
        $this->donnees['tentatives'] = array_values(array_filter(
            $this->donnees['tentatives'],
            fn($ts) => $ts > $seuil
        ));
    }

    public function tempsRestantBlocage(): int
    {
        return max(0, (int) ($this->donnees['bloque_jusqua'] ?? 0) - time());
    }

    /**
     * L'action est-elle autorisée pour l'IP courante ?
     */
    public function peutTenter(): bool
    {
        $this->purger();
        return $this->tempsRestantBlocage() === 0;
    }

    /**
     * Enregistre un échec ; déclenche le blocage au-delà du seuil.
     */
    public function enregistrerEchec(): void
    {
        $this->purger();
        $this->donnees['tentatives'][] = time();
        if (count($this->donnees['tentatives']) >= $this->maxTentatives) {
            $this->donnees['bloque_jusqua'] = time() + $this->dureeBlocage;
        }
        $this->sauvegarder();
    }

    /**
     * Réinitialise les compteurs (après un succès).
     */
    public function reinitialiser(): void
    {
        $this->donnees = ['tentatives' => [], 'bloque_jusqua' => 0];
        $this->sauvegarder();
    }

    public static function ipCourante(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
