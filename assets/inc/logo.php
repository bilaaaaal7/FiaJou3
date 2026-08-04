<?php
/**
 * Marque FiaJou3 (logo officiel) réutilisable.
 *
 * Fournit le vrai logo (tajine + vapeur + horloge + calligraphie "فياجوع" +
 * "FIAJOU3", conforme à la Charte Graphique officielle), en 2 variantes :
 *   - logo.png       (texte Noir Charbon) -> pour fond clair ;
 *   - logo-light.png (texte Blanc Pur)    -> pour fond sombre.
 * L'accent Or Tajine (#B88618) est identique dans les deux variantes.
 *
 * Les DEUX variantes sont émises dans la page ; la bonne est affichée
 * automatiquement selon le thème actif (attribut data-theme sur <html>),
 * piloté par assets/css/app.css (ou le bloc <style> des pages publiques) :
 *   .logo-theme-dark { display: none; }
 *   [data-theme="dark"] .logo-theme-light { display: none; }
 *   [data-theme="dark"] .logo-theme-dark  { display: block; }
 * => le changement de thème bascule le logo sans aucun re-rendu ni JS.
 *
 * Usage (inchangé pour les appelants existants) :
 *   <span class="logo-mark" style="width:36px;height:36px;">
 *       <?php include ROOT_PATH . '/assets/inc/logo.php'; ?>
 *   </span>
 */

$altLogo = $logoAlt ?? 'FiaJou3';
$logoAlt = null;
?>
<img class="logo-theme-light" src="<?php echo BASE_URL; ?>/assets/images/logo.png"
     alt="<?php echo htmlspecialchars($altLogo); ?>"
     width="100%" height="100%">
<img class="logo-theme-dark" src="<?php echo BASE_URL; ?>/assets/images/logo-light.png"
     alt="<?php echo htmlspecialchars($altLogo); ?>"
     width="100%" height="100%">
