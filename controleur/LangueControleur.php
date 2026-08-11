<?php
/**
 * Contrôleur : Langue
 * Route : langue
 *
 * Point d'action AJAX (appelé par assets/js/i18n.js) : enregistre la langue
 * choisie pour la session courante, pour le compte connecté (profiles.langue)
 * et dans un cookie persistant. Aucune page n'est rendue.
 */

require_once ROOT_PATH . '/assets/inc/langue.php';

header('Content-Type: application/json; charset=utf-8');

$langue = strtolower(trim((string) ($_POST['lang'] ?? '')));

if (!langue_valide($langue)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'Langue invalide.']);
    exit;
}

$_SESSION['langue'] = $langue;

if (est_connecte()) {
    require_once ROOT_PATH . '/modele/UtilisateurModele.php';
    $utilisateurModele = new UtilisateurModele();
    $profil = $utilisateurModele->findProfileByUserId((int) $_SESSION['user_id']);
    if ($profil) {
        $utilisateurModele->mettreAJourLangue((int) $profil['user_id'], $langue);
    }
}

setcookie('fiajou3_lang', $langue, time() + 31536000, '/');

echo json_encode(['ok' => true, 'lang' => $langue]);
