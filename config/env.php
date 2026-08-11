<?php
/**
 * Chargeur .env minimal (sans dépendance Composer — le projet n'utilise pas
 * Composer actuellement).
 *
 * Lit le fichier .env à la racine du projet (s'il existe) et place chaque
 * variable dans getenv()/$_ENV/$_SERVER, sans jamais écraser une variable
 * déjà définie au niveau du serveur (Apache SetEnv, variables système...).
 *
 * Format attendu (une variable par ligne) :
 *   CLE=valeur
 *   # commentaire ignoré
 *   CLE_VIDE=
 *   CLE_AVEC_ESPACES="valeur entre guillemets"
 *
 * Fichier absent (ex: en production où les variables sont définies au
 * niveau du serveur) : silencieusement ignoré, aucune erreur.
 */

if (!function_exists('env_charger')) {
    function env_charger(string $cheminFichier): void
    {
        if (!is_file($cheminFichier) || !is_readable($cheminFichier)) {
            return;
        }

        $lignes = file($cheminFichier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lignes === false) {
            return;
        }

        foreach ($lignes as $ligne) {
            $ligne = trim($ligne);

            // Ignore les commentaires et lignes vides
            if ($ligne === '' || str_starts_with($ligne, '#')) {
                continue;
            }

            if (!str_contains($ligne, '=')) {
                continue;
            }

            [$cle, $valeur] = explode('=', $ligne, 2);
            $cle = trim($cle);
            $valeur = trim($valeur);

            // Retire les guillemets englobants éventuels ("valeur" ou 'valeur')
            if (strlen($valeur) >= 2) {
                $premier = $valeur[0];
                $dernier = $valeur[strlen($valeur) - 1];
                if (($premier === '"' && $dernier === '"') || ($premier === "'" && $dernier === "'")) {
                    $valeur = substr($valeur, 1, -1);
                }
            }

            if ($cle === '') {
                continue;
            }

            // Ne jamais écraser une variable déjà définie (priorité au serveur)
            if (getenv($cle) === false) {
                putenv($cle . '=' . $valeur);
                $_ENV[$cle] = $valeur;
                $_SERVER[$cle] = $valeur;
            }
        }
    }
}

/**
 * Lit une variable d'environnement avec valeur par défaut.
 */
if (!function_exists('env')) {
    function env(string $cle, ?string $defaut = null): ?string
    {
        $valeur = getenv($cle);
        return $valeur === false ? $defaut : $valeur;
    }
}

env_charger(ROOT_PATH . '/.env');
