<?php
/**
 * Contrôleur : Demande de dossier partenaire (AJAX)
 * Route : /partenaire/demande
 *
 * Point d'entrée de la modale "Rejoignez FiaJou3" :
 *   1. reçoit l'email + le type de partenariat (cuisinier | livreur) ;
 *   2. crée une invitation sécurisée et temporaire (jeton unique) ;
 *   3. envoie un email contenant le lien de complétion du dossier ;
 *   4. répond en JSON à la modale.
 *
 * Ce flux est volontairement distinct du Register client, qui reste inchangé.
 */

require_once ROOT_PATH . '/modele/PartenaireInvitationModele.php';
require_once ROOT_PATH . '/modele/MailerModele.php';
require_once ROOT_PATH . '/modele/RateLimiterModele.php';

header('Content-Type: application/json; charset=utf-8');

$repondre = static function (bool $ok, string $message): void {
    echo json_encode(['ok' => $ok, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
};

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    $repondre(false, 'Requête invalide.');
}

$limiteur = new RateLimiterModele('partenaire_demande', 900, 5, 300);
if (!$limiteur->peutTenter()) {
    $reste = $limiteur->tempsRestantBlocage();
    $repondre(false, 'Trop de demandes. Réessayez dans ' . ceil($reste / 60) . ' minute(s).');
}

$email = trim((string) ($_POST['email'] ?? ''));
$role  = (string) ($_POST['role'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $limiteur->enregistrerEchec();
    $repondre(false, "L'adresse email n'est pas valide.");
}

if (!in_array($role, [ROLE_CUISINIER, ROLE_LIVREUR], true)) {
    $limiteur->enregistrerEchec();
    $repondre(false, 'Type de partenariat invalide.');
}

$limiteur->reinitialiser();

// Un seul lien actif par email : les invitations précédentes non utilisées
// sont invalidées avant d'en créer une nouvelle.
$invitationModele = new PartenaireInvitationModele();
$invitationModele->invaliderAnterieures($email);

$invitation = $invitationModele->creer($email, $role);
if (!$invitation) {
    $repondre(false, "Une erreur est survenue. Veuillez réessayer.");
}

$lien = BASE_URL . '/index.php?route=partenaire&token=' . urlencode($invitation['token']);

$roleLabel = $role === ROLE_CUISINIER ? 'cuisinier partenaire' : 'livreur partenaire';
$roleTitre = $role === ROLE_CUISINIER ? 'Devenir cuisinier partenaire' : 'Devenir livreur partenaire';
$expireDate = date('d/m/Y H:i', strtotime($invitation['expire_le']));

$sujet = APP_NAME . ' : complétez votre dossier ' . $roleLabel;

$corpsHtml = '<div style="font-family:Arial,Helvetica,sans-serif;max-width:600px;margin:0 auto;color:#171717;">'
    . '<h2 style="color:#B88618;">' . htmlspecialchars(APP_NAME) . '</h2>'
    . '<p>Bonjour,</p>'
    . '<p>Vous avez demandé à rejoindre FiaJou3 en tant que <strong>' . htmlspecialchars($roleLabel) . '</strong>.</p>'
    . '<p>Cliquez sur le bouton ci-dessous pour compléter votre dossier :</p>'
    . '<p style="text-align:center;margin:28px 0;">'
    . '<a href="' . htmlspecialchars($lien) . '" style="display:inline-block;padding:13px 30px;background:linear-gradient(135deg,#c8931f,#B88618);color:#ffffff;text-decoration:none;border-radius:50px;font-weight:700;">Compléter mon dossier</a>'
    . '</p>'
    . '<p>Ce lien est personnel et temporaire : il expirera le <strong>' . htmlspecialchars($expireDate) . '</strong> '
    . 'et ne peut être utilisé qu\'une seule fois.</p>'
    . '<p>Si vous n\'êtes pas à l\'origine de cette demande, ignorez simplement cet email.</p>'
    . '<p style="color:#8a8478;font-size:12px;border-top:1px solid #eee;padding-top:12px;margin-top:28px;">'
    . APP_NAME . ' — Repas faits maison, livrés chez vous.</p>'
    . '</div>';

$corpsTexte = "Bonjour,\n\n"
    . "Vous avez demandé à rejoindre " . APP_NAME . " en tant que {$roleLabel}.\n\n"
    . "Pour compléter votre dossier, cliquez sur ce lien (valable jusqu'au {$expireDate}, usage unique) :\n"
    . $lien . "\n\n"
    . "Si vous n'êtes pas à l'origine de cette demande, ignorez simplement cet email.";

$mailer = new MailerModele();
if (!$mailer->envoyer($email, $sujet, $corpsHtml, $corpsTexte)) {
    $repondre(false, "L'email n'a pas pu être envoyé. Veuillez réessayer dans quelques minutes.");
}

journaliser_audit('partenaire.demande', 'email="' . $email . '" role="' . $role . '"');

$repondre(
    true,
    'Un email vient d\'être envoyé à <strong>' . htmlspecialchars($email) . '</strong> avec votre lien de complétion. Vérifiez votre boîte de réception.'
);
