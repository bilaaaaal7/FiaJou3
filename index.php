<?php
/**
 * Point d'entrée unique de l'application (front controller).
 * Toutes les requêtes passent par ici grâce à .htaccess.
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/assets/inc/session.php';
require_once __DIR__ . '/assets/inc/auth_guard.php';
require_once __DIR__ . '/assets/inc/audit.php';
require_once __DIR__ . '/urlRewrite.php';

dispatch();
