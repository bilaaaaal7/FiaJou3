<?php
/**
 * Pied de page commun (fermeture du <body>/<html> + scripts additionnels).
 * Variable attendue (optionnelle) avant l'include :
 *   $extraJs : array - fichiers JS additionnels dans assets/js/
 */

$extraJs = $extraJs ?? [];
?>
<?php if (est_connecte()): ?>
    </div>
</div>
<?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/theme.js"></script>
    <?php if (!in_array('i18n.js', $extraJs, true)): ?>
    <script src="<?php echo BASE_URL; ?>/assets/js/i18n.js?<?php echo (int) @filemtime(ROOT_PATH . '/assets/js/i18n.js'); ?>"></script>
    <?php endif; ?>
    <?php foreach ($extraJs as $js): ?>
    <script src="<?php echo BASE_URL; ?>/assets/js/<?php echo htmlspecialchars($js); ?>?v=<?php echo (int) @filemtime(ROOT_PATH . '/assets/js/' . $js); ?>"></script>
    <?php endforeach; ?>
    <script>
        if (window.lucide) { lucide.createIcons(); }
    </script>
    <script>
        document.addEventListener('click', function (e) {
            var el = e.target.closest('[data-confirm]');
            if (!el) { return; }
            var message = el.getAttribute('data-confirm');
            var cle = el.getAttribute('data-confirm-i18n');
            if (cle && window.fjI18n) { message = window.fjI18n(cle); }
            if (message && !window.confirm(message)) {
                e.preventDefault();
            }
        });
    </script>
    <script>
        // Menu déroulant du profil (header supérieur)
        document.addEventListener('click', function (e) {
            var trigger = e.target.closest('[data-profile-trigger]');
            var openMenu = document.querySelector('.topheader-profile.open');

            if (trigger) {
                var menu = trigger.closest('[data-profile-menu]');
                var isOpen = menu.classList.contains('open');
                if (openMenu && openMenu !== menu) { openMenu.classList.remove('open'); }
                menu.classList.toggle('open', !isOpen);
                trigger.setAttribute('aria-expanded', String(!isOpen));
                return;
            }

            if (openMenu && !e.target.closest('[data-profile-menu]')) {
                openMenu.classList.remove('open');
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                var openMenu = document.querySelector('.topheader-profile.open');
                if (openMenu) { openMenu.classList.remove('open'); }
            }
        });
    </script>
    <script>
        // Tiroir latéral (mobile) : bascule via body.sidebar-open + voile
        function toggleSidebar(force) {
            var sidebar = document.getElementById('sidebar');
            var open = (typeof force === 'boolean') ? force : !document.body.classList.contains('sidebar-open');
            document.body.classList.toggle('sidebar-open', open);
            if (sidebar) { sidebar.classList.toggle('open', open); }
            var btn = document.querySelector('.menu-toggle');
            if (btn) { btn.setAttribute('aria-expanded', String(open)); }
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && document.body.classList.contains('sidebar-open')) {
                toggleSidebar(false);
            }
        });

        document.addEventListener('click', function (e) {
            if (!document.body.classList.contains('sidebar-open')) { return; }
            if (e.target.closest('#sidebar nav a') || e.target.closest('.dashboard-card')) {
                toggleSidebar(false);
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 900) { toggleSidebar(false); }
        });
    </script>
</body>
</html>
