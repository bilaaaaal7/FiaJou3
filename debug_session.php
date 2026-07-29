<?php
/**
 * Script de diagnostic temporaire — À SUPPRIMER après usage.
 * But : comprendre pourquoi la session semble persister après déconnexion.
 * Accès : http://localhost/FiaJou3/debug_session.php
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/assets/inc/session.php';
require_once __DIR__ . '/assets/inc/auth_guard.php';

header('Content-Type: text/plain; charset=utf-8');

echo "=== ETAT DE LA SESSION ===\n";
echo "session_id()        : " . session_id() . "\n";
echo "session_name()      : " . session_name() . "\n";
echo "est_connecte()      : " . (est_connecte() ? 'OUI (session active)' : 'NON') . "\n";
echo "\$_SESSION contenu :\n";
print_r($_SESSION);

echo "\n=== COOKIE RECU DU NAVIGATEUR ===\n";
print_r($_COOKIE);

echo "\n=== CONFIG SESSION PHP ===\n";
$savePath = session_save_path() ?: sys_get_temp_dir();
echo "session.save_path   : " . $savePath . "\n";
echo "Dossier existe ?     : " . (is_dir($savePath) ? 'oui' : 'NON - PROBLEME') . "\n";
echo "Dossier inscriptible?: " . (is_writable($savePath) ? 'oui' : 'NON - PROBLEME (cause probable du bug)') . "\n";

$params = session_get_cookie_params();
echo "\ncookie params actuels:\n";
print_r($params);

echo "\n=== TEST ===\n";
echo "Si 'est_connecte()' affiche OUI ici alors que tu penses être déconnecté,\n";
echo "compare le PHPSESSID ci-dessus avec celui affiché par debug_session.php\n";
echo "juste après avoir cliqué sur Déconnexion : s'il est identique, la session\n";
echo "n'a pas été détruite côté serveur (dossier non inscriptible le plus souvent).\n";
