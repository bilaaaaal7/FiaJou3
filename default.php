<?php
/**
 * Page par défaut (route "/").
 * Redirige vers le tableau de bord correspondant au rôle si l'utilisateur
 * est déjà connecté, sinon vers la page de connexion.
 */

if (est_connecte()) {
    $route = route_par_defaut_pour_role(utilisateur_role());
    header('Location: ' . BASE_URL . '/index.php?route=' . $route);
    exit;
}

header('Location: ' . BASE_URL . '/index.php?route=accueil');
exit;
