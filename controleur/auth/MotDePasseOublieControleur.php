<?php
/**
 * Contrôleur : Mot de passe oublié / Réinitialisation du mot de passe
 * Route : /mot-de-passe-oublie
 *
 * Reprend le même principe que le dossier partenaire (voir
 * PartenaireDemandeControleur / PartenaireControleur / PartenaireInvitationModele) :
 * un lien sécurisé, temporaire et à usage unique, envoyé par email.
 *
 * Cette page gère 3 étapes sur une seule route, avec le même mécanisme
 * de jeton en $_GET/$_POST que la page /partenaire :
 *
 *   1. GET  sans token         : formulaire "Email" (demande de lien).
 *   2. POST envoyer            : crée le jeton (si le compte existe) et
 *                                envoie l'email. Réponse TOUJOURS générique,
 *                                qu'un compte existe ou non (anti-énumération).
 *   3. GET  avec token         : valide le jeton (existe, non expiré, non
 *                                utilisé) puis affiche le formulaire de
 *                                nouveau mot de passe.
 *   4. POST reset               : revalide le jeton, vérifie le nouveau mot
 *                                de passe + sa confirmation, met à jour le
 *                                mot de passe (même hash que le reste de
 *                                l'application), invalide le jeton, puis
 *                                redirige vers /connexion avec un message
 *                                de succès (flash session).
 */

require_once ROOT_PATH . '/modele/UtilisateurModele.php';
require_once ROOT_PATH . '/modele/PasswordResetModele.php';
require_once ROOT_PATH . '/modele/MailerModele.php';
require_once ROOT_PATH . '/modele/RateLimiterModele.php';

$utilisateurModele = new UtilisateurModele();
$resetModele = new PasswordResetModele();

$message = "";
$erreur = "";
$modeReset = false;
$emailReset = "";
$tokenActuel = "";

// Message générique renvoyé après une demande, qu'un compte existe ou non
// pour l'email saisi (on ne révèle jamais si l'email est connu).
const MDP_OUBLIE_MESSAGE_GENERIQUE =
    "Si cette adresse email correspond à un compte, un lien de réinitialisation vous sera envoyé.";

/* =========================================================
   1) DEMANDE DU LIEN (POST envoyer)
========================================================= */
if (isset($_POST['envoyer'])) {

    $limiteur = new RateLimiterModele('mot_de_passe_oublie', 900, 5, 300);

    if (!$limiteur->peutTenter()) {
        $reste = $limiteur->tempsRestantBlocage();
        $erreur = 'Trop de demandes. Réessayez dans ' . ceil($reste / 60) . ' minute(s).';
    } else {
        $email = trim((string) ($_POST['email'] ?? ''));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $limiteur->enregistrerEchec();
            $erreur = "L'adresse email n'est pas valide.";
        } else {
            $limiteur->reinitialiser();

            $user = $utilisateurModele->findByEmail($email);

            // Le compte existe et n'est pas désactivé : on génère le lien et
            // on envoie l'email. Dans tous les autres cas (email inconnu,
            // compte désactivé), on ne fait rien de plus, mais le message
            // affiché reste identique pour ne jamais révéler l'existence
            // du compte.
            if ($user && (int) $user['actif'] === 1) {
                $resetModele->invaliderAnterieures((int) $user['id']);
                $demande = $resetModele->creer((int) $user['id'], $email);

                if ($demande) {
                    $lien = BASE_URL . '/index.php?route=mot-de-passe-oublie&token=' . urlencode($demande['token']);
                    $expireDate = date('d/m/Y H:i', strtotime($demande['expire_le']));

                    $sujet = APP_NAME . ' : réinitialisation de votre mot de passe';

                    $corpsHtml = '<div style="font-family:Arial,Helvetica,sans-serif;max-width:600px;margin:0 auto;color:#171717;">'
                        . '<h2 style="color:#B88618;">' . htmlspecialchars(APP_NAME) . '</h2>'
                        . '<p>Bonjour,</p>'
                        . '<p>Vous avez demandé la réinitialisation du mot de passe de votre compte ' . htmlspecialchars(APP_NAME) . '.</p>'
                        . '<p>Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe :</p>'
                        . '<p style="text-align:center;margin:28px 0;">'
                        . '<a href="' . htmlspecialchars($lien) . '" style="display:inline-block;padding:13px 30px;background:linear-gradient(135deg,#c8931f,#B88618);color:#ffffff;text-decoration:none;border-radius:50px;font-weight:700;">Réinitialiser mon mot de passe</a>'
                        . '</p>'
                        . '<p>Ce lien est personnel et temporaire : il expirera le <strong>' . htmlspecialchars($expireDate) . '</strong> '
                        . 'et ne peut être utilisé qu\'une seule fois.</p>'
                        . '<p>Si vous n\'êtes pas à l\'origine de cette demande, ignorez simplement cet email : votre mot de passe restera inchangé.</p>'
                        . '<p style="color:#8a8478;font-size:12px;border-top:1px solid #eee;padding-top:12px;margin-top:28px;">'
                        . APP_NAME . ' — Repas faits maison, livrés chez vous.</p>'
                        . '</div>';

                    $corpsTexte = "Bonjour,\n\n"
                        . "Vous avez demandé la réinitialisation du mot de passe de votre compte " . APP_NAME . ".\n\n"
                        . "Pour choisir un nouveau mot de passe, cliquez sur ce lien (valable jusqu'au {$expireDate}, usage unique) :\n"
                        . $lien . "\n\n"
                        . "Si vous n'êtes pas à l'origine de cette demande, ignorez simplement cet email : votre mot de passe restera inchangé.";

                    $mailer = new MailerModele();
                    $mailer->envoyer($email, $sujet, $corpsHtml, $corpsTexte);

                    journaliser_audit('mot_de_passe_oublie.demande', 'email="' . $email . '"');
                }
            }

            $message = MDP_OUBLIE_MESSAGE_GENERIQUE;
        }
    }
}

/* =========================================================
   2) SOUMISSION DU NOUVEAU MOT DE PASSE (POST reset)
========================================================= */
if (isset($_POST['reset'])) {

    $token = trim((string) ($_POST['token'] ?? ''));
    $nouveauMdp = (string) ($_POST['nouveau_mdp'] ?? '');
    $confirmation = (string) ($_POST['confirmation'] ?? '');

    $demande = $token !== '' ? $resetModele->trouverParToken($token) : false;

    if (!$demande) {
        $erreur = "Ce lien de réinitialisation est invalide. Merci de refaire une demande.";
    } elseif ((int) $demande['utilise'] === 1) {
        $erreur = "Ce lien a déjà été utilisé. Merci de refaire une demande.";
        $modeReset = true;
        $tokenActuel = $token;
        $emailReset = $demande['email'];
    } elseif (strtotime($demande['expire_le']) < time()) {
        $erreur = "Ce lien a expiré. Merci de refaire une demande.";
        $modeReset = true;
        $tokenActuel = $token;
        $emailReset = $demande['email'];
    } elseif (strlen($nouveauMdp) < 6) {
        $erreur = "Le mot de passe doit contenir au moins 6 caractères.";
        $modeReset = true;
        $tokenActuel = $token;
        $emailReset = $demande['email'];
    } elseif ($nouveauMdp !== $confirmation) {
        $erreur = "Les mots de passe ne correspondent pas.";
        $modeReset = true;
        $tokenActuel = $token;
        $emailReset = $demande['email'];
    } else {
        $utilisateurModele->changerMdp((int) $demande['user_id'], $nouveauMdp);
        $resetModele->marquerUtilise((int) $demande['id']);

        journaliser_audit('mot_de_passe_oublie.reinitialise', 'email="' . $demande['email'] . '"');

        // Message de succès affiché sur la page Connexion, puis nettoyé
        // (flash session à usage unique).
        $_SESSION['flash_succes'] = 'Mot de passe réinitialisé avec succès. Vous pouvez vous connecter.';
        header('Location: ' . BASE_URL . '/index.php?route=connexion');
        exit;
    }
}

/* =========================================================
   3) OUVERTURE DU LIEN REÇU PAR EMAIL (GET avec token)
========================================================= */
if (!isset($_POST['envoyer']) && !isset($_POST['reset'])) {
    $tokenGet = trim((string) ($_GET['token'] ?? ''));

    if ($tokenGet !== '') {
        $demande = $resetModele->trouverParToken($tokenGet);

        if (!$demande) {
            $erreur = "Ce lien de réinitialisation est invalide. Merci de refaire une demande.";
        } elseif ((int) $demande['utilise'] === 1) {
            $erreur = "Ce lien a déjà été utilisé. Merci de refaire une demande.";
        } elseif (strtotime($demande['expire_le']) < time()) {
            $erreur = "Ce lien a expiré. Merci de refaire une demande.";
        } else {
            $modeReset = true;
            $tokenActuel = $tokenGet;
            $emailReset = $demande['email'];
        }
    }
}

require ROOT_PATH . '/vue/auth/mot_de_passe_oublie.php';
