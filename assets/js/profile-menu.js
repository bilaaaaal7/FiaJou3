(function () {
    function initUserMenus() {
        document.querySelectorAll('.user-menu').forEach(function (menu) {
            if (menu.dataset.bound) return;
            menu.dataset.bound = '1';

            var trigger = menu.querySelector('.user-menu-trigger');
            if (!trigger) return;

            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                var wasOpen = menu.classList.contains('open');
                document.querySelectorAll('.user-menu.open').forEach(function (m) {
                    m.classList.remove('open');
                });
                if (!wasOpen) {
                    menu.classList.add('open');
                }
            });
        });

        document.addEventListener('click', function () {
            document.querySelectorAll('.user-menu.open').forEach(function (m) {
                m.classList.remove('open');
            });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.user-menu.open').forEach(function (m) {
                    m.classList.remove('open');
                });
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initUserMenus);
    } else {
        initUserMenus();
    }
})();
