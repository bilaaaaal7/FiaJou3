<?php
/**
 * Modèle Mailer
 * Envoi d'emails applicatifs (invitation partenaire, etc.).
 *
 * - En mode développement (EMAIL_ENABLED = false, valeur par défaut) :
 *   aucun envoi réel ; chaque message est écrit dans logs/emails/YYYY-MM-DD.log
 *   pour pouvoir tester le flux complet sans serveur de messagerie.
 * - En production (EMAIL_ENABLED = true) : envoi réel via mail() (transport
 *   SMTP configuré sur le serveur) ou via un relais SMTP simple si
 *   EMAIL_UTILISER_SMTP est activé.
 *
 * Une copie de chaque message est toujours conservée dans logs/emails/ pour
 * la traçabilité et le débogage.
 */

require_once ROOT_PATH . '/config/email.php';

class MailerModele
{
    /**
     * Expédie un email HTML (+ version texte).
     *
     * @return bool true si le message a été expédié (ou journalisé en mode dev)
     */
    public function envoyer(string $destinataire, string $sujet, string $corpsHtml, string $corpsTexte = ''): bool
    {
        $corpsTexte = $corpsTexte !== '' ? $corpsTexte : $this->htmlVersTexte($corpsHtml);
        $destinataire = trim($destinataire);

        if (!filter_var($destinataire, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $journalise = $this->journaliser($destinataire, $sujet, $corpsHtml, $corpsTexte);

        if (!EMAIL_ENABLED) {
            // Mode développement : pas d'envoi réel, la journalisation suffit.
            return $journalise;
        }

        if (EMAIL_UTILISER_SMTP) {
            return $this->envoyerParSmtp($destinataire, $sujet, $corpsHtml, $corpsTexte) || $journalise;
        }

        return $this->envoyerParMail($destinataire, $sujet, $corpsHtml, $corpsTexte) || $journalise;
    }

    private function envoyerParMail(string $destinataire, string $sujet, string $corpsHtml, string $corpsTexte): bool
    {
        $encodedSubject = '=?UTF-8?B?' . base64_encode($sujet) . '?=';
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'Content-Transfer-Encoding: base64',
            'From: ' . $this->entete(EMAIL_FROM_NAME) . ' <' . EMAIL_FROM . '>',
            'Reply-To: ' . EMAIL_FROM,
            'X-Mailer: FiaJou3',
        ];

        return @mail($destinataire, $encodedSubject, base64_encode($corpsHtml), implode("\r\n", $headers));
    }

    private function envoyerParSmtp(string $destinataire, string $sujet, string $corpsHtml, string $corpsTexte): bool
    {
        $socket = @stream_socket_client(
            'tcp://' . EMAIL_SMTP_HOST . ':' . EMAIL_SMTP_PORT,
            $errno,
            $errstr,
            10
        );
        if (!$socket) {
            return false;
        }

        $smtp = new SmtpClient($socket);
        try {
            $smtp->attendre(220);
            $smtp->commander('EHLO ' . (gethostname() ?: 'localhost'), 250);
            $smtp->lireMultiLignes();

            if (EMAIL_SMTP_SECURE === 'tls' || EMAIL_SMTP_SECURE === 'ssl') {
                $smtp->commander('STARTTLS', 220);
                $ok = @stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                if (!$ok) {
                    fclose($socket);
                    return false;
                }
                $smtp->commander('EHLO ' . (gethostname() ?: 'localhost'), 250);
                $smtp->lireMultiLignes();
            }

            if (EMAIL_SMTP_USER !== '') {
                $smtp->commander('AUTH LOGIN', 334);
                $smtp->commander(base64_encode(EMAIL_SMTP_USER), 334);
                $smtp->commander(base64_encode(EMAIL_SMTP_PASS), 235);
            }

            $smtp->commander('MAIL FROM: <' . EMAIL_FROM . '>', 250);
            $smtp->commander('RCPT TO: <' . $destinataire . '>', 250);

            fwrite($socket, "DATA\r\n");
            $smtp->attendre(354);

            $corpsSmtp = preg_replace('/^\./m', '..', $corpsHtml);
            $message = 'From: ' . $this->entete(EMAIL_FROM_NAME) . ' <' . EMAIL_FROM . ">\r\n"
                     . 'To: <' . $destinataire . ">\r\n"
                     . 'Subject: ' . $sujet . "\r\n"
                     . 'MIME-Version: 1.0' . "\r\n"
                     . 'Content-Type: text/html; charset=UTF-8' . "\r\n"
                     . 'Content-Transfer-Encoding: 8bit' . "\r\n"
                     . "\r\n"
                     . $corpsSmtp . "\r\n.\r\n";
            fwrite($socket, $message . "\r\n");
            $smtp->attendre(250);

            $smtp->commander('QUIT', 221);
            fclose($socket);
            return true;
        } catch (Exception $e) {
            @fclose($socket);
            return false;
        }
    }

    /**
     * Journalise une copie du message dans logs/emails/ (traçabilité + mode dev).
     */
    private function journaliser(string $destinataire, string $sujet, string $corpsHtml, string $corpsTexte): bool
    {
        $fichier = ROOT_PATH . '/logs/emails/emails_' . date('Y-m-d') . '.log';
        $dir = dirname($fichier);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $bloc = sprintf(
            "[%s]\nA: %s\nSujet: %s\n--- HTML ---\n%s\n--- TEXTE ---\n%s\n----------------------------------------\n",
            date('Y-m-d H:i:s'),
            $destinataire,
            $sujet,
            $corpsHtml,
            $corpsTexte
        );

        return (bool) @file_put_contents($fichier, $bloc, FILE_APPEND | LOCK_EX);
    }

    private function entete(string $valeur): string
    {
        return '=?UTF-8?B?' . base64_encode($valeur) . '?=';
    }

    private function htmlVersTexte(string $html): string
    {
        $texte = preg_replace('#<br\s*/?>#i', "\n", $html);
        $texte = preg_replace('#</p>#i', "\n\n", $texte);
        $texte = strip_tags($texte);
        return trim(html_entity_decode($texte, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }
}

/**
 * Petit client SMTP interne minimaliste (dialogue requête/réponse).
 * N'est utilisé que lorsque EMAIL_UTILISER_SMTP est activé.
 */
class SmtpClient
{
    private $socket;

    public function __construct($socket)
    {
        $this->socket = $socket;
    }

    public function attendre(int $codeAttendu): void
    {
        $reponse = fgets($this->socket);
        if ($reponse === false || (int) substr($reponse, 0, 3) !== $codeAttendu) {
            throw new RuntimeException('SMTP : réponse inattendue : ' . var_export($reponse, true));
        }
    }

    public function commander(string $commande, int $codeAttendu = 250): void
    {
        fwrite($this->socket, $commande . "\r\n");
        $this->attendre($codeAttendu);
    }

    public function lireMultiLignes(): void
    {
        $derniere = '';
        do {
            $ligne = fgets($this->socket);
            if ($ligne === false) {
                throw new RuntimeException('SMTP : connexion fermée pendant la lecture.');
            }
            $derniere = $ligne;
        } while (isset($ligne[3]) && $ligne[3] === '-');
        if ((int) substr($derniere, 0, 3) !== 250) {
            throw new RuntimeException('SMTP : réponse inattendue : ' . $derniere);
        }
    }
}
