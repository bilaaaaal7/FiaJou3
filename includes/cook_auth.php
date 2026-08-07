<?php
// Vérifie que l'utilisateur est connecté ET qu'il a le rôle cuisinier.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'cuisinier') {
    header("Location: ../auth/login.php");
    exit;
}
