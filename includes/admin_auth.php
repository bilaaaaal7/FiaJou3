<?php
// Vérifie que l'utilisateur est connecté ET qu'il a le rôle admin.
// A inclure tout en haut de chaque page du dossier /admin.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
