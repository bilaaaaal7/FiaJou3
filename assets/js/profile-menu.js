/* profile-menu.js — Toggle user dropdown via event delegation.
   No DOMContentLoaded dependency: uses document-level delegation so it
   works regardless of when the script or DOM elements are ready. */
(function () {
    function closeAll() {
        document.querySelectorAll('.user-menu.open').forEach(function (m) {
            m.classList.remove('open');
        });
    }

    document.addEventListener('click', function (e) {
        var trigger = e.target.closest('.user-menu-trigger');
        if (trigger) {
            e.stopPropagation();
            var menu = trigger.closest('.user-menu');
            if (!menu) return;
            var wasOpen = menu.classList.contains('open');
            closeAll();
            if (!wasOpen) {
                menu.classList.add('open');
            }
            return;
        }
        if (!e.target.closest('.user-menu')) {
            closeAll();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAll();
        }
    });
})();
