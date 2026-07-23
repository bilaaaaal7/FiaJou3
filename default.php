<?php
/**
 * Page par défaut (route "/").
 * Redirige vers le tableau de bord correspondant au rôle si l'utilisateur
 * est déjà connecté, sinon vers la page de connexion.
 */

if (est_connecte()) {
    header('Location: ' . BASE_URL . '/index.php?route=' . utilisateur_role());
    exit;
}

header('Location: ' . BASE_URL . '/index.php?route=connexion');
exit;
