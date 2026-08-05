<?php
/**
 * Contrôleur : Mon profil (tous les rôles connectés)
 * Route : /profil
 */

exiger_connexion();

require_once ROOT_PATH . '/modele/UtilisateurModele.php';

$utilisateurModele = new UtilisateurModele();
$profil = $utilisateurModele->getProfilComplet((int) $_SESSION['user_id']);

if (!$profil) {
    http_response_code(404);
    require ROOT_PATH . '/vue/errors/404.php';
    return;
}

require ROOT_PATH . '/vue/profil.php';
