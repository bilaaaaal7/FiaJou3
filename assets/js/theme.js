/**
 * Système de thème Light/Dark de FiaJou3.
 *
 * - Persistance dans localStorage (clé « fiajou3-theme »).
 * - Applique l'attribut data-theme="light|dark" sur <html>.
 *   (La valeur initiale est posée dès le <head> par un petit script
 *   inline dans header.php/accueil.php pour éviter tout "flash".)
 * - Met à jour l'état aria-pressed des boutons [data-theme-toggle].
 * - La visibilité des icônes soleil/lune et des variantes du logo est
 *   gérée en pur CSS via [data-theme] (aucun flash ni re-rendu).
 */
(function () {
    'use strict';

    var KEY = 'fiajou3-theme';

    function lireTheme() {
        var t = null;
        try {
            t = localStorage.getItem(KEY);
        } catch (e) { /* localStorage indisponible */ }
        return t === 'dark' ? 'dark' : 'light';
    }

    function appliquer(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        var boutons = document.querySelectorAll('[data-theme-toggle]');
        for (var i = 0; i < boutons.length; i++) {
            boutons[i].setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
        }
    }

    appliquer(lireTheme());

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-theme-toggle]');
        if (!btn) { return; }
        var prochain = lireTheme() === 'dark' ? 'light' : 'dark';
        try {
            localStorage.setItem(KEY, prochain);
        } catch (err) { /* localStorage indisponible */ }
        appliquer(prochain);
    });

    window.fiajou3Theme = {
        lire: lireTheme,
        appliquer: appliquer
    };
})();
