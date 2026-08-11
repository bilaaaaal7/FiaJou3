<?php
/**
 * Contrôleur : Dossier partenaire (cuisinier / livreur)
 * Route : /partenaire
 *
 * Page ouverte depuis le lien reçu par email :
 *   - GET  : valide le jeton sécurisé (usage unique + expiration) puis affiche
 *            le formulaire de complétion correspondant au type de partenariat ;
 *   - POST : re-valide le jeton, vérifie les champs, puis crée le compte
 *            (ou met à jour le compte existant de la même adresse, sans créer
 *            de doublon) et connecte l'utilisateur.
 *
 * Le Register classique (clients) n'est jamais utilisé par ce flux.
 */

require_once ROOT_PATH . '/modele/UtilisateurModele.php';
require_once ROOT_PATH . '/modele/PartenaireInvitationModele.php';

$utilisateurModele = new UtilisateurModele();
$invitationModele  = new PartenaireInvitationModele();

$error = '';
$invitation = null;

$token = trim((string) ($_POST['token'] ?? $_GET['token'] ?? ''));

if ($token === '') {
    $error = "Ce lien est invalide ou incomplet.";
} else {
    $invitation = $invitationModele->trouverParToken($token);
    if (!$invitation) {
        $error = "Ce lien est invalide.";
    } elseif ((int) $invitation['utilise'] === 1) {
        $error = "Ce lien a déjà été utilisé. Contactez-nous si vous souhaitez compléter un nouveau dossier.";
    } elseif (strtotime($invitation['expire_le']) < time()) {
        $error = "Ce lien a expiré. Retournez sur la page d'accueil pour demander un nouveau lien.";
    }
}

$role = $invitation ? $invitation['role'] : '';
$roleLabel = $role === ROLE_CUISINIER ? 'cuisinier partenaire' : ($role === ROLE_LIVREUR ? 'livreur partenaire' : '');

if (isset($_POST['partenaire']) && !$error) {

    $prenom       = trim((string) ($_POST['prenom'] ?? ''));
    $nom          = trim((string) ($_POST['nom'] ?? ''));
    $email        = trim((string) ($_POST['email'] ?? ''));
    $telephone    = trim((string) ($_POST['telephone'] ?? ''));
    $adresse      = trim((string) ($_POST['adresse'] ?? ''));
    $ville        = trim((string) ($_POST['ville'] ?? ''));
    $password     = (string) ($_POST['password'] ?? '');
    $confirmation = (string) ($_POST['confirmation'] ?? '');

    if (empty($prenom) || empty($nom) || empty($telephone) || empty($email)) {
        $error = "Tous les champs obligatoires doivent être remplis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "L'adresse email n'est pas valide.";
    } elseif (strtolower($email) !== strtolower($invitation['email'])) {
        $error = "L'email ne correspond pas à celui du lien reçu. Utilisez l'email demandé.";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères.";
    } elseif ($password !== $confirmation) {
        $error = "Les mots de passe ne correspondent pas.";
    }

    if (!$error) {
        $existant = $utilisateurModele->findByEmail($email);
        $userId = null;

        if ($existant) {
            $profil = $utilisateurModele->findProfileByUserId($existant['id']);
            $roleActuel = $profil['role'] ?? ROLE_CLIENT;

            if ($roleActuel === ROLE_ADMIN) {
                $error = "Cet email est déjà associé à un compte administrateur.";
            } elseif ($roleActuel !== ROLE_CLIENT && $roleActuel !== $role) {
                $error = "Cet email est déjà associé à un compte de type différent. Contactez-nous.";
            } else {
                // L'email existe déjà : on NE crée PAS de doublon. On complète
                // le dossier du compte existant (profil + rôle + mot de passe).
                $utilisateurModele->mettreAJour($existant['id'], [
                    'prenom' => $prenom, 'nom' => $nom, 'email' => $email,
                    'telephone' => $telephone, 'adresse' => $adresse,
                    'ville' => $ville, 'role' => $role,
                ]);
                $utilisateurModele->changerMdp($existant['id'], $password);
                $userId = $existant['id'];
            }
        } else {
            $userId = $utilisateurModele->creerComptePersonnel([
                'prenom' => $prenom, 'nom' => $nom, 'email' => $email,
                'telephone' => $telephone, 'adresse' => $adresse,
                'ville' => $ville, 'password' => $password,
            ], $role);
        }

        if ($userId) {
            $invitationModele->marquerUtilisee((int) $invitation['id'], $userId);
            journaliser_audit('partenaire.completer', 'email="' . $email . '" role="' . $role . '" user_id=' . $userId);

            $profile = $utilisateurModele->findProfileByUserId($userId);
            $_SESSION['user_id'] = $userId;
            $_SESSION['prenom']  = $profile['prenom'] ?? $prenom;
            $_SESSION['role']    = $profile['role'] ?? $role;
            $_SESSION['email']   = $email;

            header('Location: ' . BASE_URL . '/index.php?route=' . route_par_defaut_pour_role($role));
            exit;
        }
    }
}

require ROOT_PATH . '/vue/partenaire.php';
