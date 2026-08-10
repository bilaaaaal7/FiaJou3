<?php
/**
 * Fichier legacy (pré-refactorisation MVC) conservé uniquement pour
 * l'historique du dépôt. La page "Livreurs" est désormais servie par
 * controleur/admin/LivreurControleur.php + vue/admin/livreurs.php,
 * via le routeur (urlRewrite.php).
 *
 * Ce fichier ne doit normalement jamais être atteint : la règle .htaccess
 * "RewriteRule ^(config|modele|controleur|vue|admin|auth|client|cuisinier|
 * livreur|includes|logs)(/|$) - [F,L]" bloque l'accès direct au dossier
 * admin/. S'il est atteint quand même (AllowOverride All désactivé,
 * .htaccess ignoré par le serveur, etc.), on redirige vers la vraie page
 * plutôt que d'afficher l'ancienne interface (formulaire toujours visible).
 * Redirection relative (et non via BASE_URL) car ce script est physiquement
 * dans admin/, un sous-dossier de la racine où se trouve index.php.
 */
header('Location: ../index.php?route=admin/livreurs');
exit;
