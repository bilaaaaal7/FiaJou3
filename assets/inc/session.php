<?php
/**
 * Démarre la session PHP une seule fois, quel que soit le point d'entrée.
 * Remplace les multiples `session_start()` dispersés dans chaque fichier.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
