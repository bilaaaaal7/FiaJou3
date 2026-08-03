<?php
/**
 * Helper d'audit : journalise les actions sensibles (administratives,
 * changements de statut, assignations) dans logs/audit_AAAA-MM-JJ.log.
 * Ligne au format :
 *   [date heure] user=ID email=email role=role action=action details="..." ip=IP
 */

if (!function_exists('journaliser_audit')) {
    function journaliser_audit(string $action, string $details = ''): void
    {
        $fichier = ROOT_PATH . '/logs/audit_' . date('Y-m-d') . '.log';
        $dir = dirname($fichier);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $user  = $_SESSION['user_id'] ?? 0;
        $email = $_SESSION['email'] ?? '';
        $role  = $_SESSION['role'] ?? 'anonyme';
        $ip    = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        $details = str_replace(["\n", "\r", '"'], [' ', ' ', ''], $details);

        $ligne = sprintf(
            "[%s] user=%s email=%s role=%s action=%s details=\"%s\" ip=%s\n",
            date('Y-m-d H:i:s'),
            $user,
            $email,
            $role,
            $action,
            $details,
            $ip
        );

        @file_put_contents($fichier, $ligne, FILE_APPEND | LOCK_EX);
    }
}
