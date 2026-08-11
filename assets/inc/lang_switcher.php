<?php
/**
 * Sélecteur de langue réutilisable (partiel).
 *
 * Utilisation :
 *   - pages publiques (connexion, inscription, mot de passe oublié, dossier
 *     partenaire) : rendu flottant en haut à droite (styles .lang-switcher
 *     de auth.css) ;
 *   - page Paramètres : rendu en ligne dans une carte, poser
 *     $langSwitcherInline = true avant l'include ;
 *   - barre supérieure (topheader) : poser $langSwitcherCompact = true avant
 *     l'include (variante compacte : icône seule, sans effet sur le menu).
 *
 * Affichage :
 *   - variante par défaut « dropdown » : une icône globe dorée qui ouvre un
 *     petit menu déroulant avec les trois langues empilées verticalement
 *     (Français / English / العربية) ;
 *   - variante « inline » : cartes sélectionnables (page Paramètres).
 *
 * Le clic sur une langue est géré par assets/js/i18n.js (délégation sur
 * [data-lang]) ; la logique de traduction n'est pas modifiée ici. Le script
 * embarqué ne gère que l'ouverture/fermeture du menu (icône + clic extérieur).
 */

require_once ROOT_PATH . '/assets/inc/langue.php';

$langueActive = langue_actuelle();
$langSwitcherInline  = !empty($langSwitcherInline);
$langSwitcherCompact = !empty($langSwitcherCompact);
$classLangSwitcher = 'lang-switcher';
if ($langSwitcherInline) {
    $classLangSwitcher .= ' lang-switcher--inline';
} else {
    $classLangSwitcher .= ' lang-switcher--dropdown';
}
if ($langSwitcherCompact) {
    $classLangSwitcher .= ' lang-switcher--compact';
}

$languesAffichage = [
    'fr' => ['code' => 'FR', 'nom' => 'Français'],
    'en' => ['code' => 'EN', 'nom' => 'English'],
    'ar' => ['code' => 'ع', 'nom' => 'العربية'],
];
?>
<?php if ($langSwitcherInline): ?>
<div class="<?php echo $classLangSwitcher; ?>" role="group" data-i18n-aria="common.langueSelector" aria-label="Sélecteur de langue">
    <?php foreach ($languesAffichage as $codeLangue => $infoLangue): ?>
    <button type="button" class="<?php echo $langueActive === $codeLangue ? 'active' : ''; ?>" data-lang="<?php echo $codeLangue; ?>">
        <span class="lang-code" aria-hidden="true"><?php echo $infoLangue['code']; ?></span>
        <span class="lang-name"><?php echo $infoLangue['nom']; ?></span>
    </button>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="<?php echo $classLangSwitcher; ?>">
    <button type="button" class="lang-switcher-toggle" data-lang-toggle aria-haspopup="true" aria-expanded="false" data-i18n-aria="common.langueSelector" aria-label="Sélecteur de langue">
        <i data-lucide="globe" class="lang-switcher-icon" aria-hidden="true"></i>
    </button>
    <div class="lang-switcher-menu" data-lang-menu>
        <?php foreach ($languesAffichage as $codeLangue => $infoLangue): ?>
        <button type="button" class="lang-switcher-option<?php echo $langueActive === $codeLangue ? ' active' : ''; ?>" data-lang="<?php echo $codeLangue; ?>">
            <span class="lang-code" aria-hidden="true"><?php echo $infoLangue['code']; ?></span>
            <span class="lang-name"><?php echo $infoLangue['nom']; ?></span>
        </button>
        <?php endforeach; ?>
    </div>
</div>
<script>
(function () {
    if (window.__fjLangSwitcherMenus) { return; }
    window.__fjLangSwitcherMenus = true;
    function fermerTous() {
        var groupes = document.querySelectorAll('.lang-switcher--dropdown');
        for (var i = 0; i < groupes.length; i++) {
            groupes[i].classList.remove('is-open');
            var bouton = groupes[i].querySelector('[data-lang-toggle]');
            if (bouton) { bouton.setAttribute('aria-expanded', 'false'); }
        }
    }
    document.addEventListener('click', function (e) {
        var cible = e.target;
        var toggle = cible && cible.closest ? cible.closest('[data-lang-toggle]') : null;
        if (toggle) {
            var groupe = toggle.closest('.lang-switcher--dropdown');
            var rouvrir = groupe && !groupe.classList.contains('is-open');
            fermerTous();
            if (rouvrir && groupe) {
                groupe.classList.add('is-open');
                groupe.querySelector('[data-lang-toggle]').setAttribute('aria-expanded', 'true');
            }
            return;
        }
        if (cible && cible.closest && cible.closest('[data-lang-menu]')) {
            fermerTous();
            return;
        }
        fermerTous();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' || e.keyCode === 27) { fermerTous(); }
    });
})();
</script>
<?php endif; ?>
