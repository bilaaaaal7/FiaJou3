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
 */

$pageTitle       = $pageTitle       ?? APP_NAME;
$metaDescription = $metaDescription ?? 'Repas faits maison, livrés chez vous avec ' . APP_NAME . '.';
$metaKeywords    = $metaKeywords    ?? '';
$extraCss        = $extraCss        ?? [];
$bodyClass       = $bodyClass       ?? '';
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title id="pageTitle"><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <?php if (!empty($metaKeywords)): ?>
    <meta name="keywords" content="<?php echo htmlspecialchars($metaKeywords); ?>">
    <?php endif; ?>

    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo BASE_URL; ?>/assets/images/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo BASE_URL; ?>/assets/images/favicon-16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo BASE_URL; ?>/assets/images/favicon-180.png">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/assets/images/favicon.ico">

    <link id="bootstrapCss" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/app.css">
    <?php foreach ($extraCss as $css): ?>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/<?php echo htmlspecialchars($css); ?>">
    <?php endforeach; ?>
</head>
<body class="<?php echo htmlspecialchars($bodyClass); ?>">
