<?php
/**
 * Lien « Retour à l'accueil » (flèche + texte), réutilisé dans toutes les
 * pages de l'espace client. Style : assets/css/admin.css (.back-home).
 * À placer en haut de la page, au-dessus du titre.
 */
?>
<a class="back-home" href="<?php echo BASE_URL; ?>/index.php?route=accueil">
    <i data-lucide="arrow-left" aria-hidden="true"></i>
    <span data-i18n="common.retourAccueil">Retour à l'accueil</span>
</a>
