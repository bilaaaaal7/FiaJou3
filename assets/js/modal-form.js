(function () {
    'use strict';

    function fermer(overlay) {
        if (!overlay) { return; }
        overlay.hidden = true;
        if (!document.querySelector('.modal-overlay:not([hidden])')) {
            document.body.classList.remove('modal-open');
        }
    }

    // Applique le mode ajout / modification à la modale : titre, bouton
    // de soumission (name + label) et champ caché "id" (vidé en ajout).
    function appliquerMode(overlay, mode) {
        var estEdit = (mode === 'edit');
        var titre = overlay.querySelector('.modal-head h3');
        if (titre) {
            var nouveauTitre = estEdit ? titre.getAttribute('data-title-edit') : titre.getAttribute('data-title-add');
            if (nouveauTitre) { titre.textContent = nouveauTitre; }
        }
        var bouton = overlay.querySelector('button[type="submit"]');
        if (bouton) {
            var nom = estEdit ? bouton.getAttribute('data-name-edit') : bouton.getAttribute('data-name-add');
            var label = estEdit ? bouton.getAttribute('data-label-edit') : bouton.getAttribute('data-label-add');
            if (nom) { bouton.name = nom; }
            if (label) { bouton.textContent = label; }
        }
        if (!estEdit) {
            var champId = overlay.querySelector('input[type="hidden"][name="id"]');
            if (champId) { champId.value = ''; }
        }
    }

    function ouvrir(overlay, mode, champs, erreur) {
        if (!overlay) { return; }
        var form = overlay.querySelector('form');
        if (form) { form.reset(); }
        var estEdit = (mode === 'edit');
        overlay.querySelectorAll('[data-only-add]').forEach(function (el) {
            el.style.display = estEdit ? 'none' : '';
            el.querySelectorAll('input, select, textarea').forEach(function (champ) {
                champ.disabled = estEdit;
                champ.required = !estEdit;
            });
        });
        if (champs) {
            Object.keys(champs).forEach(function (name) {
                var el = overlay.querySelector('[name="' + name + '"]');
                if (el) { el.value = champs[name]; }
            });
        }
        var erreurP = overlay.querySelector('.modal-error');
        if (erreurP) {
            erreurP.textContent = erreur || '';
            erreurP.hidden = !erreur;
        }
        appliquerMode(overlay, mode);
        overlay.hidden = false;
        document.body.classList.add('modal-open');
        var premier = overlay.querySelector('input:not([type="hidden"]), select, textarea');
        if (premier) { premier.focus(); }
    }

    window.ouvrirModalForm = function (overlayId, mode, champs, erreur) {
        var overlay = document.getElementById(overlayId);
        if (overlay) { ouvrir(overlay, mode || 'add', champs || null, erreur || ''); }
    };

    document.addEventListener('click', function (e) {
        var declencheur = e.target.closest('[data-modal-open]');
        if (declencheur) {
            e.preventDefault();
            var overlay = document.getElementById(declencheur.getAttribute('data-modal-open'));
            if (!overlay) { return; }
            var champs = null;
            var raw = declencheur.getAttribute('data-fields');
            if (raw) {
                try { champs = JSON.parse(raw); } catch (err) { champs = null; }
            }
            ouvrir(overlay, declencheur.getAttribute('data-mode') || 'add', champs, '');
            return;
        }
        var fermerBtn = e.target.closest('[data-modal-close]');
        if (fermerBtn) {
            e.preventDefault();
            fermer(fermerBtn.closest('.modal-overlay'));
            return;
        }
        if (e.target.classList && e.target.classList.contains('modal-overlay')) {
            fermer(e.target);
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') { return; }
        var ouvert = document.querySelector('.modal-overlay:not([hidden])');
        if (ouvert) { fermer(ouvert); }
    });
})();
