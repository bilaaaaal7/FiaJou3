/**
 * Honeypot anti-spam (protection formulaires).
 *
 * Injecte dans chaque <form> un champ invisible (mais présent dans le HTML,
 * donc détectable par un bot qui le remplirait) nommé "website". Un utilisateur
 * réel laisse ce champ vide ; un bot le remplit souvent automatiquement.
 *
 * À la soumission, si le champ est rempli, la soumission est bloquée AVANT
 * tout envoi réseau / appel API / fetch / soumission native : on l'empêche
 * dès la phase de capture, donc avant les gestionnaires "submit" existants
 * (qui tournent en phase bubble) et avant le submit du navigateur.
 *
 * Aucune modification du design, des champs existants, des validations ni du
 * comportement normal des formulaires : le champ est invisible via une
 * technique "visually-hidden" (position absolue hors écran), pas display:none.
 */
(function () {
    'use strict';

    if (window.__fjHoneypotInstalled) { return; }
    window.__fjHoneypotInstalled = true;

    var NOM_CHAMP = 'website';

    // Feuille de style injectée une seule fois : masque le champ à l'écran et
    // aux lecteurs d'écran, tout en le laissant présent dans le DOM/HTML.
    function ajouterCss() {
        if (document.getElementById('fj-honeypot-style')) { return; }
        var style = document.createElement('style');
        style.id = 'fj-honeypot-style';
        style.textContent =
            '.fj-honeypot{position:absolute!important;left:-9999px!important;' +
            'width:1px;height:1px;margin:0;padding:0;border:0;overflow:hidden;' +
            'clip:rect(0 0 0 0);clip-path:inset(50%);white-space:nowrap;' +
            'word-wrap:normal;opacity:0;}';
        (document.head || document.documentElement).appendChild(style);
    }

    // Injecte le poubelle (honeypot) dans un formulaire, s'il n'y est pas déjà.
    function ajouterAuForm(form) {
        if (!form || form.tagName !== 'FORM') { return; }
        if (form.querySelector('[data-fj-honeypot]')) { return; }

        var champ = document.createElement('input');
        champ.type = 'text';
        champ.name = NOM_CHAMP;
        champ.id = 'website-' + Math.random().toString(36).slice(2, 8);
        champ.className = 'fj-honeypot';
        champ.setAttribute('data-fj-honeypot', '1');
        champ.setAttribute('aria-hidden', 'true');
        champ.setAttribute('tabindex', '-1');
        champ.setAttribute('autocomplete', 'off');
        champ.value = '';
        form.appendChild(champ);
    }

    // Couvre aussi les formulaires ajoutés dynamiquement (contenu remplacé par
    // AJAX, modales, etc.).
    function balayer() {
        var i, forms = document.forms;
        for (i = 0; i < forms.length; i++) {
            ajouterAuForm(forms[i]);
        }
    }

    // Vrai si le formulaire soumis contient un honeypot rempli (spam/bot).
    function estSpam(form) {
        if (!form || !form.querySelector) { return false; }
        var champ = form.querySelector('[data-fj-honeypot]');
        return !!(champ && champ.value && champ.value.trim() !== '');
    }

    // Phase de CAPTURE : exécutée avant les gestionnaires "submit" existants
    // (phase bubble, ex. admin_form.js, menu-semaine-admin.js, commander, etc.)
    // et avant la soumission native. Si un honeypot est rempli, on bloque
    // immédiatement : aucun fetch / appel API / envoi backend ne part.
    document.addEventListener('submit', function (e) {
        if (estSpam(e.target)) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            if (typeof e.target.reset === 'function') { e.target.reset(); }
        }
    }, true);

    function initialiser() {
        ajouterCss();
        balayer();
        if (window.MutationObserver) {
            var observer = new MutationObserver(function () { balayer(); });
            var cible = document.body || document.documentElement;
            if (cible) {
                observer.observe(cible, { childList: true, subtree: true });
            }
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialiser);
    } else {
        initialiser();
    }
})();
