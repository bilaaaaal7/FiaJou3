/**
 * Panneau de gestion (ajout / modification) partagé par les pages admin
 * cuisiniers, livreurs et zones de livraison.
 *
 * Comportement commun aux trois pages :
 *  - au chargement, seule la liste (tableau) est visible, le formulaire est
 *    masqué ;
 *  - cliquer sur « Ajouter » masque la liste et affiche uniquement le
 *    formulaire de création ;
 *  - « Modifier » masque la liste et affiche le formulaire pré-rempli,
 *    sans recharger la page ;
 *  - « Annuler » ferme le formulaire, vide les champs et réaffiche la liste ;
 *  - après une création, une modification ou une suppression, le contenu est
 *    rafraîchi automatiquement (AJAX) sans navigation ni rechargement :
 *    le serveur est appelé sur les mêmes URL que les formulaires classiques
 *    (les requêtes existantes sont inchangées) et le DOM est remplacé par la
 *    réponse.
 *
 * L'affichage est piloté par un objet d'état unique (même logique que les
 * états React showForm / isEditing / selectedItem) :
 *   { showForm, isEditing, selectedItem }
 */

(function () {
    'use strict';

    function execInlineScripts(container) {
        var scripts = container.querySelectorAll('script:not([src])');
        Array.prototype.forEach.call(scripts, function (s) {
            var n = document.createElement('script');
            n.textContent = s.textContent;
            s.parentNode.replaceChild(n, s);
        });
    }

    function initFormPanel(cfg) {
        var content = document.getElementById(cfg.contentId);
        var listPanel = document.getElementById(cfg.listId);
        var addBtn = document.getElementById(cfg.toggleId);
        var collapse = document.getElementById(cfg.collapseId);
        var form = document.getElementById(cfg.formId);

        if (!content || !collapse || !form) { return; }

        var submitBtn = form.querySelector('[data-btn-submit]');
        var annulerBtn = form.querySelector('[data-btn-annuler]');
        var titleAdd = collapse.querySelector('[data-title-add]');
        var titleEdit = collapse.querySelector('[data-title-edit]');
        var passField = form.querySelector('[data-field-password]');
        var passInput = passField ? passField.querySelector('input') : null;
        var idInput = form.querySelector('[name="id"]');

        var state = {
            showForm: collapse.classList.contains('open'),
            isEditing: cfg.initialMode === 'edit',
            selectedItem: null
        };

        function setEditUi(isEdit) {
            if (titleAdd) { titleAdd.hidden = isEdit; }
            if (titleEdit) { titleEdit.hidden = !isEdit; }
            if (submitBtn) {
                submitBtn.name = isEdit ? 'modifier' : 'ajouter';
                submitBtn.textContent = isEdit ? cfg.labels.editSubmit : cfg.labels.addSubmit;
            }
            if (passField) { passField.hidden = isEdit; }
            if (passInput) {
                if (isEdit) {
                    passInput.removeAttribute('required');
                    passInput.value = '';
                } else {
                    passInput.setAttribute('required', '');
                }
            }
        }

        function clearForm() {
            var elements = form.elements;
            for (var i = 0; i < elements.length; i++) {
                var el = elements[i];
                if (el.type === 'hidden') { continue; }
                if (el.type === 'checkbox' || el.type === 'radio') { el.checked = false; continue; }
                if (el.tagName === 'INPUT' || el.tagName === 'SELECT' || el.tagName === 'TEXTAREA') {
                    el.value = '';
                }
            }
            if (idInput) { idInput.value = ''; }
        }

        function focusFirst() {
            var first = form.querySelector('input:not([type="hidden"]):not([type="password"]):not([type="submit"])');
            if (first) { first.focus(); }
        }

        function render() {
            if (state.showForm) {
                if (listPanel) { listPanel.classList.add('is-hidden'); }
                collapse.classList.add('open');
            } else {
                if (listPanel) { listPanel.classList.remove('is-hidden'); }
                collapse.classList.remove('open');
            }
            setEditUi(state.isEditing);
            if (addBtn) { addBtn.setAttribute('aria-expanded', state.showForm ? 'true' : 'false'); }
        }

        function replaceContent(html) {
            var doc = new DOMParser().parseFromString(html, 'text/html');
            var fresh = doc.getElementById(cfg.contentId);
            var current = document.getElementById(cfg.contentId);
            if (!fresh || !current) { return; }
            current.replaceWith(fresh);
            execInlineScripts(fresh);
            if (window.lucide) {
                try { window.lucide.createIcons(); } catch (e) { }
            }
        }

        function openAdd() {
            state.showForm = true;
            state.isEditing = false;
            state.selectedItem = null;
            clearForm();
            render();
            focusFirst();
        }

        function cancelForm() {
            state.showForm = false;
            state.isEditing = false;
            state.selectedItem = null;
            clearForm();
            render();
        }

        function openEdit(item, id) {
            state.showForm = true;
            state.isEditing = true;
            state.selectedItem = item;
            cfg.populate(form, item);
            if (idInput) { idInput.value = id; }
            render();
            focusFirst();
        }

        function submitForm(e) {
            e.preventDefault();
            if (submitBtn) { submitBtn.disabled = true; }
            var fd = new FormData(form);
            if (submitBtn && submitBtn.name) { fd.append(submitBtn.name, '1'); }
            fetch(form.getAttribute('action'), {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd
            })
            .then(function (r) { return r.text(); })
            .then(replaceContent)
            .catch(function () {
                if (submitBtn) { submitBtn.disabled = false; }
            });
        }

        function runAction(e) {
            var link = e.target.closest(cfg.ajaxSelector);
            if (!link) { return; }
            e.preventDefault();
            e.stopPropagation();
            var msg = link.getAttribute('data-confirm');
            if (msg && !window.confirm(msg)) { return; }
            link.classList.add('disabled');
            fetch(link.getAttribute('href'), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (r) { return r.text(); })
            .then(replaceContent)
            .catch(function () { link.classList.remove('disabled'); });
        }

        if (addBtn) { addBtn.addEventListener('click', openAdd); }

        if (annulerBtn) {
            annulerBtn.addEventListener('click', function (e) {
                e.preventDefault();
                cancelForm();
            });
        }

        form.addEventListener('submit', submitForm);

        if (cfg.items && cfg.populate && content) {
            var links = content.querySelectorAll(cfg.editSelector);
            Array.prototype.forEach.call(links, function (link) {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    var item = cfg.items[link.getAttribute('data-id')];
                    if (item) { openEdit(item, link.getAttribute('data-id')); }
                });
            });
        }

        if (cfg.ajaxSelector && content) {
            content.addEventListener('click', runAction);
        }

        render();
    }

    window.fjInitFormPanel = initFormPanel;
})();
