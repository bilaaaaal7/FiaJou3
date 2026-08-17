<?php
/**
 * Système de langue (i18n) — résolution de la langue active côté serveur.
 *
 * Persistance :
 *   - visiteur anonyme : cookie « fiajou3_lang » (et localStorage côté JS) ;
 *   - compte connecté  : colonne profiles.langue, posée en $_SESSION['langue']
 *     à la connexion (voir LoginControleur) et mise à jour par le sélecteur
 *     via le contrôleur de langue (route « langue »).
 *
 * Le moteur JS (assets/js/i18n.js) reçoit la langue résolue via
 * window.FJ_I18N, posé par assets/inc/header.php sur les pages participantes.
 */

require_once __DIR__ . '/session.php';

function langues_supportees(): array
{
    return ['fr', 'en', 'ar'];
}

function langue_valide(?string $langue): bool
{
    return $langue !== null && in_array($langue, langues_supportees(), true);
}

/**
 * Langue active pour la requête courante.
 * Ordre de priorité : session (posée à la connexion ou par le sélecteur)
 * → cookie du navigateur → français par défaut.
 */
function langue_actuelle(): string
{
    static $langue = null;
    if ($langue !== null) {
        return $langue;
    }
    if (langue_valide($_SESSION['langue'] ?? null)) {
        $langue = $_SESSION['langue'];
        return $langue;
    }
    if (langue_valide($_COOKIE['fiajou3_lang'] ?? null)) {
        $langue = $_COOKIE['fiajou3_lang'];
        return $langue;
    }
    $langue = 'fr';
    return $langue;
}

/**
 * Résout un champ multilingue stocké en base dans la langue active.
 *
 * La colonne de base (`nom`, `description`…) est la valeur française ;
 * les colonnes `*_en` / `*_ar` contiennent les traductions optionnelles.
 * Si la traduction demandée est vide ou absente, la valeur de base est
 * renvoyée. Aucune traduction automatique : seule la saisie admin est servie.
 */
function localiser(array $ligne, string $champ): string
{
    $langue = langue_actuelle();
    if ($langue !== 'fr') {
        $cleLocale = $champ . '_' . $langue;
        if (!empty($ligne[$cleLocale])) {
            return (string) $ligne[$cleLocale];
        }
    }
    return (string) ($ligne[$champ] ?? '');
}

/**
 * Dictionnaire des textes rendus côté serveur : titres/confirmations
 * construits dynamiquement (ex. « Commande #12 »). Le reste du contenu
 * est traduit côté client via assets/js/i18n.js.
 *
 * @return array<string, array<string, string>>
 */
function dict_serveur(): array
{
    return [
        'dyn.commande'       => ['fr' => 'Commande', 'en' => 'Order', 'ar' => 'الطلب'],
        'dyn.livraison'      => ['fr' => 'Livraison', 'en' => 'Delivery', 'ar' => 'التوصيل'],
        'dyn.cuisinier'      => ['fr' => 'cuisinier', 'en' => 'cook', 'ar' => 'طباخ'],
        'dyn.livreur'        => ['fr' => 'livreur', 'en' => 'courier', 'ar' => 'ساعي'],
        'dyn.administrateur' => ['fr' => 'Administrateur', 'en' => 'Administrator', 'ar' => 'مدير النظام'],
    ];
}

/**
 * Traduction d'une clé de dictionnaire serveur, avec substitution des
 * placeholders « {param} ». Retourne la clé telle quelle si inconnue.
 */
function __(string $cle, array $params = []): string
{
    static $langue = null;
    if ($langue === null) {
        $langue = langue_actuelle();
    }
    $dict = dict_serveur();
    $texte = $dict[$cle][$langue] ?? $dict[$cle]['fr'] ?? $cle;
    foreach ($params as $k => $v) {
        $texte = str_replace('{' . $k . '}', (string) $v, $texte);
    }
    return $texte;
}

/**
 * Jours de la semaine (index 0 = dimanche), dans la langue active.
 *
 * @return array<int, string>
 */
function jours_localises(?string $langue = null): array
{
    $langue = $langue ?? langue_actuelle();
    $jours = [
        'fr' => [0 => 'dimanche', 1 => 'lundi', 2 => 'mardi', 3 => 'mercredi', 4 => 'jeudi', 5 => 'vendredi', 6 => 'samedi'],
        'en' => [0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'],
        'ar' => [0 => 'الأحد', 1 => 'الاثنين', 2 => 'الثلاثاء', 3 => 'الأربعاء', 4 => 'الخميس', 5 => 'الجمعة', 6 => 'السبت'],
    ];
    return $jours[$langue] ?? $jours['fr'];
}

/**
 * Mois de l'année (index 1 = janvier), dans la langue active.
 *
 * @return array<int, string>
 */
function mois_localises(?string $langue = null): array
{
    $langue = $langue ?? langue_actuelle();
    $mois = [
        'fr' => [1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril', 5 => 'mai', 6 => 'juin', 7 => 'juillet', 8 => 'août', 9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre'],
        'en' => [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'],
        'ar' => [1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل', 5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس', 9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'],
    ];
    return $mois[$langue] ?? $mois['fr'];
}

/**
 * Date du jour localisée, ex. « Lundi 12 août 2026 ».
 */
function date_bienvenue(?string $langue = null): string
{
    $jours = jours_localises($langue);
    $mois = mois_localises($langue);
    return ucfirst($jours[(int) date('w')]) . ' ' . date('j') . ' ' . $mois[(int) date('n')] . ' ' . date('Y');
}

/**
 * Résout un texte français stocké en base ou transmis par rediriger_avec_erreur
 * (notification, commentaire d'activité, message d'erreur) en informations de
 * traduction utilisables par render_i18n() :
 *
 *   - ['cle' => ..., 'params' => [...]] : remplacement complet (cle de i18n.js) ;
 *   - ['clePrefixe' => ..., 'prefixeFr' => ..., 'suffixe' => ...] : préfixe traduit
 *     suivi d'un suffixe libre (ex. « Problème signalé : <texte utilisateur> »).
 *
 * Retourne null si le texte n'est pas reconnu (il sera alors affiché tel quel).
 */
function infos_i18n(?string $texte): ?array
{
    $texte = trim((string) $texte);
    if ($texte === '') {
        return null;
    }

    $exacts = [
        'Nouvelle commande assignée'  => ['cle' => 'notif.nouvelleCommandeAssigne'],
        'Nouvelle livraison assignée' => ['cle' => 'notif.nouvelleLivraisonAssigne'],
        'Commande confirmée'          => ['cle' => 'notif.commandeConfirmee'],
        'Livraison démarrée'          => ['cle' => 'activite.livraisonDemarree'],
        'Livrée'                      => ['cle' => 'activite.livree'],
        'Identifiant de commande invalide.' => ['cle' => 'erreurs.commandeInvalide'],
        'Commande introuvable.'       => ['cle' => 'erreurs.commandeIntrouvable'],
        "Vous n'avez pas accès à cette commande." => ['cle' => 'erreurs.accesRefuse'],
        'Transition de statut non autorisée.' => ['cle' => 'erreurs.transitionNonAutorisee'],
        'Commande ou statut invalide.' => ['cle' => 'erreurs.commandeStatutInvalide'],
        'Tous les champs obligatoires doivent être remplis.' => ['cle' => 'erreurs.champsRequis'],
        'Veuillez remplir tous les champs correctement.' => ['cle' => 'erreurs.champsCorrects'],
        'Le mot de passe doit contenir au moins 6 caractères.' => ['cle' => 'erreurs.motDePasseCourt'],
        'Cet email est déjà utilisé.' => ['cle' => 'erreurs.emailUtilise'],
        'Impossible de supprimer cet utilisateur : il a des commandes ou données associées. Vous pouvez le désactiver à la place.' => ['cle' => 'erreurs.suppressionUtilisateur'],
        'Impossible de supprimer ce cuisinier : il a des commandes ou données associées. Vous pouvez le désactiver à la place.' => ['cle' => 'erreurs.suppressionCuisinier'],
        'Impossible de supprimer ce livreur : il a des commandes ou données associées. Vous pouvez le désactiver à la place.' => ['cle' => 'erreurs.suppressionLivreur'],
        'Impossible de supprimer cette zone : elle est utilisée par des commandes existantes.' => ['cle' => 'erreurs.suppressionZone'],
        'Impossible de supprimer ce plat : il fait partie de commandes existantes. Vous pouvez le rendre indisponible à la place.' => ['cle' => 'erreurs.suppressionPlat'],
        "Impossible de supprimer cette catégorie : elle contient encore des plats. Supprimez ou déplacez d'abord ses plats." => ['cle' => 'erreurs.suppressionCategorie'],
    ];
    if (isset($exacts[$texte])) {
        return $exacts[$texte];
    }

    $modeles = [
        ['/^La commande #(\d+) vous a été assignée\.$/',            'notif.commandeAssigne', 1],
        ['/^La commande #(\d+) vous a été assignée pour livraison\.$/', 'notif.commandeAssigneLivraison', 1],
        ['/^Votre commande #(\d+) est en cours de traitement\.$/',  'notif.commandeTraitement', 1],
        ['/^Votre commande #(\d+) a été enregistrée avec succès\.$/', 'notif.commandeEnregistree', 1],
        ['/^Votre commande #(\d+) est en route vers vous\.$/',      'notif.commandeEnRoute', 1],
        ['/^Votre commande #(\d+) a été livrée avec succès\.$/',    'notif.commandeLivree', 1],
        ['/^Votre commande #(\d+) est en préparation\.$/',          'notif.commandePreparation', 1],
        ['/^Votre commande #(\d+) est prête\.$/',                   'notif.commandePrete', 1],
        ['/^Commande #(\d+) assignée$/',                            'notif.commandeAssigneTitre', 1],
        ['/^Commande #(\d+) en cours de traitement$/',              'notif.commandeTraitementTitre', 1],
        ['/^Commande #(\d+) en livraison$/',                        'notif.commandeEnLivraisonTitre', 1],
        ['/^Commande #(\d+) livrée$/',                              'notif.commandeLivreeTitre', 1],
        ['/^Commande #(\d+)$/',                                     'notif.commande', 1],
    ];
    foreach ($modeles as $modele) {
        if (preg_match($modele[0], $texte, $captures)) {
            return ['cle' => $modele[1], 'params' => ['id' => (int) $captures[$modele[2]]]];
        }
    }

    if (preg_match('/^Problème signalé\s*:\s*(.*)$/u', $texte, $captures)) {
        return [
            'clePrefixe' => 'activite.problemeSignale',
            'prefixeFr'  => 'Problème signalé :',
            'suffixe'    => trim($captures[1]),
        ];
    }

    return null;
}

/**
 * Rend un texte (notification, commentaire d'activité, erreur) sous forme de
 * balise HTML traduisible : un élément [data-i18n] (avec [data-i18n-params]
 * le cas échéant) si le texte est reconnu, sinon le texte brut échappé.
 * À utiliser dans un contexte HTML (echo direct).
 */
function render_i18n(?string $texte, string $classe = ''): string
{
    $texte = (string) $texte;
    if (trim($texte) === '') {
        return '';
    }
    $classeAttr = $classe !== '' ? ' class="' . htmlspecialchars($classe) . '"' : '';
    $infos = infos_i18n($texte);

    if ($infos === null) {
        return '<span' . $classeAttr . '>' . htmlspecialchars($texte) . '</span>';
    }

    if (isset($infos['cle'])) {
        $paramsAttr = '';
        if (!empty($infos['params'])) {
            $json = json_encode($infos['params'], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP);
            $paramsAttr = ' data-i18n-params="' . htmlspecialchars($json, ENT_QUOTES) . '"';
        }
        return '<span' . $classeAttr . ' data-i18n="' . htmlspecialchars($infos['cle']) . '"' . $paramsAttr . '>'
            . htmlspecialchars($texte) . '</span>';
    }

    $prefixe = '<span' . $classeAttr . '><span data-i18n="' . htmlspecialchars($infos['clePrefixe']) . '">'
        . htmlspecialchars($infos['prefixeFr'] ?? $texte) . '</span>';
    return $prefixe . htmlspecialchars($infos['suffixe'] ?? '') . '</span>';
}

/**
 * Clé i18n (dictionnaire JS) correspondant à un texte reconnu, sinon le texte
 * brut — utilisée pour passer une erreur à un composant JS (modales) qui
 * résout les clés via window.fjI18n (voir modal-form.js).
 */
function cle_i18n(?string $texte): string
{
    $infos = infos_i18n($texte);
    return $infos !== null && isset($infos['cle']) ? $infos['cle'] : (string) $texte;
}
