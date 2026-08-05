(function () {
    'use strict';

    var root = document.getElementById('modalModifierItem');
    if (!root) { return; }

    var form = document.getElementById('formModifierItem');
    var errorBox = document.getElementById('mi-error');
    var product = document.getElementById('mi-product');
    var nom = document.getElementById('mi-nom');
    var category = document.getElementById('mi-category');
    var prix = document.getElementById('mi-prix');
    var description = document.getElementById('mi-description');

    function formatterPrix(v) {
        var n = parseFloat(v);
        if (isNaN(n)) { return ''; }
        return n.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' DH';
    }

    function remplirDepuisPlat() {
        var opt = product.options[product.selectedIndex];
        if (!opt) { return; }
        nom.value = opt.getAttribute('data-nom') || '';
        category.value = opt.getAttribute('data-category') || '';
        prix.value = opt.getAttribute('data-prix') || '';
        description.value = opt.getAttribute('data-description') || '';
    }

    product.addEventListener('change', remplirDepuisPlat);

    function openModal(btn) {
        form.reset();
        errorBox.hidden = true;
        document.getElementById('mi-item-id').value = btn.getAttribute('data-item-id') || '';
        document.getElementById('mi-menu-id').value = btn.getAttribute('data-menu-id') || '';
        product.value = btn.getAttribute('data-product-id') || '';
        nom.value = btn.getAttribute('data-nom') || '';
        category.value = btn.getAttribute('data-category-id') || '';
        prix.value = btn.getAttribute('data-prix') || '';
        description.value = btn.getAttribute('data-description') || '';
        root.hidden = false;
        document.body.classList.add('modal-open');
        product.focus();
    }

    function closeModal() {
        root.hidden = true;
        document.body.classList.remove('modal-open');
    }

    function appliquerItem(item) {
        var chip = document.getElementById('item-' + item.id);
        if (!chip) { return; }
        var nomTxt = item.plat_nom || '';
        var catTxt = item.categorie || '';
        var prixTxt = formatterPrix(item.prix);

        var nameEl = chip.querySelector('.dish-chip-name');
        if (nameEl) { nameEl.textContent = nomTxt; }

        var catEl = chip.querySelector('.chip-cat');
        if (catEl) { catEl.textContent = catTxt; }

        var prixEl = chip.querySelector('.chip-prix');
        if (prixEl) { prixEl.textContent = (catTxt ? ' · ' : '') + prixTxt; }

        var editBtn = chip.querySelector('[data-modal-edit]');
        if (editBtn) {
            editBtn.setAttribute('data-product-id', item.product_id);
            editBtn.setAttribute('data-nom', nomTxt);
            editBtn.setAttribute('data-category-id', item.categorie_id || 0);
            editBtn.setAttribute('data-prix', item.prix);
            editBtn.setAttribute('data-description', item.description || '');
            editBtn.title = 'Modifier ' + nomTxt;
            editBtn.setAttribute('aria-label', 'Modifier ' + nomTxt);
        }

        var retirer = chip.querySelector('[data-confirm]');
        if (retirer) {
            retirer.setAttribute('data-confirm', 'Retirer ' + nomTxt + ' du menu ?');
            retirer.title = 'Retirer ' + nomTxt;
            retirer.setAttribute('aria-label', 'Retirer ' + nomTxt);
        }
    }

    document.addEventListener('click', function (e) {
        var edit = e.target.closest('[data-modal-edit]');
        if (edit) {
            e.preventDefault();
            openModal(edit);
            return;
        }
        if (e.target.closest('[data-modal-close]') || e.target === root) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !root.hidden) { closeModal(); }
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var btn = form.querySelector('button[type="submit"]');
        var boutonOriginal = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Enregistrement…';
        errorBox.hidden = true;

        var baseUrl = (window.MENU_SEMAINE_ADMIN && window.MENU_SEMAINE_ADMIN.url) || '';
        fetch(baseUrl, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(form)
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res.ok && res.item) {
                appliquerItem(res.item);
                closeModal();
            } else {
                errorBox.textContent = res.message || 'Erreur lors de l\'enregistrement.';
                errorBox.hidden = false;
            }
        })
        .catch(function () {
            errorBox.textContent = 'Erreur réseau : la modification n\'a pas été enregistrée.';
            errorBox.hidden = false;
        })
        .then(function () {
            btn.disabled = false;
            btn.textContent = boutonOriginal;
        });
    });
})();
