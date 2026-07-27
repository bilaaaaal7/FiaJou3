<?php
/**
 * Aide à l'authentification et au contrôle d'accès basé sur les rôles (RBAC).
 * Utilisé par les contrôleurs pour protéger les pages admin/client/cuisinier/livreur.
 */

require_once __DIR__ . '/session.php';

function est_connecte(): bool
{
    return isset($_SESSION['user_id']);
}

function utilisateur_role(): ?string
{
    return $_SESSION['role'] ?? null;
}

/**
 * Convertit un rôle utilisateur en route par défaut de son espace.
 * Nécessaire car certains rôles (cook, driver) ont une valeur différente
 * du nom de leur route (cuisinier, livreur).
 */
function route_par_defaut_pour_role(?string $role): string
{
    $redirects = [
        ROLE_ADMIN     => 'admin',
        ROLE_CLIENT    => 'client/dashboard',
        ROLE_CUISINIER => 'cuisinier',
        ROLE_LIVREUR   => 'livreur',
    ];

    return $redirects[$role] ?? ($role ?? 'connexion');
}

/**
 * Redirige vers la page de connexion si l'utilisateur n'est pas connecté.
 */
function exiger_connexion(): void
{
    if (!est_connecte()) {
        header('Location: ' . BASE_URL . '/index.php?route=connexion');
        exit;
    }
}

/**
 * Redirige vers la page de connexion (ou vers une page 403) si l'utilisateur
 * n'a pas l'un des rôles autorisés.
 *
 * @param string|string[] $rolesAutorises
 */
function exiger_role(string|array $rolesAutorises): void
{
    exiger_connexion();

    $rolesAutorises = (array) $rolesAutorises;

    if (!in_array(utilisateur_role(), $rolesAutorises, true)) {
        http_response_code(403);
        require ROOT_PATH . '/vue/errors/403.php';
        exit;
    }
}
