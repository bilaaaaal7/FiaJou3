<?php
/**
 * Système de langue (i18n) — résolution de la langue active côté serveur.
 *
 * Persistance :
 *   - visiteur anonyme : cookie « fiajou3_lang » (et localStorage côté JS) ;
 *   - compte connecté  : colonne profiles.langue, posée en $_SESSION['langue']
 *     à la connexion (voir LoginControleur) et mise à jour par le sélecteur
 *     via le contrôleur de langue (route « langue »).
 *
 * Le moteur JS (assets/js/i18n.js) reçoit la langue résolue via
 * window.FJ_I18N, posé par assets/inc/header.php sur les pages participantes.
 */

require_once __DIR__ . '/session.php';

function langues_supportees(): array
{
    return ['fr', 'en', 'ar'];
}

function langue_valide(?string $langue): bool
{
    return $langue !== null && in_array($langue, langues_supportees(), true);
}

/**
 * Langue active pour la requête courante.
 * Ordre de priorité : session (posée à la connexion ou par le sélecteur)
 * → cookie du navigateur → français par défaut.
 */
function langue_actuelle(): string
{
    static $langue = null;
    if ($langue !== null) {
        return $langue;
    }
    if (langue_valide($_SESSION['langue'] ?? null)) {
        $langue = $_SESSION['langue'];
        return $langue;
    }
    if (langue_valide($_COOKIE['fiajou3_lang'] ?? null)) {
        $langue = $_COOKIE['fiajou3_lang'];
        return $langue;
    }
    $langue = 'fr';
    return $langue;
}
