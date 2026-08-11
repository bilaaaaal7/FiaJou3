<?php
/**
 * Contrôleur : Complément d'inscription Google
 * Route : /auth/google/complete
 *
 * Étape intermédiaire, atteinte uniquement depuis GoogleCallbackControleur
 * lorsqu'un nouvel utilisateur se connecte avec Google pour la première
 * fois. Google ne fournit jamais le téléphone (obligatoire dans
 * `profiles`, voir RegisterControleur) : on ne demande ici QUE les
 * informations manquantes, prénom/nom/email restant ceux renvoyés par
 * Google (affichés en lecture seule, non modifiables sur cette page).
 *
 * Le profil Google en attente ($_SESSION['google_pending']) est un jeton de
 * session à usage unique : posé par GoogleCallbackControleur, consommé
 * (et supprimé) à la création du compte, jamais rejouable.
 */

require_once ROOT_PATH . '/modele/UtilisateurModele.php';

$pending = $_SESSION['google_pending'] ?? null;

if (!is_array($pending) || empty($pending['google_id']) || empty($pending['email'])) {
    // Page atteinte directement sans passer par Google (lien direct,
    // session expirée...) : rien à compléter, retour à la connexion.
    rediriger_avec_erreur('connexion', "Votre session d'inscription Google a expiré. Merci de réessayer.");
}

$utilisateurModele = new UtilisateurModele();
$error = "";

if (isset($_POST['completer'])) {
    // Revalide que l'email n'a pas été pris entre-temps par un autre compte
    // (ex: deux onglets, inscription classique concurrente).
    $existant = $utilisateurModele->findByEmail($pending['email']);
    if ($existant) {
        unset($_SESSION['google_pending']);
        rediriger_avec_erreur('connexion', "Un compte existe déjà avec cet email. Connectez-vous normalement.");
    }

    $telephone = trim((string) ($_POST['telephone'] ?? ''));
    $adresse = trim((string) ($_POST['adresse'] ?? ''));
    $ville = trim((string) ($_POST['ville'] ?? ''));

    if ($telephone === '') {
        $error = "Le numéro de téléphone est obligatoire.";
    } else {
        $userId = $utilisateurModele->creerCompteGoogle([
            'email'     => $pending['email'],
            'google_id' => $pending['google_id'],
            'prenom'    => $pending['prenom'],
            'nom'       => $pending['nom'],
            'telephone' => $telephone,
            'adresse'   => $adresse,
            'ville'     => $ville,
        ]);

        unset($_SESSION['google_pending']);

        $profile = $utilisateurModele->findProfileByUserId($userId);

        $_SESSION['user_id'] = $userId;
        $_SESSION['prenom']  = $profile['prenom'];
        $_SESSION['role']    = $profile['role'];
        $_SESSION['email']   = $pending['email'];

        journaliser_audit('inscription.google', 'email="' . $pending['email'] . '"');

        $retour = $_SESSION['google_oauth_retour'] ?? null;
        unset($_SESSION['google_oauth_retour']);

        $cible = retour_connexion_valide($retour);
        if ($cible !== null) {
            header('Location: ' . BASE_URL . '/' . $cible);
            exit;
        }

        header('Location: ' . BASE_URL . '/index.php?route=' . route_par_defaut_pour_role($profile['role']));
        exit;
    }
}

require ROOT_PATH . '/vue/auth/google_complete.php';
