/**
 * Comportement du menu déroulant "profil" : ouverture/fermeture au clic,
 * fermeture au clic extérieur ou touche Échap. Vanilla JS volontairement
 * (aucune dépendance à une version précise de Bootstrap), car ce composant
 * est réutilisé sur des pages qui chargent Bootstrap 4 (landing "feane")
 * ou Bootstrap 5 (espace connecté).
 */
(function () {
    function initProfileMenus() {
        document.querySelectorAll('[data-profile-menu]').forEach(function (menu) {
            if (menu.dataset.profileMenuBound) return;
            menu.dataset.profileMenuBound = '1';

            var trigger = menu.querySelector('[data-profile-trigger]');
            if (!trigger) return;

            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                var isOpen = menu.classList.contains('is-open');
                document.querySelectorAll('[data-profile-menu].is-open').forEach(function (m) {
                    m.classList.remove('is-open');
                    var t = m.querySelector('[data-profile-trigger]');
                    if (t) t.setAttribute('aria-expanded', 'false');
                });
                if (!isOpen) {
                    menu.classList.add('is-open');
                    trigger.setAttribute('aria-expanded', 'true');
                }
            });
        });

        document.addEventListener('click', function () {
            document.querySelectorAll('[data-profile-menu].is-open').forEach(function (m) {
                m.classList.remove('is-open');
                var t = m.querySelector('[data-profile-trigger]');
                if (t) t.setAttribute('aria-expanded', 'false');
            });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('[data-profile-menu].is-open').forEach(function (m) {
                    m.classList.remove('is-open');
                });
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initProfileMenus);
    } else {
        initProfileMenus();
    }
})();
