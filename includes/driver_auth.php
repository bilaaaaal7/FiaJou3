<?php
// Vérifie que l'utilisateur est connecté ET qu'il a le rôle driver (livreur).
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'driver') {
    header("Location: ../auth/login.php");
    exit;
}
