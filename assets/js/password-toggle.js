(function () {
    'use strict';

    function initPasswordToggles() {
        document.querySelectorAll('[data-password-toggle]').forEach(function (wrapper) {
            var input = wrapper.querySelector('input');
            var btn = wrapper.querySelector('.password-toggle-btn');
            if (!input || !btn) return;

            btn.addEventListener('click', function () {
                var isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';

                var icon = btn.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-eye', isHidden);
                    icon.classList.toggle('fa-eye-slash', !isHidden);
                }

                if (window.fjI18n) {
                    btn.setAttribute('aria-label',
                        isHidden ? window.fjI18n('common.hidePassword') : window.fjI18n('common.showPassword')
                    );
                }
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPasswordToggles);
    } else {
        initPasswordToggles();
    }
})();
