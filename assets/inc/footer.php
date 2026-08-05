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
    <?php foreach ($extraJs as $js): ?>
    <script src="<?php echo BASE_URL; ?>/assets/js/<?php echo htmlspecialchars($js); ?>"></script>
    <?php endforeach; ?>
    <script>
        if (window.lucide) { lucide.createIcons(); }
    </script>
    <script>
        document.addEventListener('click', function (e) {
            var el = e.target.closest('[data-confirm]');
            if (!el) { return; }
            var message = el.getAttribute('data-confirm');
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
</body>
</html>
