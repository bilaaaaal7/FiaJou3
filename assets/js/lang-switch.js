/*
  Logique du sélecteur de langue (FR / EN / AR) partagée entre les pages
  de connexion et d'inscription. Extrait des scripts inline originaux.

  Chaque page définit son propre dictionnaire `window.i18n` (les textes
  diffèrent entre connexion et inscription) puis inclut ce fichier.
*/

const BOOTSTRAP_LTR = "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css";
const BOOTSTRAP_RTL = "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css";

function setLang(lang) {
    if (!window.i18n || !window.i18n[lang]) return;

    document.querySelectorAll('.lang-switcher button').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.lang === lang);
    });

    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.getAttribute('data-i18n');
        if (window.i18n[lang][key]) {
            el.textContent = window.i18n[lang][key];
        }
    });

    const dir = lang === 'ar' ? 'rtl' : 'ltr';
    document.documentElement.lang = lang;
    document.documentElement.dir = dir;

    const bootstrapCss = document.getElementById('bootstrapCss');
    const targetHref = dir === 'rtl' ? BOOTSTRAP_RTL : BOOTSTRAP_LTR;
    if (bootstrapCss && bootstrapCss.getAttribute('href') !== targetHref) {
        bootstrapCss.setAttribute('href', targetHref);
    }

    if (window.i18n[lang].pageTitle) {
        document.getElementById('pageTitle').textContent = window.i18n[lang].pageTitle;
    }

    localStorage.setItem('fiajou3_lang', lang);
}

document.addEventListener('DOMContentLoaded', function () {
    const savedLang = localStorage.getItem('fiajou3_lang');
    if (savedLang && window.i18n && window.i18n[savedLang]) {
        setLang(savedLang);
    }
});
