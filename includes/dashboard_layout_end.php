    </main>
</div>

<script>
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });
    }

    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!confirm(form.getAttribute('data-confirm'))) {
                e.preventDefault();
            }
        });
    });

    document.querySelectorAll('a[data-confirm]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            if (!confirm(link.getAttribute('data-confirm'))) {
                e.preventDefault();
            }
        });
    });
</script>

</body>
</html>
