<?php
/**
 * Marque FiaJou3 (logo officiel) réutilisable.
 *
 * Fournit le vrai logo (tajine + vapeur + horloge + calligraphie "فياجوع" +
 * "FIAJOU3", conforme à la Charte Graphique officielle), en 2 variantes pour
 * rester lisible quel que soit le fond sur lequel il est posé :
 *   - assets/images/logo.png       : texte en Noir Charbon -> à utiliser sur
 *                                     fond clair (Blanc Pur / Crème).
 *   - assets/images/logo-light.png : texte en Blanc Pur -> à utiliser sur
 *                                     fond sombre (Noir Charbon #171717).
 * L'accent Or Tajine (#B88618) est identique dans les deux variantes.
 *
 * Usage (inchangé pour les appelants existants) :
 *   <span class="logo-mark" style="width:36px;height:36px;">
 *       <?php include ROOT_PATH . '/assets/inc/logo.php'; ?>
 *   </span>
 *
 * Sur un fond sombre, définir $logoSurFondSombre = true; juste avant
 * l'include pour charger automatiquement la variante claire :
 *   <?php $logoSurFondSombre = true; include ROOT_PATH . '/assets/inc/logo.php'; ?>
 */

$fichierLogo = (!empty($logoSurFondSombre)) ? 'logo-light.png' : 'logo.png';
$altLogo      = $logoAlt ?? 'FiaJou3';

// Réinitialisation pour ne pas affecter les inclusions suivantes de logo.php
// dans la même page (chaque appel doit déclarer explicitement son contexte).
$logoSurFondSombre = false;
$logoAlt = null;
?>
<img src="<?php echo BASE_URL; ?>/assets/images/<?php echo $fichierLogo; ?>"
     alt="<?php echo htmlspecialchars($altLogo); ?>"
     style="display:block;width:100%;height:100%;object-fit:contain;">
