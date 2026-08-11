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
 */
function route_par_defaut_pour_role(?string $role): string
{
    $redirects = [
        ROLE_ADMIN     => 'admin',
        ROLE_CLIENT    => 'accueil',
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

/**
 * Redirige vers une route interne en attachant un message d'erreur (?erreur=).
 * Utilisé par les contrôleurs pour signaler un échec (accès refusé, statut
 * invalide...) tout en préservant le formulaire de la page de destination.
 */
function rediriger_avec_erreur(string $route, string $message): void
{
    header('Location: ' . BASE_URL . '/index.php?route=' . ltrim($route, '/') . '&erreur=' . urlencode($message));
    exit;
}

/**
 * Construit l'URL de la page de connexion en mémorisant la destination
 * interne à atteindre après une authentification réussie.
 *
 * Cible des boutons « Commander » / « Commencer à commander » : un visiteur
 * non connecté est toujours envoyé sur la page de connexion (jamais
 * directement sur l'inscription), puis ramené vers la page de commande
 * qu'il voulait initialement consulter une fois connecté.
 *
 * @param string $route  Route interne, ex. 'client'
 * @param array  $params Paramètres GET supplémentaires, ex. ['ajouter' => 12]
 */
function url_connexion_avec_retour(string $route, array $params = []): string
{
    $retour = 'route=' . ltrim($route, '/');
    if (!empty($params)) {
        $retour .= '&' . http_build_query($params);
    }

    return BASE_URL . '/index.php?route=connexion&retour=' . urlencode($retour);
}

/**
 * Restitue une destination « retour » sûre (route interne de l'espace client)
 * pour la redirection après connexion/inscription, ou null si aucun retour
 * exploitable. Empêche tout open-redirect et n'envoie l'espace client qu'aux
 * utilisateurs ayant le rôle client.
 */
function retour_connexion_valide(?string $retour): ?string
{
    if ($retour === null || $retour === '' || !est_connecte() || utilisateur_role() !== ROLE_CLIENT) {
        return null;
    }

    parse_str($retour, $params);
    $route = $params['route'] ?? '';
    unset($params['route']);

    $routesClient = [
        'client',
        'client/commander',
        'client/panier',
        'client/produit',
        'client/mes-commandes',
        'client/detail-commande',
        'client/menu-semaine',
        'client/notifications',
    ];

    if (!in_array($route, $routesClient, true)) {
        return null;
    }

    $cible = 'route=' . $route;
    if (!empty($params)) {
        $cible .= '&' . http_build_query($params);
    }

    return 'index.php?' . $cible;
}
