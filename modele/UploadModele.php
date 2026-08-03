<?php
/**
 * Modèle Upload
 * Gère l'enregistrement sécurisé des images uploadées (plats, catégories).
 * Centralisé ici pour éviter la duplication entre PlatModele et CategorieModele
 * et pour corriger les failles de l'ancienne implémentation :
 *   - le nom de fichier n'est plus celui envoyé par le navigateur (évite
 *     l'écrasement d'un autre fichier ou l'injection de caractères spéciaux) ;
 *   - le type réel du fichier est vérifié via finfo (pas seulement l'extension
 *     déclarée par le client, facilement falsifiable) ;
 *   - seules les extensions image sont acceptées (empêche l'upload d'un .php) ;
 *   - une taille maximale est imposée.
 */

class UploadModele
{
    private const EXTENSIONS_AUTORISEES = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];

    private const MIME_AUTORISES = [
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png'  => ['png'],
        'image/webp' => ['webp'],
        'image/gif'  => ['gif'],
        'image/avif' => ['avif'],
    ];

    private const TAILLE_MAX = 5 * 1024 * 1024; // 5 Mo

    /**
     * Enregistre un fichier uploadé ($_FILES['xxx']) dans UPLOADS_PATH et
     * retourne le nom de fichier généré, ou null si aucun fichier valide
     * n'a été envoyé (ex : champ optionnel laissé vide).
     *
     * @throws RuntimeException si un fichier a été envoyé mais est invalide
     *                          (type non autorisé, trop lourd, erreur d'upload).
     */
    public static function enregistrer(array $fichier): ?string
    {
        // Champ vide ou aucun fichier sélectionné : rien à faire.
        if (!isset($fichier['error']) || $fichier['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($fichier['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException("Erreur lors de l'envoi du fichier (code {$fichier['error']}).");
        }

        if (!is_uploaded_file($fichier['tmp_name'])) {
            throw new RuntimeException("Envoi de fichier invalide.");
        }

        if ($fichier['size'] > self::TAILLE_MAX) {
            throw new RuntimeException("L'image dépasse la taille maximale autorisée (5 Mo).");
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($fichier['tmp_name']);

        if (!isset(self::MIME_AUTORISES[$mime])) {
            throw new RuntimeException("Type de fichier non autorisé. Formats acceptés : jpg, png, webp, gif, avif.");
        }

        $extensionDeclaree = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));
        $extension = in_array($extensionDeclaree, self::MIME_AUTORISES[$mime], true)
            ? $extensionDeclaree
            : self::MIME_AUTORISES[$mime][0];

        if (!in_array($extension, self::EXTENSIONS_AUTORISEES, true)) {
            throw new RuntimeException("Extension de fichier non autorisée.");
        }

        if (!is_dir(UPLOADS_PATH)) {
            mkdir(UPLOADS_PATH, 0755, true);
        }

        // Nom de fichier généré côté serveur : évite les collisions et toute
        // tentative d'injection via le nom d'origine (ex: ../../evil.php).
        $nomFichier = bin2hex(random_bytes(16)) . '.' . $extension;

        if (!move_uploaded_file($fichier['tmp_name'], UPLOADS_PATH . '/' . $nomFichier)) {
            throw new RuntimeException("Impossible d'enregistrer l'image sur le serveur.");
        }

        return $nomFichier;
    }

    /**
     * Supprime un fichier du dossier uploads (ex : ancienne image remplacée).
     * Ne fait rien si le fichier n'existe pas / nom vide.
     */
    public static function supprimer(?string $nomFichier): void
    {
        if (empty($nomFichier)) {
            return;
        }

        $chemin = UPLOADS_PATH . '/' . basename($nomFichier);
        if (is_file($chemin)) {
            @unlink($chemin);
        }
    }
}
