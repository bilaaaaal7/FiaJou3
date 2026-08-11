<?php
/**
 * En-tête commun (balise <head> + ouverture du <body>).
 * Variables attendues (optionnelles) avant l'include :
 *   $pageTitle       : string - titre de l'onglet (défini par défaut par
 *                       route dans urlRewrite.php, surchargeable par la vue)
 *   $metaDescription : string - meta description SEO (idem, via urlRewrite.php)
 *   $metaKeywords    : string - meta keywords SEO, si pertinent (idem)
 *   $extraCss        : array  - fichiers CSS additionnels dans assets/css/
 *   $bodyClass       : string - classe CSS optionnelle sur <body>
 *
 * i18n (site entier) — la langue est résolue et servie globalement ; une vue
 * qui participe au dictionnaire pose :
 *   $i18nPage        : string - identifiant de page pour le dictionnaire
 *                      (accueil|login|register|mdp|partenaire|parametres|menu|
 *                       menu_semaine|panier|mes_commandes|profil), posé sur <body>.
 */

$pageTitle       = $pageTitle       ?? APP_NAME;
$metaDescription = $metaDescription ?? 'Repas faits maison, livrés chez vous avec ' . APP_NAME . '.';
$metaKeywords    = $metaKeywords    ?? '';
$extraCss        = $extraCss        ?? [];
$bodyClass       = $bodyClass       ?? '';
$i18nActive      = $i18nActive      ?? false;
$i18nPage        = $i18nPage        ?? '';

// Langue active servie côté serveur (évite tout « flash » de langue sur
// l'ensemble du site). Les pages qui ne posent pas de $i18nPage restent en
// français tant que leur contenu n'est pas couvert par le dictionnaire.
require_once ROOT_PATH . '/assets/inc/langue.php';
$langueHtml = langue_actuelle();
$dirHtml    = $langueHtml === 'ar' ? 'rtl' : 'ltr';

// Empêche le navigateur de servir une page authentifiée depuis son cache
// (bouton "précédent", bfcache) après une déconnexion : sans ça, revenir
// en arrière peut réafficher un dashboard déjà rendu sans repasser par PHP,
// donc sans jamais réévaluer est_connecte().
if (est_connecte() && !headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}
?>
<!DOCTYPE html>
<html lang="<?php echo $langueHtml; ?>" dir="<?php echo $dirHtml; ?>">
<head>
    <script>
        (function () {
            var t = null;
            try { t = localStorage.getItem('fiajou3-theme'); } catch (e) { /* ignore */ }
            if (t !== 'dark') { t = 'light'; }
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    <script>
        window.FJ_I18N = {
            lang: '<?php echo $langueHtml; ?>',
            connecte: <?php echo est_connecte() ? 'true' : 'false'; ?>,
            url: '<?php echo BASE_URL; ?>/index.php?route=langue'
        };
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title id="pageTitle"><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <?php if (!empty($metaKeywords)): ?>
    <meta name="keywords" content="<?php echo htmlspecialchars($metaKeywords); ?>">
    <?php endif; ?>

    <link rel="icon" type="image/svg+xml" href="<?php echo BASE_URL; ?>/assets/images/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo BASE_URL; ?>/assets/images/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo BASE_URL; ?>/assets/images/favicon-16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo BASE_URL; ?>/assets/images/favicon-180.png">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/assets/images/favicon.ico">

    <link id="bootstrapCss" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/feane/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Open+Sans:wght@400;600;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/app.css?v=<?php echo (int) @filemtime(ROOT_PATH . '/assets/css/app.css'); ?>">
    <?php foreach ($extraCss as $css): ?>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/<?php echo htmlspecialchars($css); ?>?v=<?php echo (int) @filemtime(ROOT_PATH . '/assets/css/' . $css); ?>">
    <?php endforeach; ?>
</head>
<body class="<?php echo htmlspecialchars($bodyClass); ?>"<?php if ($i18nPage !== ''): ?> data-fj-page="<?php echo htmlspecialchars($i18nPage); ?>"<?php endif; ?>>
<?php require ROOT_PATH . '/assets/inc/mini_panier.php'; ?>
