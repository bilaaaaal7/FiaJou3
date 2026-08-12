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
require_once ROOT_PATH . '/assets/inc/langue.php';

header('Content-Type: application/json; charset=utf-8');

// Messages renvoyés à la modale (UI), traduits selon la langue active de la
// requête. Le contenu de l'email d'invitation reste en français pour
// l'instant (hors périmètre de cette passe i18n front-end).
$L = langue_actuelle();
$msg = [
    'fr' => [
        'requeteInvalide'  => 'Requête invalide.',
        'tropDeDemandes'   => 'Trop de demandes. Réessayez dans %d minute(s).',
        'emailInvalide'    => "L'adresse email n'est pas valide.",
        'roleInvalide'     => 'Type de partenariat invalide.',
        'erreurCreation'   => 'Une erreur est survenue. Veuillez réessayer.',
        'erreurEnvoi'      => "L'email n'a pas pu être envoyé. Veuillez réessayer dans quelques minutes.",
        'succes'           => 'Un email vient d\'être envoyé à <strong>%s</strong> avec votre lien de complétion. Vérifiez votre boîte de réception.',
    ],
    'en' => [
        'requeteInvalide'  => 'Invalid request.',
        'tropDeDemandes'   => 'Too many requests. Please try again in %d minute(s).',
        'emailInvalide'    => 'This email address is not valid.',
        'roleInvalide'     => 'Invalid partnership type.',
        'erreurCreation'   => 'Something went wrong. Please try again.',
        'erreurEnvoi'      => 'The email could not be sent. Please try again in a few minutes.',
        'succes'           => 'An email was just sent to <strong>%s</strong> with your link to complete your application. Check your inbox.',
    ],
    'ar' => [
        'requeteInvalide'  => 'طلب غير صالح.',
        'tropDeDemandes'   => 'عدد كبير جدًا من الطلبات. يُرجى المحاولة بعد %d دقيقة (دقائق).',
        'emailInvalide'    => 'عنوان البريد الإلكتروني غير صالح.',
        'roleInvalide'     => 'نوع الشراكة غير صالح.',
        'erreurCreation'   => 'حدث خطأ ما. يُرجى المحاولة مجددًا.',
        'erreurEnvoi'      => 'تعذّر إرسال البريد الإلكتروني. يُرجى المحاولة بعد دقائق قليلة.',
        'succes'           => 'تم للتو إرسال بريد إلكتروني إلى <strong>%s</strong> يحتوي على رابط إكمال طلبك. تحقق من بريدك الوارد.',
    ],
][$L] ?? null;
// langue_actuelle() ne retourne que 'fr' | 'en' | 'ar' (cf. langues_supportees()) :
// $msg est donc toujours défini ci-dessus.

$repondre = static function (bool $ok, string $message): void {
    echo json_encode(['ok' => $ok, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
};

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    $repondre(false, $msg['requeteInvalide']);
}

$limiteur = new RateLimiterModele('partenaire_demande', 900, 5, 300);
if (!$limiteur->peutTenter()) {
    $reste = $limiteur->tempsRestantBlocage();
    $repondre(false, sprintf($msg['tropDeDemandes'], (int) ceil($reste / 60)));
}

$email = trim((string) ($_POST['email'] ?? ''));
$role  = (string) ($_POST['role'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $limiteur->enregistrerEchec();
    $repondre(false, $msg['emailInvalide']);
}

if (!in_array($role, [ROLE_CUISINIER, ROLE_LIVREUR], true)) {
    $limiteur->enregistrerEchec();
    $repondre(false, $msg['roleInvalide']);
}

$limiteur->reinitialiser();

// Un seul lien actif par email : les invitations précédentes non utilisées
// sont invalidées avant d'en créer une nouvelle.
$invitationModele = new PartenaireInvitationModele();
$invitationModele->invaliderAnterieures($email);

$invitation = $invitationModele->creer($email, $role);
if (!$invitation) {
    $repondre(false, $msg['erreurCreation']);
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
    $repondre(false, $msg['erreurEnvoi']);
}

journaliser_audit('partenaire.demande', 'email="' . $email . '" role="' . $role . '"');

$repondre(
    true,
    sprintf($msg['succes'], htmlspecialchars($email))
);
