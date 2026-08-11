<?php
/**
 * Configuration de l'envoi d'emails.
 *
 * L'application envoie des emails de deux façons, selon EMAIL_ENABLED :
 *
 * 1) EMAIL_ENABLED = false (DÉFAUT, mode développement)
 *    Aucun email n'est envoyé pour de vrai. À la place, chaque message est
 *    écrit dans logs/emails/YYYY-MM-DD.log pour permettre de tester le flux
 *    de bout en bout sans serveur SMTP. La route "Je m'inscris" (partenaire)
 *    et le lien envoyé restent 100 % fonctionnels en local.
 *
 * 2) EMAIL_ENABLED = true
 *    L'email est réellement expédié via la fonction mail() de PHP. Pour que
 *    l'envoi fonctionne, un serveur d'envoi doit être disponible :
 *    - sous XAMPP : configurer sendmail (C:\xampp\sendmail\sendmail.ini) puis
 *      décommenter sendmail_path dans php.ini ; ou
 *    - sous Linux : un MTA local (postfix, msmtp, ...) doit accepter les
 *      emails locaux ; ou
 *    - mettre MAIL_UTILISER_SMTP à true et renseigner un relais SMTP réel
 *      (les constantes EMAIL_SMTP_* ci-dessous sont alors utilisées).
 *
 * Ce qui manque pour activer l'envoi réel dans ce projet :
 *   1. passer EMAIL_ENABLED à true ;
 *   2. fournir un transport d'envoi (sendmail local OU relais SMTP) ;
 *   3. renseigner EMAIL_FROM / EMAIL_FROM_NAME avec une adresse d'expéditeur
 *      acceptée par le serveur de messagerie.
 *
 * Même quand EMAIL_ENABLED est true, une copie est toujours conservée dans
 * logs/emails/ pour la traçabilité.
 */

// Active l'envoi réel des emails (false = mode dev, les messages sont logués)
define('EMAIL_ENABLED', false);

// Expéditeur des emails
define('EMAIL_FROM', 'no-reply@fiajou3.local');
define('EMAIL_FROM_NAME', 'FiaJou3');

// Utiliser un relais SMTP explicite (requires EMAIL_ENABLED = true)
define('EMAIL_UTILISER_SMTP', false);
define('EMAIL_SMTP_HOST', 'smtp.example.com');
define('EMAIL_SMTP_PORT', 587);
define('EMAIL_SMTP_SECURE', 'tls'); // tls | ssl | '' (aucun)
define('EMAIL_SMTP_USER', '');
define('EMAIL_SMTP_PASS', '');

// Durée de validité d'un lien d'invitation partenaire (en secondes) : 48 h.
define('PARTENAIRE_INVITATION_DUREE', 172800);
