<?php
/**
 * Page d'accueil publique de FiaJou3
 * Design repris du template "Feane" (thème sombre / doré), adapté au
 * contexte de FiaJou3 : repas faits maison, livrés chez vous.
 *
 * Variables reçues du contrôleur (AccueilControleur.php) :
 *   $menu          : array|false - menu de la semaine publié
 *   $itemsParJour  : array       - plats groupés par jour ('lundi' => [...], ...)
 */

$pageTitle = APP_NAME . " - Repas faits maison, livrés chez vous";

// Participation au système i18n (FR/EN/AR) : langue résolue côté serveur pour
// éviter tout « flash », data-fj-page pour le titre de l'onglet par page.
require_once ROOT_PATH . '/assets/inc/langue.php';
$i18nPage    = 'accueil';
$langueHtml  = langue_actuelle();
$dirHtml     = $langueHtml === 'ar' ? 'rtl' : 'ltr';

// Client connecté : la Home reste accessible et affiche Panier + Profil.
$estClientConnecte = est_connecte() && utilisateur_role() === ROLE_CLIENT;
$panierNb = (int) ($panierNb ?? 0);

// Cible des boutons « Commander » / « Commencer à commander » : l'espace de
// commande pour un client déjà connecté, sinon la page de connexion (avec un
// retour vers cet espace après authentification — jamais l'inscription).
$lienCommander = $estClientConnecte
    ? BASE_URL . '/index.php?route=client'
    : url_connexion_avec_retour('client');

$jourLabels = [
    'lundi'     => 'Lundi',
    'mardi'     => 'Mardi',
    'mercredi'  => 'Mercredi',
    'jeudi'     => 'Jeudi',
    'vendredi'  => 'Vendredi',
    'dimanche'  => 'Dimanche',
];

// Quelques photos maison pour illustrer le hero et les mises en avant
$photoTajine   = UPLOADS_URL . '/acceuil.png';
$photoCouscous = UPLOADS_URL . '/fait_maison.png';
$photoViande   = UPLOADS_URL . '/livraison_rapide.png';

$hasMenu = $menu && !empty($itemsParJour) && array_sum(array_map('count', $itemsParJour)) > 0;
?>
<!DOCTYPE html>
<html lang="<?php echo $langueHtml; ?>" dir="<?php echo $dirHtml; ?>">
<head>
    <script>
        (function () {
            var t = null;
            try { t = localStorage.getItem('fiajou3-theme'); } catch (e) { /* ignore */ }
            if (t !== 'dark') { t = 'light'; }
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    <script>
        window.FJ_I18N = {
            lang: '<?php echo $langueHtml; ?>',
            connecte: <?php echo est_connecte() ? 'true' : 'false'; ?>,
            url: '<?php echo BASE_URL; ?>/index.php?route=langue'
        };
    </script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Commandez des repas faits maison préparés par des cuisiniers locaux et faites-vous livrer rapidement avec <?php echo APP_NAME; ?>.">
    <meta name="keywords" content="repas maison, livraison de repas, cuisine locale, commande de repas, <?php echo APP_NAME; ?>">
    <title id="pageTitle"><?php echo htmlspecialchars($pageTitle); ?></title>

    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo BASE_URL; ?>/assets/images/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo BASE_URL; ?>/assets/images/favicon-16.png">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/assets/images/favicon.ico">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>/assets/feane/css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
    <link href="<?php echo BASE_URL; ?>/assets/feane/css/font-awesome.min.css" rel="stylesheet" />
    <link href="<?php echo BASE_URL; ?>/assets/feane/css/style.css" rel="stylesheet" />
    <link href="<?php echo BASE_URL; ?>/assets/feane/css/responsive.css" rel="stylesheet" />
    <link href="<?php echo BASE_URL; ?>/assets/css/profile-menu.css" rel="stylesheet" />

    <style>
        /* =====================================================================
           FiaJou3 — Accueil : habillage premium de la page d'accueil.
           Tous les sélecteurs restent confinés à cette page (le bloc <style>
           n'existe que sur vue/accueil.php) : aucune autre page n'est affectée.
           Charte graphique : Noir Charbon #171717, Or Tajine #B88618, Blanc.
           ===================================================================== */

        :root {
            --fj-noir: #171717;
            --fj-noir-2: #1b1b21;
            --fj-or: #B88618;
            --fj-or-clair: #e0b14d;
            --fj-or-sombre: #976e14;
            --fj-blanc: #ffffff;
            --fj-ombre: 0 14px 30px rgba(23, 23, 23, 0.10);
            --fj-ombre-or: 0 10px 24px rgba(184, 134, 24, 0.30);

            /* Jetons sémantiques du sélecteur de langue (identiques à
               assets/css/app.css, source unique du site). */
            --gold: #B88618;
            --gold-hover: #976e14;
            --gold-light: #f4ecd8;
            --gold-dark: #7a5810;
            --surface: #ffffff;
            --border-soft: #e4dfd3;
            --muted: #706B62;
            --text: #171717;
            --on-gold: #171717;
            --font-body: 'Open Sans', Arial, sans-serif;
        }

        /* Jetons sémantiques du sélecteur de langue — mode sombre. */
        [data-theme="dark"] {
            --gold: #B88618;
            --gold-hover: #a67a1a;
            --gold-light: #3a3120;
            --gold-dark: #e0b14d;
            --surface: #18181e;
            --border-soft: #2b2b32;
            --muted: #a49d92;
            --text: #f2efe8;
            --on-gold: #171717;
        }

        /* ---------- Base ---------- */
        html { scroll-behavior: smooth; scroll-padding-top: 96px; }
        body { -webkit-font-smoothing: antialiased; text-rendering: optimizeLegibility; }
        ::selection { background: rgba(184, 134, 24, 0.28); }

        #menu, #about, #partenaire { scroll-margin-top: 96px; }

        /* ---------- Animations d'apparition au scroll ---------- */
        .fj-reveal { opacity: 1; transform: none; }
        html.fj-js .fj-reveal {
            opacity: 0;
            transform: translateY(26px);
            transition: opacity .7s ease, transform .7s cubic-bezier(.22, .61, .36, 1);
            will-change: opacity, transform;
        }
        html.fj-js .fj-reveal.is-visible { opacity: 1; transform: none; }
        .fj-reveal-delay-1 { transition-delay: .12s; }
        .fj-reveal-delay-2 { transition-delay: .24s; }
        .fj-reveal-delay-3 { transition-delay: .36s; }

        /* ---------- Header fixe (reste visible au scroll) ---------- */
        .header_section {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 999;
            padding: 10px 0;
            background: rgba(23, 23, 23, 0.95);
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
            transition: background-color .3s ease, box-shadow .3s ease, padding .3s ease, border-color .3s ease;
        }
        .header_section.is-scrolled {
            background: rgba(23, 23, 23, 0.97);
            padding: 7px 0;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.35);
        }
        .header_section .container { max-width: 1320px; }

        /* ---------- Barre de navigation : alignement + espacements uniformes ---------- */
        .header_section .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            min-height: 68px;
            padding: 0;
        }
        .custom_nav-container .navbar-nav {
            display: flex;
            align-items: center;
            gap: 6px;
            padding-left: 0;
            margin: 0;
        }

        .navbar-brand {
            display: inline-flex;
            align-items: center;
            gap: 0;
            padding: 0;
            margin: 0;
            flex-shrink: 0;
        }
        .logo-mark { display: inline-flex; align-items: center; justify-content: center; }
        .logo-mark svg { display: block; width: 100%; height: 100%; }
        .logo-mark img { display: block; width: 100%; height: 100%; object-fit: contain; }
        .navbar-brand .logo-mark { width: 60px; height: 60px; flex-shrink: 0; }

        .custom_nav-container .navbar-nav .nav-item .nav-link {
            position: relative;
            display: inline-flex;
            align-items: center;
            padding: 10px 18px;
            margin: 0 2px;
            color: rgba(255, 255, 255, 0.85);
            text-transform: uppercase;
            font-weight: 600;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            border-radius: 30px;
            transition: color .3s ease, background-color .3s ease, transform .3s ease;
        }
        .custom_nav-container .navbar-nav .nav-item .nav-link::after {
            content: "";
            position: absolute;
            left: 18px;
            right: 18px;
            bottom: 5px;
            height: 2px;
            border-radius: 2px;
            background: var(--fj-or);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform .3s ease;
        }
        .custom_nav-container .navbar-nav .nav-item:hover .nav-link,
        .custom_nav-container .navbar-nav .nav-item.active .nav-link { color: #ffffff; }
        .custom_nav-container .navbar-nav .nav-item.active .nav-link {
            background: rgba(184, 134, 24, 0.16);
        }
        .custom_nav-container .navbar-nav .nav-item:hover .nav-link::after,
        .custom_nav-container .navbar-nav .nav-item.active .nav-link::after { transform: scaleX(1); }

        .user_option {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            flex-shrink: 0;
        }

        /* ---------- Logo : le header et le hero sont sombres dans les DEUX thèmes ;
           la variante claire (texte Noir Charbon) y serait illisible,
           on force donc la variante blanche (le template peut forcer display
           sur l'image, d'où !important pour une bascule fiable) ---------- */
        .logo-theme-light { display: none !important; }
        .logo-theme-dark { display: block !important; }

        /* ---------- Bouton de basculement de thème (header) ---------- */
        .header_section .theme-toggle {
            width: 42px;
            height: 42px;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.30);
            color: #f5f5f5;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background-color .3s ease, border-color .3s ease, color .3s ease, transform .3s ease, box-shadow .3s ease;
        }
        .header_section .theme-toggle svg { width: 18px; height: 18px; }
        .header_section .theme-toggle [data-theme-icon="moon"] { display: none; }
        [data-theme="dark"] .header_section .theme-toggle [data-theme-icon="sun"] { display: none; }
        [data-theme="dark"] .header_section .theme-toggle [data-theme-icon="moon"] { display: inline-flex; }
        .header_section .theme-toggle:hover {
            background: rgba(184, 134, 24, 0.22);
            border-color: var(--fj-or);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(184, 134, 24, 0.25);
        }

        /* ---------- Sélecteur de langue (header) : icône globe + menu ---------- */
        /* Règles identiques à celles d'assets/css/app.css (page Admin) :
           icône globe dorée seule dans le header, menu déroulant masqué
           par défaut et ouvert au clic. */
        .header_section .lang-switcher--dropdown {
            position: relative;
            display: inline-flex;
            align-items: center;
            background: none;
            border: none;
            border-radius: 0;
            padding: 0;
            box-shadow: none;
            margin-inline-end: 8px;
        }
        .header_section .lang-switcher--dropdown .lang-switcher-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            padding: 6px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.30);
            background: rgba(255, 255, 255, 0.07);
            color: #e0b14d;
            box-shadow: none;
            cursor: pointer;
            transition: background-color .2s ease-in-out, color .2s ease-in-out, box-shadow .2s ease-in-out;
        }
        .header_section .lang-switcher--dropdown .lang-switcher-toggle svg {
            width: 20px;
            height: 20px;
        }
        .header_section .lang-switcher--dropdown .lang-switcher-toggle:hover,
        .header_section .lang-switcher--dropdown .lang-switcher-toggle:focus-visible {
            background: rgba(184, 134, 24, 0.22);
            border-color: var(--fj-or);
            color: #f0ca6d;
            box-shadow: 0 6px 16px rgba(184, 134, 24, 0.25);
        }
        .header_section .lang-switcher--dropdown .lang-switcher-menu {
            position: absolute;
            top: calc(100% + 8px);
            inset-inline-end: 0;
            z-index: 1200;
            min-width: 180px;
            padding: 6px;
            background: var(--surface);
            border: 1px solid var(--border-soft);
            border-radius: 14px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.18);
            display: flex;
            flex-direction: column;
            gap: 2px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-4px);
            transition: opacity .18s ease-in-out, transform .18s ease-in-out, visibility .18s ease-in-out;
        }
        .header_section .lang-switcher--dropdown.is-open .lang-switcher-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .header_section .lang-switcher--dropdown .lang-switcher-option {
            display: flex;
            align-items: center;
            gap: 10px;
            border: none;
            background: transparent;
            color: var(--muted);
            font-family: var(--font-body);
            font-weight: 600;
            font-size: 0.85rem;
            padding: 8px 10px;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
            text-align: start;
            transition: background-color .15s ease-in-out, color .15s ease-in-out;
        }
        .header_section .lang-switcher--dropdown .lang-switcher-option .lang-code {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 24px;
            height: 24px;
            padding: 0 5px;
            border-radius: 999px;
            background: var(--border-soft);
            color: var(--muted);
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            transition: background-color .15s ease-in-out, color .15s ease-in-out;
        }
        .header_section .lang-switcher--dropdown .lang-switcher-option:hover {
            background: color-mix(in srgb, var(--gold) 12%, transparent);
            color: var(--text);
        }
        .header_section .lang-switcher--dropdown .lang-switcher-option:hover .lang-code {
            background: color-mix(in srgb, var(--gold) 18%, var(--border-soft));
            color: var(--gold-dark);
        }
        .header_section .lang-switcher--dropdown .lang-switcher-option.active {
            color: var(--gold-dark);
            background: var(--gold-light);
        }
        .header_section .lang-switcher--dropdown .lang-switcher-option.active .lang-code {
            background: var(--gold);
            color: var(--on-gold);
        }
        .header_section .lang-switcher--dropdown .lang-switcher-option.active::after {
            content: '✓';
            margin-inline-start: auto;
            font-weight: 800;
            color: var(--gold-dark);
        }
        html[dir="rtl"] .header_section .lang-switcher--dropdown .lang-switcher-option {
            font-family: 'Tajawal', var(--font-body);
        }

        /* ---------- Bouton "Commander" (header) ---------- */
        .user_option .order_online {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 11px 28px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
            color: #ffffff;
            background: linear-gradient(135deg, #c8931f, var(--fj-or) 55%, var(--fj-or-sombre));
            box-shadow: 0 6px 16px rgba(184, 134, 24, 0.35);
            transition: transform .25s ease, box-shadow .25s ease, background .25s ease, color .25s ease;
        }
        .user_option .order_online:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #d8a52b, #c8931f 55%, #a37209);
            box-shadow: 0 10px 22px rgba(184, 134, 24, 0.45);
            color: #ffffff;
        }
        .user_option .order_online:active { transform: translateY(0); }

        /* ---------- Hero sans photo (charte : Noir Charbon + Or Tajine) ---------- */
        .hero_area--branded {
            background: #171717;
            overflow: hidden;
            min-height: 100vh;
            min-height: 100svh;
        }

        .hero_pattern {
            position: absolute; inset: 0; opacity: 0.06; pointer-events: none;
        }

        .hero_glow {
            position: absolute; inset: 0; pointer-events: none;
            background:
                radial-gradient(circle at 78% 30%, #c38d194d 0%, rgba(184,134,24,0) 55%),
                radial-gradient(circle at 12% 90%, rgba(184,134,24,0.10) 0%, rgba(184,134,24,0) 45%);
        }

        .slider_section--static { padding: 60px 0 90px; }
        .slider_section--static .detail-box { margin-bottom: 0; }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--fj-or-clair);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.6px;
            font-size: 0.82rem;
            margin-bottom: 18px;
        }
        .hero-eyebrow::before,
        .hero-eyebrow::after {
            content: "";
            height: 1px;
            width: 26px;
            background: linear-gradient(90deg, transparent, rgba(184, 134, 24, 0.7));
        }
        .hero-eyebrow::after {
            background: linear-gradient(90deg, rgba(184, 134, 24, 0.7), transparent);
        }

        .slider_section .detail-box h1 {
            font-size: 3.2rem;
            font-weight: 800;
            line-height: 1.12;
            margin-bottom: 18px;
            letter-spacing: -0.5px;
            max-width: 620px;
            color: #ffffff;
        }
        .slider_section .detail-box p {
            font-size: 1.05rem;
            line-height: 1.75;
            color: rgba(255, 255, 255, 0.72);
            max-width: 500px;
            margin-bottom: 26px;
        }

        .hero_visual {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero_icon {
            position: relative;
            width: 300px;
            height: 300px;
            max-width: 76vw;
            color: #F8F5EF;
            border-radius: 50%;
            background: radial-gradient(circle at 50% 38%, rgba(184, 134, 24, 0.18), rgba(184, 134, 24, 0) 68%);
            filter: drop-shadow(0 24px 48px rgba(0, 0, 0, 0.4));
            will-change: transform, box-shadow;
            /* Flottement doux + respiration (échelle très légère) + glow discret */
            animation: fj-hero-float 7s ease-in-out infinite;
        }
        .hero_icon::before {
            content: "";
            position: absolute;
            inset: -16px;
            border-radius: 50%;
            border: 1.5px dashed rgba(184, 134, 24, 0.45);
            animation: fj-spin 42s linear infinite;
        }
        .hero_icon::after {
            content: "";
            position: absolute;
            inset: 16px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.10);
        }

        .hero_icon svg { display: block; width: 100%; height: 100%; }
        .hero_icon img { display: block; width: 100%; height: 100%; object-fit: contain; }

        .hero_dot {
            position: absolute;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 30%, #e0b14d, var(--fj-or));
            box-shadow: 0 0 18px rgba(184, 134, 24, 0.8);
            animation: fj-float 4.5s ease-in-out infinite;
        }
        .hero_dot--1 { top: 16%; right: 12%; }
        .hero_dot--2 { bottom: 20%; left: 8%; width: 10px; height: 10px; animation-delay: 1.2s; }

        @keyframes fj-spin { to { transform: rotate(360deg); } }
        @keyframes fj-float {
            0%, 100% { transform: translateY(0); }
            50%      { transform: translateY(-14px); }
        }
        /* Logo hero : monte/descend lentement, « respire » (scale très faible)
           et diffuse un halo or discret — 60fps (transform + box-shadow uniquement). */
        @keyframes fj-hero-float {
            0%, 100% {
                transform: translateY(0) scale(1);
                box-shadow: 0 0 0 0 rgba(184, 134, 24, 0), 0 24px 48px rgba(0, 0, 0, 0.35);
            }
            50% {
                transform: translateY(-12px) scale(1.02);
                box-shadow: 0 0 58px 12px rgba(184, 134, 24, 0.16), 0 24px 48px rgba(0, 0, 0, 0.35);
            }
        }

        /* ---------- Boutons (partagés par la page d'accueil) ---------- */
        .slider_section .detail-box a.btn1,
        .offer_section .box .detail-box a,
        .food_section .btn-box a,
        .about_section .detail-box a,
        .partner_section .box a.btn1,
        .partner_section .box button.btn1 {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px 34px;
            margin-top: 0;
            border: none;
            border-radius: 50px;
            font-family: 'Poppins', 'Open Sans', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.3px;
            line-height: 1.2;
            color: #ffffff;
            background: linear-gradient(135deg, #c8931f, var(--fj-or) 55%, var(--fj-or-sombre));
            box-shadow: var(--fj-ombre-or);
            cursor: pointer;
            transition: transform .25s ease, box-shadow .25s ease, background .25s ease, color .25s ease;
        }
        .slider_section .detail-box a.btn1:hover,
        .offer_section .box .detail-box a:hover,
        .food_section .btn-box a:hover,
        .about_section .detail-box a:hover,
        .partner_section .box a.btn1:hover,
        .partner_section .box button.btn1:hover {
            transform: translateY(-3px);
            background: linear-gradient(135deg, #d8a52b, #c8931f 55%, #a37209);
            box-shadow: 0 14px 30px rgba(184, 134, 24, 0.5);
            color: #ffffff;
        }
        .slider_section .detail-box a.btn1:active,
        .offer_section .box .detail-box a:active,
        .food_section .btn-box a:active,
        .about_section .detail-box a:active,
        .partner_section .box a.btn1:active,
        .partner_section .box button.btn1:active { transform: translateY(-1px); }

        /* ---------- Titres de section : filet or sous le titre ---------- */
        .heading_container h2 {
            position: relative;
            padding-bottom: 14px;
            letter-spacing: -0.3px;
        }
        .heading_container h2::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            width: 58px;
            height: 3px;
            border-radius: 3px;
            background: linear-gradient(90deg, var(--fj-or), var(--fj-or-clair));
        }
        .heading_container.heading_center h2::after {
            left: 50%;
            transform: translateX(-50%);
        }

        /* ---------- Offres ---------- */
        .offer_section { padding-top: 56px; }

        .offer_section .box {
            display: flex;
            align-items: center;
            margin-top: 28px;
            border-radius: 20px;
            padding: 26px 22px;
            background: linear-gradient(150deg, #1e1e24, var(--fj-noir));
            border: 1px solid rgba(255, 255, 255, 0.05);
            color: #ffffff;
            box-shadow: 0 16px 34px rgba(0, 0, 0, 0.16);
            transition: transform .3s ease, box-shadow .3s ease;
        }
        .offer_section .box:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 44px rgba(0, 0, 0, 0.24);
        }

        .offer_section .box .img-box {
            width: 150px;
            min-width: 150px;
            height: 150px;
            margin-right: 18px;
            border: 3px solid var(--fj-or);
            box-shadow: 0 0 0 6px rgba(184, 134, 24, 0.12);
        }
        .offer_section .box:hover .img-box img { transform: scale(1.12); }

        .offer_section .box .detail-box h5 {
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
        }
        .offer_section .box .detail-box h6 { color: rgba(255, 255, 255, 0.85); }
        .offer_section .box .detail-box h6 span { color: var(--fj-or); }
        .offer_section .box .detail-box a { margin-top: 14px; }

        /* ---------- Menu de la semaine ---------- */
        .food_section { padding-top: 40px; }

        .food_section .filters_menu {
            margin: 34px 0 16px;
            gap: 6px;
        }
        .food_section .filters_menu li {
            padding: 8px 24px;
            font-weight: 600;
            font-size: 0.95rem;
            color: #5f5a50;
            border: 1px solid rgba(0, 0, 0, 0.07);
            transition: all .3s ease;
        }
        .food_section .filters_menu li:hover { color: var(--fj-or); border-color: rgba(184, 134, 24, 0.4); }
        .food_section .filters_menu li.active {
            background: linear-gradient(135deg, #c8931f, var(--fj-or));
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 6px 14px rgba(184, 134, 24, 0.32);
        }

        .food_section .box .price-tag { color: var(--fj-or); font-weight: 700; }
        .food_section .box .categorie-tag {
            display: inline-block; font-size: 0.72rem; text-transform: uppercase;
            letter-spacing: 0.05em; color: #7a5810; background: #f4ecd8;
            border-radius: 20px; padding: 2px 10px; margin-bottom: 8px;
        }

        .food_section .box {
            margin-top: 25px;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 12px 26px rgba(0, 0, 0, 0.08);
            transition: transform .3s ease, box-shadow .3s ease;
        }
        .food_section .box:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.16);
        }
        .food_section .box:hover .img-box img { transform: scale(1.12); }

        .food_section .box .detail-box { padding: 22px 24px 26px; }

        .food_section .box .options a {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #c8931f, var(--fj-or));
            box-shadow: 0 4px 12px rgba(184, 134, 24, 0.35);
            transition: transform .25s ease, box-shadow .25s ease;
        }
        .food_section .box .options a:hover {
            transform: scale(1.12) rotate(-6deg);
            box-shadow: 0 8px 18px rgba(184, 134, 24, 0.5);
        }

        .food_section .btn-box { margin-top: 40px; }

        .menu-empty-state {
            text-align: center; color: #8a8478; opacity: 0.85; padding: 44px 20px;
        }
        .menu-samedi-note {
            text-align: center; color: #c38d19; font-weight: 600; font-size: 0.9rem;
            margin-bottom: 22px; letter-spacing: 0.02em;
        }

        /* ---------- Menu de la semaine : cartes par jour + samedi (menu libre) ---------- */
        .menu-days {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
            gap: 16px;
            margin-top: 34px;
        }
        .menu-day-card {
            display: flex;
            flex-direction: column;
            gap: 10px;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.07);
            border-radius: 16px;
            padding: 20px 22px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.07);
            transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease;
        }
        .menu-day-card:hover {
            transform: translateY(-3px);
            border-color: rgba(184, 134, 24, 0.55);
            box-shadow: 0 18px 34px rgba(184, 134, 24, 0.16), 0 14px 28px rgba(0, 0, 0, 0.10);
        }
        .menu-day-card:focus-visible {
            outline: 2px solid rgba(184, 134, 24, 0.6);
            outline-offset: 2px;
        }
        .menu-day-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
        .menu-day-name {
            font-weight: 800;
            font-size: 0.95rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--fj-or-sombre);
        }
        .menu-day-status {
            flex-shrink: 0;
            font-size: 0.66rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 20px;
        }
        .menu-day-status-open { background: #f4ecd8; color: #7a5810; }
        .menu-day-status-closed { background: #fdecea; color: #c0392b; }
        .menu-day-status-unavailable { background: #f1efe9; color: #8a8478; }
        .menu-day-dish {
            margin: 0;
            font-size: 1.02rem;
            font-weight: 700;
            line-height: 1.35;
            color: #171717;
        }
        .menu-day-cat { font-size: 0.78rem; color: #8a8478; }
        .menu-day-more {
            display: inline-block;
            margin-top: 3px;
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--fj-or-sombre);
            background: rgba(255, 190, 51, 0.18);
            border-radius: 20px;
            padding: 2px 9px;
        }
        .menu-day-card-foot {
            margin-top: auto;
            padding-top: 6px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .menu-day-price { font-size: 1.08rem; font-weight: 800; color: var(--fj-or); }
        .menu-day-voir {
            flex-basis: 100%;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--fj-or-sombre);
            transition: color .25s ease;
        }
        .menu-day-voir::after {
            content: "→";
            transition: transform .25s ease;
        }
        html[dir="rtl"] .menu-day-voir::after { content: "←"; }
        .menu-day-card:hover .menu-day-voir { color: var(--fj-or); }
        .menu-day-card:hover .menu-day-voir::after { transform: translateX(3px); }
        html[dir="rtl"] .menu-day-card:hover .menu-day-voir::after { transform: translateX(-3px); }

        .menu-add-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #c8931f, var(--fj-or));
            color: #ffffff;
            font-size: 1.4rem;
            font-weight: 600;
            line-height: 1;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(184, 134, 24, 0.35);
            transition: transform .25s ease, box-shadow .25s ease;
        }
        .menu-add-btn:hover {
            transform: scale(1.12) rotate(-6deg);
            box-shadow: 0 8px 18px rgba(184, 134, 24, 0.5);
            color: #ffffff;
        }

        /* Samedi — Menu libre (fond or de l'ancien design) */
        .menu-samedi {
            margin-top: 34px;
            padding: 26px 24px;
            border-radius: 18px;
            background: linear-gradient(135deg, #c8931f, var(--fj-or));
            box-shadow: 0 14px 30px rgba(184, 134, 24, 0.30);
        }
        .menu-samedi-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        .menu-samedi-title { margin: 0; color: #ffffff; font-size: 1.3rem; font-weight: 800; }
        .menu-samedi-date {
            background: rgba(23, 23, 23, 0.20);
            color: #ffffff;
            font-size: 0.78rem;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 20px;
        }
        .menu-samedi-desc { margin: 10px 0 0; color: rgba(255, 255, 255, 0.9); font-size: 0.85rem; }
        .menu-samedi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
            gap: 12px;
            margin-top: 18px;
        }
        .menu-samedi-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            background: #ffffff;
            border-radius: 14px;
            padding: 14px 16px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.16);
            transition: transform .25s ease, box-shadow .25s ease;
        }
        .menu-samedi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.22);
        }
        .menu-samedi-dish { font-size: 0.92rem; font-weight: 700; color: #171717; line-height: 1.3; }
        .menu-samedi-meta { display: block; margin-top: 2px; font-size: 0.76rem; color: #8a8478; }
        .menu-samedi-badge {
            flex-shrink: 0;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 4px 10px;
            border-radius: 20px;
        }
        .menu-samedi-badge-closed { background: #fdecea; color: #c0392b; }
        .menu-samedi-badge-unavailable { background: #f1efe9; color: #8a8478; }

        [data-theme="dark"] .menu-day-card {
            background: #1b1b21;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.35);
        }
        [data-theme="dark"] .menu-day-card:hover {
            border-color: rgba(224, 177, 77, 0.5);
            box-shadow: 0 18px 34px rgba(0, 0, 0, 0.45), 0 6px 22px rgba(224, 177, 77, 0.16);
        }
        [data-theme="dark"] .menu-day-card:focus-visible { outline-color: rgba(224, 177, 77, 0.7); }
        [data-theme="dark"] .menu-day-name { color: #e0b14d; }
        [data-theme="dark"] .menu-day-dish { color: #f2efe8; }
        [data-theme="dark"] .menu-day-cat { color: #b9b2a6; }
        [data-theme="dark"] .menu-day-voir { color: #e0b14d; }
        [data-theme="dark"] .menu-day-status-open { background: #3a3120; color: #e0b14d; }
        [data-theme="dark"] .menu-day-status-closed { background: #452824; color: #ff9b8f; }
        [data-theme="dark"] .menu-day-status-unavailable { background: #2a2a30; color: #b9b2a6; }
        [data-theme="dark"] .menu-samedi-card {
            background: #232329;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
        }
        [data-theme="dark"] .menu-samedi-dish { color: #f2efe8; }
        [data-theme="dark"] .menu-samedi-meta { color: #b9b2a6; }
        [data-theme="dark"] .menu-samedi-badge-closed { background: #452824; color: #ff9b8f; }
        [data-theme="dark"] .menu-samedi-badge-unavailable { background: #2a2a30; color: #b9b2a6; }

        .menu-day-card, .menu-samedi { cursor: pointer; }
        .menu-samedi:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 42px rgba(184, 134, 24, 0.42);
        }

        body.fj-modal-ouvert { overflow: hidden; }

        /* ---------- Modale "Menu du jour" (panneau flottant centré) ---------- */
        .fj-modal {
            position: fixed;
            inset: 0;
            z-index: 2200;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            transition: opacity .3s ease, visibility .3s ease;
        }
        .fj-modal.is-open { opacity: 1; visibility: visible; }
        .fj-modal-overlay {
            position: absolute;
            inset: 0;
            background: rgba(10, 10, 14, 0.68);
            -webkit-backdrop-filter: blur(3px);
            backdrop-filter: blur(3px);
        }
        .fj-modal-dialog {
            position: relative;
            width: 100%;
            max-width: 720px;
            max-height: min(86vh, 640px);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.38);
            transform: translateY(18px) scale(0.97);
            transition: transform .3s cubic-bezier(.22, .61, .36, 1);
        }
        .fj-modal.is-open .fj-modal-dialog { transform: none; }
        .fj-modal-close {
            position: absolute;
            top: 12px;
            right: 14px;
            z-index: 2;
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.06);
            color: #171717;
            font-size: 1.5rem;
            line-height: 1;
            cursor: pointer;
            transition: background-color .2s ease, color .2s ease, transform .2s ease;
        }
        .fj-modal-close:hover {
            background: rgba(184, 134, 24, 0.18);
            color: var(--fj-or-sombre);
            transform: rotate(90deg);
        }
        .fj-modal-panel {
            display: none;
            overflow-y: auto;
            padding: 26px 26px 28px;
        }
        .fj-modal-panel.is-active { display: block; }
        .fj-modal-head { padding-right: 40px; margin-bottom: 14px; }
        .fj-modal-title {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            color: #171717;
        }
        .fj-modal-date {
            display: inline-block;
            margin-top: 8px;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--fj-or-sombre);
            background: #f4ecd8;
            padding: 4px 12px;
            border-radius: 20px;
        }
        .fj-modal-closed {
            display: inline-block;
            margin: 0 0 14px;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            background: #c0392b;
            color: #ffffff;
        }
        .fj-modal-list { display: flex; flex-direction: column; gap: 14px; }
        .fj-modal-dish {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.07);
            border-radius: 14px;
            padding: 12px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
        }
        .fj-modal-dish-img {
            width: 96px;
            height: 96px;
            flex-shrink: 0;
            border-radius: 12px;
            overflow: hidden;
            background: #f1efe9;
        }
        .fj-modal-dish-img img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .fj-modal-dish-info {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .fj-modal-dish-name { font-size: 1rem; font-weight: 700; color: #171717; line-height: 1.3; }
        .fj-modal-dish-cat {
            align-self: flex-start;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #7a5810;
            background: #f4ecd8;
            padding: 2px 10px;
            border-radius: 20px;
        }
        .fj-modal-dish-desc { margin: 0; font-size: 0.8rem; color: #8a8478; line-height: 1.45; }
        .fj-modal-dish-foot {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 6px;
        }
        .fj-modal-dish-price { font-size: 1.02rem; font-weight: 800; color: var(--fj-or); }
        .fj-modal-dish-status {
            font-size: 0.66rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 3px 10px;
            border-radius: 20px;
        }
        .fj-modal-dish-status-ok { background: #e7f4e7; color: #2e7d32; }
        .fj-modal-dish-status-ko { background: #f1efe9; color: #8a8478; }
        .fj-modal-add {
            margin-left: auto;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 30px;
            background: linear-gradient(135deg, #c8931f, var(--fj-or));
            color: #ffffff;
            font-size: 0.85rem;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(184, 134, 24, 0.35);
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .fj-modal-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(184, 134, 24, 0.5);
            color: #ffffff;
        }
        .fj-modal-add .ico-plus { font-size: 1.1rem; font-weight: 600; line-height: 1; }

        /* ---------- Modale "Devenir partenaire" (email + envoi du lien) ---------- */
        .fj-modal-dialog--sm { max-width: 460px; }
        .fj-partenaire-sub { margin: 6px 0 0; color: #8a8478; font-size: 0.92rem; line-height: 1.5; }
        .fj-partenaire-intro { margin: 0 0 16px; color: #5f5a50; font-size: 0.92rem; line-height: 1.6; }
        .fj-partenaire-champ label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.9rem; color: #171717; }
        .fj-partenaire-champ .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e0d8c6;
            border-radius: 12px;
            font-size: 0.95rem;
            color: #171717;
            background: #fbfaf6;
            transition: border-color .2s ease, box-shadow .2s ease;
        }
        .fj-partenaire-champ .form-control:focus {
            border-color: var(--fj-or);
            box-shadow: 0 0 0 3px rgba(184, 134, 24, 0.18);
            outline: none;
        }
        .fj-partenaire-erreur {
            margin-top: 10px;
            padding: 9px 12px;
            border-radius: 10px;
            background: #fdecea;
            border: 1px solid #f5c6c2;
            color: #b23b2e;
            font-size: 0.86rem;
            line-height: 1.5;
        }
        .fj-partenaire-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-top: 20px;
        }
        .fj-partenaire-annuler {
            padding: 12px 22px;
            border: 1px solid #d8d2c4;
            border-radius: 50px;
            background: #ffffff;
            color: #5f5a50;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: border-color .2s ease, color .2s ease, background .2s ease;
        }
        .fj-partenaire-annuler:hover { border-color: #b23b2e; color: #b23b2e; background: #fdf3f2; }
        .fj-partenaire-continuer {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 30px;
            border: none;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.3px;
            color: #ffffff;
            background: linear-gradient(135deg, #c8931f, var(--fj-or) 55%, var(--fj-or-sombre));
            box-shadow: var(--fj-ombre-or);
            cursor: pointer;
            transition: transform .25s ease, box-shadow .25s ease, background .25s ease;
        }
        .fj-partenaire-continuer:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #d8a52b, #c8931f 55%, #a37209);
            box-shadow: 0 14px 30px rgba(184, 134, 24, 0.5);
        }
        .fj-partenaire-continuer:disabled { opacity: 0.55; cursor: not-allowed; transform: none; }
        .fj-partenaire-ok { text-align: center; font-size: 3rem; color: #2e7d32; margin: 8px 0 6px; }
        .fj-partenaire-message { margin: 0; color: #5f5a50; line-height: 1.7; font-size: 0.95rem; }
        .fj-partenaire-message strong { color: #171717; }
        .fj-partenaire-fermer {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px 30px;
            border: none;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1rem;
            color: #ffffff;
            background: linear-gradient(135deg, #c8931f, var(--fj-or) 55%, var(--fj-or-sombre));
            box-shadow: var(--fj-ombre-or);
            cursor: pointer;
            transition: transform .25s ease, box-shadow .25s ease;
        }
        .fj-partenaire-fermer:hover { transform: translateY(-2px); box-shadow: 0 14px 30px rgba(184, 134, 24, 0.5); }

        [data-theme="dark"] .fj-partenaire-sub { color: #b9b2a6; }
        [data-theme="dark"] .fj-partenaire-intro { color: #b9b2a6; }
        [data-theme="dark"] .fj-partenaire-champ label { color: #f2efe8; }
        [data-theme="dark"] .fj-partenaire-champ .form-control { background: #232329; border-color: #2e2e36; color: #f2efe8; }
        [data-theme="dark"] .fj-partenaire-erreur { background: rgba(178, 59, 46, 0.15); border-color: rgba(178, 59, 46, 0.4); color: #ff9a8d; }
        [data-theme="dark"] .fj-partenaire-annuler { background: #1b1b21; border-color: #2e2e36; color: #b9b2a6; }
        [data-theme="dark"] .fj-partenaire-annuler:hover { border-color: #ff9a8d; color: #ff9a8d; background: rgba(178, 59, 46, 0.12); }
        [data-theme="dark"] .fj-partenaire-message { color: #b9b2a6; }
        [data-theme="dark"] .fj-partenaire-message strong { color: #f2efe8; }

        [data-theme="dark"] .fj-modal-dialog { background: #1b1b21; }
        [data-theme="dark"] .fj-modal-title { color: #f2efe8; }
        [data-theme="dark"] .fj-modal-close { background: rgba(255, 255, 255, 0.08); color: #f2efe8; }
        [data-theme="dark"] .fj-modal-close:hover { background: rgba(184, 134, 24, 0.25); color: #e0b14d; }
        [data-theme="dark"] .fj-modal-date { background: #3a3120; color: #e0b14d; }
        [data-theme="dark"] .fj-modal-dish {
            background: #232329;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
        }
        [data-theme="dark"] .fj-modal-dish-img { background: #2a2a30; }
        [data-theme="dark"] .fj-modal-dish-name { color: #f2efe8; }
        [data-theme="dark"] .fj-modal-dish-cat { background: #3a3120; color: #e0b14d; }
        [data-theme="dark"] .fj-modal-dish-desc { color: #b9b2a6; }
        [data-theme="dark"] .fj-modal-dish-status-ok { background: #1f3321; color: #81c784; }
        [data-theme="dark"] .fj-modal-dish-status-ko { background: #2a2a30; color: #b9b2a6; }

        @media (max-width: 480px) {
            .fj-modal { padding: 12px; }
            .fj-modal-panel { padding: 20px 16px 22px; }
            .fj-modal-dish-img { width: 76px; height: 76px; }
        }

        /* ---------- À propos ---------- */
        .about_section {
            background: linear-gradient(160deg, #1b1b21, var(--fj-noir));
            color: #ffffff;
        }
        .about_section .img-box img {
            max-width: 460px;
            width: 100%;
            border-radius: 24px;
            box-shadow: 0 28px 56px rgba(0, 0, 0, 0.4);
        }
        .about_section .detail-box h2 { color: #ffffff; }
        .about_section .detail-box p {
            margin-top: 16px;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.8;
            font-size: 1.05rem;
            max-width: 520px;
        }
        .about_section .detail-box a { margin-top: 20px; }

        /* ---------- Devenir partenaire ---------- */
        .partner_section .heading_container { margin-bottom: 20px; }

        .partner_section .box {
            text-align: center;
            padding: 40px 28px;
            height: 100%;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 20px;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.06);
            transition: transform .3s ease, box-shadow .3s ease;
        }
        .partner_section .box:hover {
            transform: translateY(-6px);
            box-shadow: 0 22px 44px rgba(0, 0, 0, 0.12);
        }
        .partner_section .box i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 92px;
            height: 92px;
            border-radius: 50%;
            color: var(--fj-or) !important;
            background: rgba(184, 134, 24, 0.10);
            border: 1px dashed rgba(184, 134, 24, 0.45);
            transition: transform .3s ease, background-color .3s ease;
        }
        .partner_section .box:hover i {
            transform: translateY(-4px) scale(1.06);
            background: rgba(184, 134, 24, 0.16);
        }
        .partner_section .box h5 { margin: 18px 0 10px; font-weight: 700; }
        .partner_section .box p { color: #5f5a50; line-height: 1.7; }
        .partner_section .box a.btn1, .partner_section .box button.btn1 { margin-top: 14px; }

        /* ---------- Témoignages ---------- */
        .client_section .heading_container { margin-bottom: 30px; }

        .client_section .box .detail-box {
            background: linear-gradient(160deg, #1d1d23, var(--fj-noir));
            color: #ffffff;
            padding: 30px 28px 16px;
            border-radius: 20px;
            box-shadow: 0 16px 34px rgba(0, 0, 0, 0.16);
            position: relative;
        }
        .client_section .box .detail-box::before {
            content: "\201C";
            position: absolute;
            top: 6px;
            left: 24px;
            font-family: Georgia, serif;
            font-size: 64px;
            line-height: 1;
            color: rgba(184, 134, 24, 0.35);
        }
        .client_section .box .detail-box p { font-size: 15px; line-height: 1.7; }
        .client_section .box .detail-box h6 { font-weight: 700; font-size: 18px; color: var(--fj-or-clair); }

        /* Les flèches précédent/suivant gardent toujours leur position
           physique (précédent = flèche de gauche, suivant = flèche de
           droite), quelle que soit la langue : c'est le sens de défilement
           interne d'Owl Carousel (option "rtl", cf. custom.js) qui s'adapte
           à l'arabe, pas la position des flèches. */
        .client_section .owl-carousel .owl-nav {
            direction: ltr;
        }
        .client_section .owl-carousel .owl-nav .owl-prev,
        .client_section .owl-carousel .owl-nav .owl-next {
            background: rgba(184, 134, 24, 0.14);
            color: var(--fj-or);
            transition: all .3s ease;
        }
        .client_section .owl-carousel .owl-nav .owl-prev:hover,
        .client_section .owl-carousel .owl-nav .owl-next:hover {
            background: var(--fj-or);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(184, 134, 24, 0.4);
        }

        /* La mécanique du carrousel (largeurs/positions/flottement des
           cartes calculés par Owl Carousel) doit rester stable et
           prévisible : on isole donc explicitement sa direction en LTR,
           indépendamment du sens hérité de <html dir="rtl">. C'est ce sens
           hérité (combiné au conteneur ".row" en display:flex juste
           au-dessus) qui faisait sortir les cartes du champ visible en
           arabe alors que les flèches de navigation, elles, restaient
           visibles. Owl gère lui-même le défilement RTL via son option
           "rtl" (cf. custom.js) : ce n'est plus le rôle du CSS hérité.
           On NE force PAS ce sens sur toute la section (le reste du bloc
           "Ce que disent nos clients" garde le sens de la page) : seule la
           mécanique du carrousel est isolée. */
        .client_section .owl-carousel.client_owl-carousel {
            direction: ltr;
        }

        /* Le texte des avis (citation, nom, rôle) doit lui rester dans le
           sens de la langue courante : on réapplique explicitement le RTL
           hérité de la page sur le contenu, qui serait sinon repassé en LTR
           par la règle ci-dessus. C'est le seul endroit où le sens de
           lecture arabe doit s'appliquer dans le carrousel. */
        html[dir="rtl"] .client_section .owl-carousel .box .detail-box {
            direction: rtl;
            text-align: right;
        }
        html[dir="rtl"] .client_section .owl-carousel .box .detail-box::before {
            left: auto;
            right: 24px;
        }

        /* ---------- Pied de page ---------- */
        .footer_section {
            padding: 64px 0 34px;
            position: relative;
        }
        .footer_section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 130px;
            height: 3px;
            border-radius: 3px;
            background: linear-gradient(90deg, transparent, var(--fj-or), transparent);
        }
        .footer_section .footer-logo {
            font-size: 30px;
            letter-spacing: 0.5px;
            color: #ffffff;
        }
        .footer_section .footer_social a {
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            transition: all .3s ease;
        }
        .footer_section .footer_social a:hover {
            background: var(--fj-or);
            color: #ffffff;
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(184, 134, 24, 0.4);
        }
        .footer_section .footer_contact .contact_link_box a { transition: color .3s ease, padding-left .3s ease; }
        .footer_section .footer_contact .contact_link_box a:hover { padding-left: 6px; }

        /* ---------- Transitions de couleurs entre thèmes (aucun effet sur le layout) ---------- */
        body, .header_section, .hero_area--branded, .food_section .box,
        .about_section, .client_section .box .detail-box, .footer_section,
        .offer_section .box, .partner_section .box {
            transition: background-color .25s ease, color .25s ease;
        }

        /* ---------- Mode clair : navbar + hero adaptés au thème ----------
           (le mode clair étant le thème par défaut, les styles de base ci-dessus
           décrivent le mode sombre ; ce bloc bascule proprement navbar, hero,
           logo et menus vers une version claire conforme à la charte.) ---------- */

        /* Logo : variante à texte Noir Charbon sur fond clair */
        [data-theme="light"] .logo-theme-light { display: block !important; }
        [data-theme="light"] .logo-theme-dark { display: none !important; }

        /* Navbar claire : fond quasi transparent, intégrée au hero, avec une
           simple ligne de séparation. Au scroll : fond légèrement plus présent
           pour la lisibilité, ombre très discrète. */
        [data-theme="light"] .header_section {
            background: rgba(255, 255, 255, 0.55);
            border-bottom: 1px solid rgba(23, 23, 23, 0.06);
        }
        [data-theme="light"] .header_section.is-scrolled {
            background: rgba(255, 255, 255, 0.90);
            box-shadow: 0 6px 18px rgba(23, 23, 23, 0.08);
        }

        /* Liens du menu en clair : texte noir, actif en pastille or */
        [data-theme="light"] .custom_nav-container .navbar-nav .nav-item .nav-link {
            color: rgba(23, 23, 23, 0.80);
        }
        [data-theme="light"] .custom_nav-container .navbar-nav .nav-item:hover .nav-link,
        [data-theme="light"] .custom_nav-container .navbar-nav .nav-item.active .nav-link { color: #171717; }
        [data-theme="light"] .custom_nav-container .navbar-nav .nav-item.active .nav-link {
            background: rgba(184, 134, 24, 0.14);
        }

        /* Hamburger (bouton menu mobile) en sombre */
        [data-theme="light"] .custom_nav-container .navbar-toggler span,
        [data-theme="light"] .custom_nav-container .navbar-toggler span::before,
        [data-theme="light"] .custom_nav-container .navbar-toggler span::after { background-color: #171717; }

        /* Bascule de thème en clair */
        [data-theme="light"] .header_section .theme-toggle {
            background: rgba(0, 0, 0, 0.05);
            border-color: rgba(23, 23, 23, 0.30);
            color: #333333;
        }
        [data-theme="light"] .header_section .theme-toggle:hover {
            background: rgba(184, 134, 24, 0.15);
            border-color: var(--fj-or);
            color: var(--fj-or-sombre);
            box-shadow: 0 6px 16px rgba(184, 134, 24, 0.20);
        }

        /* Icônes panier + menu profil en clair : bordure lisible, icône visible */
        [data-theme="light"] .user_option .fj-cart-nav {
            border-color: rgba(23, 23, 23, 0.28);
            background: rgba(0, 0, 0, 0.04);
            color: #171717;
        }
        [data-theme="light"] .user_option .fj-cart-nav:hover {
            border-color: var(--fj-or);
            background: rgba(184, 134, 24, 0.12);
        }
        [data-theme="light"] .user_option .fj-cart-nav:hover svg { color: var(--fj-or-sombre); }
        [data-theme="light"] .header_section .profile-menu__trigger {
            border-color: rgba(23, 23, 23, 0.28);
            background: rgba(0, 0, 0, 0.04);
            color: #171717;
        }

        /* Hero clair : fond blanc cassé, texte Noir Charbon */
        [data-theme="light"] .hero_area--branded {
            background: linear-gradient(180deg, #ffffff 0%, #fbf8f1 100%);
        }
        [data-theme="light"] .hero_glow {
            background:
                radial-gradient(circle at 78% 30%, #c38d1926 0%, rgba(184, 134, 24, 0) 55%),
                radial-gradient(circle at 12% 90%, rgba(184, 134, 24, 0.06) 0%, rgba(184, 134, 24, 0) 45%);
        }
        [data-theme="light"] .slider_section .detail-box h1 { color: #171717; }
        [data-theme="light"] .slider_section .detail-box p { color: rgba(23, 23, 23, 0.70); }
        [data-theme="light"] .hero-eyebrow { color: var(--fj-or); }
        [data-theme="light"] .hero_icon {
            color: #171717;
            filter: drop-shadow(0 20px 40px rgba(0, 0, 0, 0.16));
        }
        [data-theme="light"] .hero_icon::after { border-color: rgba(23, 23, 23, 0.08); }

        /* ---------- Mode sombre : variante plus sombre, mêmes composants, mêmes positions ---------- */
        [data-theme="dark"] body { background: #0f0f13; }
        [data-theme="dark"] .heading_container h2 { color: #f2efe8; }
        [data-theme="dark"] .heading_container p { color: #b9b2a6; }
        [data-theme="dark"] .food_section .filters_menu li { color: #e8e4dc; border-color: rgba(255, 255, 255, 0.10); }
        [data-theme="dark"] .food_section .filters_menu li.active { background: linear-gradient(135deg, #c8931f, #B88618); color: #ffffff; }
        [data-theme="dark"] .food_section .box {
            background: linear-gradient(to bottom, #232329 25px, #18181e 25px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.35);
        }
        [data-theme="dark"] .food_section .box .img-box { background: #232329; }
        [data-theme="dark"] .food_section .box .categorie-tag { color: #e0b14d; background: #3a3120; }
        [data-theme="dark"] .partner_section .box {
            background: #1b1b21;
            border-color: rgba(255, 255, 255, 0.06);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.3);
        }
        [data-theme="dark"] .partner_section .box i { background: rgba(184, 134, 24, 0.14); }
        [data-theme="dark"] .partner_section .box h5 { color: #f2efe8; }
        [data-theme="dark"] .partner_section .box p { color: #b9b2a6; }
        [data-theme="dark"] .menu-empty-state { color: #cfc9be; }
        [data-theme="dark"] .menu-samedi-note { color: #e0b14d; }

        /* ---------- Responsive : tablette & mobile ---------- */
        @media (max-width: 991px) {
            .header_section { padding: 8px 0; }
            .custom_nav-container .navbar-collapse {
                background: rgba(23, 23, 23, 0.97);
                margin-top: 10px;
                padding: 16px 18px 22px;
                border-radius: 16px;
                border: 1px solid rgba(255, 255, 255, 0.08);
                box-shadow: 0 24px 48px rgba(0, 0, 0, 0.4);
            }
            [data-theme="light"] .custom_nav-container .navbar-collapse {
                background: rgba(255, 255, 255, 0.98);
                border-color: rgba(0, 0, 0, 0.08);
                box-shadow: 0 24px 48px rgba(0, 0, 0, 0.15);
            }
            .custom_nav-container .navbar-nav {
                padding-left: 0;
                align-items: center;
                gap: 2px;
                width: 100%;
            }
            .custom_nav-container .navbar-nav .nav-item {
                width: 100%;
                text-align: center;
            }
            .custom_nav-container .navbar-nav .nav-item .nav-link {
                padding: 10px 20px;
                margin: 2px 0;
                justify-content: center;
                width: 100%;
            }
            .user_option {
                justify-content: center;
                margin-top: 12px;
                gap: 8px;
            }
            .hero_area--branded { min-height: auto; }
        }

        @media (max-width: 767px) {
            .navbar-brand .logo-mark { width: 50px; height: 50px; }
            .slider_section--static { padding: 44px 0 64px; }
            .slider_section .detail-box h1 { font-size: 2.2rem; }
            .slider_section .detail-box p { font-size: 1rem; }
            .hero_visual { margin-top: 34px; }
            .hero_icon { width: 200px; height: 200px; }
            .hero_dot--1 { right: 6%; }
            .hero_dot--2 { left: 4%; }
            .offer_section .box { padding: 22px 18px; }
            .about_section .detail-box { margin-bottom: 40px; }
            .about_section .row { flex-direction: column-reverse; }
            .about_section .img-box { margin-bottom: 8px; }
            .partner_section .box { padding: 32px 22px; }
            .footer_section { padding: 52px 0 30px; }
        }

        @media (max-width: 480px) {
            .slider_section .detail-box h1 { font-size: 1.9rem; }
            .user_option .order_online { padding: 10px 22px; font-size: 0.9rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            .fj-reveal { opacity: 1 !important; transform: none !important; transition: none !important; }
            .hero_icon, .hero_icon::before, .hero_dot { animation: none !important; }
            * { animation-duration: .01ms !important; }
        }

        /* ---------- Icône Panier de la navbar (client connecté) ---------- */
        .user_option .fj-cart-nav {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.25);
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
            cursor: pointer;
            padding: 0;
            transition: border-color .2s ease, transform .2s ease, box-shadow .2s ease, background-color .2s ease;
        }
        .user_option .fj-cart-nav svg {
            width: 19px;
            height: 19px;
            color: var(--fj-or-clair);
            transition: color .2s ease;
        }
        .user_option .fj-cart-nav:hover {
            border-color: var(--fj-or);
            transform: translateY(-1px);
            background: rgba(184, 134, 24, 0.12);
            box-shadow: 0 6px 16px rgba(184, 134, 24, 0.28);
        }
        .user_option .fj-cart-nav:hover svg { color: #ffffff; }
        .user_option .fj-cart-nav:focus-visible { outline: 2px solid var(--fj-or); outline-offset: 2px; }
        .user_option .fj-cart-badge {
            position: absolute;
            top: -3px;
            right: -3px;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            border-radius: 999px;
            background: var(--fj-or);
            color: #ffffff;
            font-size: 0.68rem;
            font-weight: 700;
            line-height: 18px;
            text-align: center;
            pointer-events: none;
        }
        /* Le menu profil est aussi affiché dans le header public (fond sombre). */
        .header_section .profile-menu__trigger {
            border-color: rgba(255, 255, 255, 0.25);
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
        }
        .header_section .profile-menu__trigger .fa-user { color: var(--fj-or-clair); }
        @media (max-width: 991px) {
            .user_option { justify-content: center; }
        }
    </style>
</head>

<body data-fj-page="accueil">

    <div class="hero_area hero_area--branded">
        <div class="hero_pattern" aria-hidden="true">
            <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="fjDiamonds" x="0" y="0" width="64" height="64" patternUnits="userSpaceOnUse">
                        <rect x="28" y="28" width="8" height="8" fill="#9d7214" transform="rotate(45 32 32)"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#fjDiamonds)"/>
            </svg>
        </div>
        <div class="hero_glow" aria-hidden="true"></div>
        <!-- header -->
        <header class="header_section">
            <div class="container">
                <nav class="navbar navbar-expand-lg custom_nav-container">
                    <a class="navbar-brand" href="<?php echo BASE_URL; ?>/index.php?route=accueil">
                        <span class="logo-mark"><?php include ROOT_PATH . '/assets/inc/logo.php'; ?></span>
                    </a>

                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class=""></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mx-auto">
                            <li class="nav-item active">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/index.php?route=accueil"><span data-i18n="accueil.navAccueil">Accueil</span> <span class="sr-only">(current)</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#menu"><span data-i18n="accueil.navMenuSemaine">Menu de la semaine</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#about"><span data-i18n="accueil.navAPropos">À propos</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#partenaire"><span data-i18n="accueil.navPartenaire">Devenir partenaire</span></a>
                            </li>
                        </ul>
                        <div class="user_option">
                            <?php require ROOT_PATH . '/assets/inc/lang_switcher.php'; ?>
                            <?php $themeToggleClass = 'header-theme-toggle'; require ROOT_PATH . '/assets/inc/theme_toggle.php'; ?>
                            <?php if ($estClientConnecte): ?>
                                <button type="button" class="fj-cart-nav" onclick="fjCartOuvrir()"
                                        aria-label="Ouvrir mon panier" title="Mon panier" data-i18n-aria="nav.ouvrirMonPanier">
                                    <i data-lucide="shopping-cart" aria-hidden="true"></i>
                                    <span class="fj-cart-badge" data-fj-cart-badge<?php echo $panierNb > 0 ? '' : ' hidden'; ?>><?php echo $panierNb > 9 ? '9+' : $panierNb; ?></span>
                                </button>
                                <?php $profileMenuVariant = 'light'; require ROOT_PATH . '/assets/inc/profile_menu.php'; ?>
                            <?php else: ?>
                                <a href="<?php echo $lienCommander; ?>" class="order_online">
                                    <span data-i18n="accueil.commander">Commander</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </nav>
            </div>
        </header>
        <!-- end header -->

        <!-- hero (sans photo, 100% charte graphique) -->
        <section class="slider_section slider_section--static">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-7 col-lg-6">
                        <div class="detail-box">
                            <span class="hero-eyebrow fj-reveal" data-i18n="accueil.heroEyebrow">Repas marocain chaud, livré à l'heure</span>
                            <h1 class="fj-reveal fj-reveal-delay-1" data-i18n="accueil.heroTitre">Des repas faits maison, livrés chez vous</h1>
                            <p class="fj-reveal fj-reveal-delay-2" data-i18n="accueil.heroSousTitre">
                                Commandez des plats préparés avec soin par des cuisiniers locaux
                                et recevez-les chauds directement à votre porte, en quelques clics.
                            </p>
                            <div class="btn-box fj-reveal fj-reveal-delay-3">
                                <a href="<?php echo $lienCommander; ?>" class="btn1">
                                    <span data-i18n="accueil.consulterMenu"<?php echo $estClientConnecte ? '' : ' hidden'; ?>>Consulter le menu</span>
                                    <span data-i18n="accueil.commencerCommander"<?php echo $estClientConnecte ? ' hidden' : ''; ?>>Commencer à commander</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 col-lg-6 hero_visual fj-reveal fj-reveal-delay-2">
                        <div class="hero_icon"><?php include ROOT_PATH . '/assets/inc/logo.php'; ?></div>
                        <span class="hero_dot hero_dot--1" aria-hidden="true"></span>
                        <span class="hero_dot hero_dot--2" aria-hidden="true"></span>
                    </div>
                </div>
            </div>
        </section>
        <!-- end hero -->
    </div>

    <!-- offer section -->
    <section class="offer_section layout_padding-bottom">
        <div class="offer_container">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 fj-reveal">
                        <div class="box">
                            <div class="img-box">
                                <img src="<?php echo $photoCouscous; ?>" alt="Cuisine locale">
                            </div>
                            <div class="detail-box">
                                <h5 data-i18n="accueil.offre1Titre">Cuisine 100% locale</h5>
                                <h6 data-i18n="accueil.offre1Sous"><span>Fait</span> maison</h6>
                                <a href="<?php echo $lienCommander; ?>">
                                    <span data-i18n="accueil.commander">Commander</span> <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 fj-reveal fj-reveal-delay-1">
                        <div class="box">
                            <div class="img-box">
                                <img src="<?php echo $photoViande; ?>" alt="Livraison rapide">
                            </div>
                            <div class="detail-box">
                                <h5 data-i18n="accueil.offre2Titre">Livraison rapide</h5>
                                <h6 data-i18n="accueil.offre2Sous"><span>Chaud</span> et à l'heure</h6>
                                <a href="<?php echo $lienCommander; ?>">
                                    <span data-i18n="accueil.commander">Commander</span> <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end offer section -->

    <!-- food / menu de la semaine section -->
    <section class="food_section layout_padding-bottom" id="menu">
        <div class="container">
            <div class="heading_container heading_center fj-reveal">
                <h2 data-i18n="accueil.menuTitre">Menu de la semaine</h2>
            </div>

            <?php if (!$hasMenu): ?>
                <div class="menu-empty-state fj-reveal" data-i18n="accueil.menuVide">
                    Aucun menu n'est publié pour le moment. Revenez bientôt !
                </div>
            <?php else: ?>
                <?php
                $dateParJour   = [];
                $ouvertParJour = [];
                foreach ($jourLabels as $jourKey => $jourLabel) {
                    $date = $menuSemaineModele->prochaineDatePourJour($jourKey);
                    $dateParJour[$jourKey] = $date;
                    $ouvertParJour[$jourKey] = false;
                    if ($date) {
                        [$ouvertParJour[$jourKey]] = $menuSemaineModele->dateLivraisonValide($date);
                    }
                }

                $statutJour = [];
                foreach ($jourLabels as $jourKey => $jourLabel) {
                    $aUnPlatDisponible = false;
                    foreach (($itemsParJour[$jourKey] ?? []) as $platJour) {
                        if (!empty($platJour['disponible'])) {
                            $aUnPlatDisponible = true;
                            break;
                        }
                    }
                    if ($ouvertParJour[$jourKey] && $aUnPlatDisponible) {
                        $statutJour[$jourKey] = 'ouvert';
                    } elseif ($ouvertParJour[$jourKey]) {
                        $statutJour[$jourKey] = 'indisponible';
                    } else {
                        $statutJour[$jourKey] = 'cloture';
                    }
                }

                $dateSamedi = $menuSemaineModele->prochaineDatePourJour(JOUR_MENU_LIBRE);
                $samediOuvert = false;
                if ($dateSamedi) {
                    [$samediOuvert] = $menuSemaineModele->dateLivraisonValide($dateSamedi);
                }

                $itemsSamedi = [];
                foreach ($jourLabels as $jourKey => $jourLabel) {
                    foreach (($itemsParJour[$jourKey] ?? []) as $item) {
                        $itemsSamedi[$item['product_id']] = $item;
                    }
                }

                $panneauxModale = [];
                foreach ($jourLabels as $jourKey => $jourLabel) {
                    if (empty($itemsParJour[$jourKey])) {
                        continue;
                    }
                    $panneauxModale[$jourKey] = [
                        'titre'   => $jourLabel,
                        'date'    => $dateParJour[$jourKey],
                        'ouvert'  => $statutJour[$jourKey] === 'ouvert',
                        'cloture' => $statutJour[$jourKey] === 'cloture',
                        'items'   => $itemsParJour[$jourKey],
                    ];
                }
                if (!empty($itemsSamedi)) {
                    $panneauxModale['samedi'] = [
                        'titre'   => 'Samedi — Menu libre',
                        'date'    => $samediOuvert ? $dateSamedi : null,
                        'ouvert'  => $samediOuvert,
                        'cloture' => !$samediOuvert,
                        'items'   => array_values($itemsSamedi),
                    ];
                }

                $platsParId = [];
                require_once ROOT_PATH . '/modele/PlatModele.php';
                foreach ((new PlatModele())->getMenu() as $plat) {
                    $platsParId[(int) $plat['id']] = $plat;
                }
                ?>

                <div class="menu-days">
                    <?php foreach ($jourLabels as $jourKey => $jourLabel): ?>
                        <?php $itemsJour = $itemsParJour[$jourKey] ?? []; ?>
                        <?php if (empty($itemsJour)): continue; endif; ?>
                        <?php $item = $itemsJour[0]; ?>
                        <?php $nbPlatsJour = count($itemsJour); ?>
                        <article class="menu-day-card" data-fj-modal-open="<?php echo $jourKey; ?>" role="button" tabindex="0" aria-haspopup="dialog" aria-label="Voir le menu du <?php echo $jourLabel; ?>">
                            <header class="menu-day-card-head">
                                <span class="menu-day-name" data-i18n="jours.<?php echo $jourKey; ?>"><?php echo $jourLabel; ?></span>
                                <span class="menu-day-status menu-day-status-<?php echo $statutJour[$jourKey]; ?>" data-i18n="common.<?php echo $statutJour[$jourKey] === 'ouvert' ? 'ouvert' : ($statutJour[$jourKey] === 'cloture' ? 'cloture' : 'indisponible'); ?>">
                                    <?php echo $statutJour[$jourKey] === 'ouvert' ? 'Ouvert' : ($statutJour[$jourKey] === 'cloture' ? 'Clôturé' : 'Indisponible'); ?>
                                </span>
                            </header>
                            <div class="menu-day-card-body">
                                <h4 class="menu-day-dish"><?php echo htmlspecialchars($item['plat_nom']); ?></h4>
                                <?php if (!empty($item['categorie'])): ?>
                                    <span class="menu-day-cat"><?php echo htmlspecialchars($item['categorie']); ?></span>
                                <?php endif; ?>
                                <?php if ($nbPlatsJour > 1): ?>
                                    <?php $autresPlats = $nbPlatsJour - 1; ?>
                                    <span class="menu-day-more">+ <?php echo $autresPlats; ?> <span data-i18n="accueil.autresPlats">autre(s) plat(s)</span></span>
                                <?php endif; ?>
                            </div>
                            <footer class="menu-day-card-foot">
                                <span class="menu-day-price">
                                    <?php echo isset($item['prix']) ? number_format((float) $item['prix'], 2) . ' MAD' : ''; ?>
                                </span>
                                <?php if ($statutJour[$jourKey] === 'ouvert'): ?>
                                    <a class="menu-add-btn"
                                       href="<?php echo $estClientConnecte
                                           ? BASE_URL . '/index.php?route=client&ajouter=' . (int) $item['product_id'] . '&date=' . $dateParJour[$jourKey]
                                           : url_connexion_avec_retour('client', ['ajouter' => (int) $item['product_id'], 'date' => $dateParJour[$jourKey]]); ?>"
                                       title="<?php echo $estClientConnecte
                                           ? 'Ajouter au panier · livraison le ' . date('d/m/Y', strtotime($dateParJour[$jourKey]))
                                           : 'Connectez-vous pour commander'; ?>"
                                       aria-label="Ajouter au panier" data-i18n-aria="common.ajouterPanier">+</a>
                                    <?php endif; ?>
                            </footer>
                            <span class="menu-day-voir" data-i18n="accueil.voirPlats">Voir les plats</span>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($itemsSamedi)): ?>
                <section class="menu-samedi" data-fj-modal-open="samedi" role="button" tabindex="0" aria-haspopup="dialog" aria-label="Voir le menu libre du samedi">
                    <header class="menu-samedi-head">
                        <h3 class="menu-samedi-title" data-i18n="jours.samediMenuLibre">Samedi — Menu libre</h3>
                        <?php if ($samediOuvert && $dateSamedi): ?>
                            <span class="menu-samedi-date"><span data-i18n="common.livraisonLe">Livraison le</span> <?php echo date('d/m/Y', strtotime($dateSamedi)); ?></span>
                        <?php endif; ?>
                    </header>
                    <p class="menu-samedi-desc" data-i18n="accueil.samediDesc">
                        Aucun menu spécifique le samedi : choisissez librement parmi tous les plats de la semaine.
                    </p>
                    <div class="menu-samedi-grid">
                        <?php foreach ($itemsSamedi as $item): ?>
                            <div class="menu-samedi-card">
                                <div>
                                    <span class="menu-samedi-dish"><?php echo htmlspecialchars($item['plat_nom']); ?></span>
                                    <span class="menu-samedi-meta">
                                        <?php if (!empty($item['categorie'])): ?><?php echo htmlspecialchars($item['categorie']); ?> · <?php endif; ?>
                                        <?php echo isset($item['prix']) ? number_format((float) $item['prix'], 2) . ' MAD' : ''; ?>
                                    </span>
                                </div>
                                <?php if ($samediOuvert && $item['disponible']): ?>
                                    <a class="menu-add-btn"
                                       href="<?php echo $estClientConnecte
                                           ? BASE_URL . '/index.php?route=client&ajouter=' . (int) $item['product_id'] . '&date=' . $dateSamedi
                                           : BASE_URL . '/index.php?route=inscription'; ?>"
                                       title="<?php echo $estClientConnecte
                                           ? 'Ajouter au panier · livraison le ' . date('d/m/Y', strtotime($dateSamedi))
                                           : 'Créez un compte pour commander'; ?>"
                                       aria-label="Ajouter au panier" data-i18n-aria="common.ajouterPanier">+</a>
                                <?php elseif (!$samediOuvert): ?>
                                    <span class="menu-samedi-badge menu-samedi-badge-closed" data-i18n="common.cloture">Clôturé</span>
                                <?php else: ?>
                                    <span class="menu-samedi-badge menu-samedi-badge-unavailable" data-i18n="common.indisponible">Indisponible</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>
            <?php endif; ?>

            <div class="btn-box fj-reveal">
                <a href="<?php echo $lienCommander; ?>">
                    <span data-i18n="accueil.consulterMenuComplet"<?php echo $estClientConnecte ? '' : ' hidden'; ?>>Consulter le menu complet</span>
                    <span data-i18n="accueil.creerCompte"<?php echo $estClientConnecte ? ' hidden' : ''; ?>>Créer un compte pour commander</span>
                </a>
            </div>
        </div>
    </section>
    <!-- end food section -->

    <?php if ($hasMenu): ?>
    <!-- modale "Menu du jour" : panneaux pré-rendus (données existantes), ouverts sans rechargement -->
    <div class="fj-modal" id="fj-modal" role="dialog" aria-modal="true" aria-label="Menu du jour">
        <div class="fj-modal-overlay" data-fj-modal-close></div>
        <div class="fj-modal-dialog">
            <button type="button" class="fj-modal-close" data-fj-modal-close aria-label="Fermer">&times;</button>

            <?php foreach ($panneauxModale as $jourKey => $panneau): ?>
                <div class="fj-modal-panel" data-fj-modal-panel="<?php echo $jourKey; ?>">
                    <div class="fj-modal-head">
                        <h3 class="fj-modal-title" data-i18n="<?php echo $jourKey === 'samedi' ? 'jours.samediMenuLibre' : 'jours.' . $jourKey; ?>"><?php echo htmlspecialchars($panneau['titre']); ?></h3>
                        <?php if ($panneau['date']): ?>
                            <span class="fj-modal-date"><span data-i18n="common.livraisonLe">Livraison le</span> <?php echo date('d/m/Y', strtotime($panneau['date'])); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ($panneau['cloture']): ?>
                        <span class="fj-modal-closed" data-i18n="common.cloture">Clôturé</span>
                    <?php endif; ?>
                    <div class="fj-modal-list">
                        <?php foreach ($panneau['items'] as $item): ?>
                            <?php $itemDescription = $platsParId[(int) $item['product_id']]['description'] ?? null; ?>
                            <article class="fj-modal-dish">
                                <div class="fj-modal-dish-img">
                                    <?php if (!empty($item['image'])): ?>
                                        <img src="<?php echo UPLOADS_URL . '/' . rawurlencode($item['image']); ?>" alt="<?php echo htmlspecialchars($item['plat_nom']); ?>" loading="lazy">
                                    <?php else: ?>
                                        <img src="<?php echo $photoTajine; ?>" alt="<?php echo htmlspecialchars($item['plat_nom']); ?>" loading="lazy">
                                    <?php endif; ?>
                                </div>
                                <div class="fj-modal-dish-info">
                                    <span class="fj-modal-dish-name"><?php echo htmlspecialchars($item['plat_nom']); ?></span>
                                    <?php if (!empty($item['categorie'])): ?>
                                        <span class="fj-modal-dish-cat"><?php echo htmlspecialchars($item['categorie']); ?></span>
                                    <?php endif; ?>
                                    <?php if ($itemDescription): ?>
                                        <p class="fj-modal-dish-desc"><?php echo htmlspecialchars($itemDescription); ?></p>
                                    <?php endif; ?>
                                    <div class="fj-modal-dish-foot">
                                        <span class="fj-modal-dish-price">
                                            <?php echo isset($item['prix']) ? number_format((float) $item['prix'], 2) . ' MAD' : ''; ?>
                                        </span>
                                        <span class="fj-modal-dish-status <?php echo $item['disponible'] ? 'fj-modal-dish-status-ok' : 'fj-modal-dish-status-ko'; ?>" data-i18n="<?php echo $item['disponible'] ? 'common.disponible' : 'common.indisponible'; ?>">
                                            <?php echo $item['disponible'] ? 'Disponible' : 'Indisponible'; ?>
                                        </span>
                                        <?php if ($panneau['ouvert'] && $item['disponible']): ?>
                                            <a class="fj-modal-add"
                                               href="<?php echo $estClientConnecte
                                                   ? BASE_URL . '/index.php?route=client&ajouter=' . (int) $item['product_id'] . '&date=' . $panneau['date']
                                                   : BASE_URL . '/index.php?route=inscription'; ?>"
                                               title="<?php echo $estClientConnecte
                                                   ? 'Ajouter au panier · livraison le ' . date('d/m/Y', strtotime($panneau['date']))
                                                   : 'Créez un compte pour commander'; ?>">
                                                <span class="ico-plus">+</span> <span data-i18n="menu.ajouter">Ajouter</span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- modale "Devenir partenaire" : saisie de l'email (cuisinier / livreur) -->
    <div class="fj-modal" id="fj-modal-partenaire" role="dialog" aria-modal="true" aria-label="Devenir partenaire" aria-hidden="true" data-i18n-aria="accueil.partenaireModalTitre">
        <div class="fj-modal-overlay" data-fj-partenaire-close></div>
        <div class="fj-modal-dialog fj-modal-dialog--sm">
            <button type="button" class="fj-modal-close" data-fj-partenaire-close aria-label="Fermer" data-i18n-aria="common.fermer">&times;</button>

            <div class="fj-modal-panel is-active">
                <div class="fj-modal-head">
                    <h3 class="fj-modal-title" id="fj-partenaire-titre" data-i18n="accueil.partenaireModalTitre">Devenir partenaire</h3>
                    <p class="fj-partenaire-sub" id="fj-partenaire-sous-titre" data-i18n="accueil.partenaireModalSub">Rejoignez FiaJou3 et complétez votre dossier.</p>
                </div>

                <div id="fj-partenaire-forme">
                    <p class="fj-partenaire-intro" data-i18n="accueil.partenaireModalIntro">Indiquez votre email : nous vous enverrons un lien sécurisé pour compléter votre dossier.</p>
                    <form id="fj-partenaire-form" novalidate>
                        <input type="hidden" name="role" id="fj-partenaire-role" value="">
                        <div class="fj-partenaire-champ">
                            <label for="fj-partenaire-email" data-i18n="accueil.partenaireModalEmail">Email</label>
                            <input type="email" class="form-control" id="fj-partenaire-email" name="email"
                                   placeholder="votre@email.fr" required autocomplete="email">
                        </div>
                        <div class="fj-partenaire-erreur" id="fj-partenaire-erreur" hidden></div>
                        <div class="fj-partenaire-actions">
                            <button type="button" class="btn fj-partenaire-annuler" data-fj-partenaire-close data-i18n="accueil.partenaireModalAnnuler">Annuler</button>
                            <button type="submit" class="btn1 fj-partenaire-continuer" data-i18n="accueil.partenaireModalContinuer">Continuer</button>
                        </div>
                    </form>
                </div>

                <div id="fj-partenaire-succes" hidden>
                    <p class="fj-partenaire-ok"><i class="fa fa-check-circle" aria-hidden="true"></i></p>
                    <p class="fj-partenaire-message" id="fj-partenaire-message"></p>
                    <div class="fj-partenaire-actions">
                        <button type="button" class="btn1 fj-partenaire-fermer" data-fj-partenaire-close data-i18n="accueil.partenaireModalFermer">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Libellés traduits pour la modale "Devenir partenaire", utilisés par le
         script ci-dessous (aucun texte n'y est codé en dur : tout provient du
         dictionnaire i18n via ces éléments cachés, pour rester cohérent avec
         la langue active, y compris après un changement de langue sans
         rechargement). -->
    <div hidden aria-hidden="true">
        <span id="fj-i18n-titre-cuisinier" data-i18n="accueil.partenaireCuisinier">Devenir cuisinier partenaire</span>
        <span id="fj-i18n-sub-cuisinier" data-i18n="accueil.partenaireModalSubCuisinier">Rejoignez FiaJou3 en tant que cuisinier.</span>
        <span id="fj-i18n-titre-livreur" data-i18n="accueil.partenaireLivreur">Devenir livreur partenaire</span>
        <span id="fj-i18n-sub-livreur" data-i18n="accueil.partenaireModalSubLivreur">Rejoignez FiaJou3 en tant que livreur.</span>
        <span id="fj-i18n-email-invalide" data-i18n="accueil.partenaireModalEmailInvalide">Veuillez saisir une adresse email valide.</span>
        <span id="fj-i18n-erreur-generique" data-i18n="accueil.partenaireModalErreurGenerique">Une erreur est survenue. Veuillez réessayer.</span>
    </div>

    <!-- about section -->
    <section class="about_section layout_padding" id="about">
        <div class="container">
            <div class="row">
                <div class="col-md-6 fj-reveal">
                    <div class="img-box">
                        <img src="<?php echo BASE_URL; ?>/uploads/taj.png" alt="À propos de <?php echo htmlspecialchars(APP_NAME); ?>">
                    </div>
                </div>
                <div class="col-md-6 fj-reveal fj-reveal-delay-1">
                    <div class="detail-box">
                        <div class="heading_container">
                            <h2 data-i18n="accueil.aboutTitre">Qui sommes-nous</h2>
                        </div>
                        <p data-i18n="accueil.aboutTexte">
                            <?php echo htmlspecialchars(APP_NAME); ?> met en relation des cuisiniers locaux passionnés
                            avec des gourmands pressés. Chaque plat est préparé à la commande, comme à la maison,
                            puis livré rapidement par nos livreurs partenaires près de chez vous.
                        </p>
                        <a href="#partenaire" data-i18n="accueil.aboutEnSavoirPlus">
                            En savoir plus
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end about section -->

    <!-- devenir partenaire section (remplace la réservation de table) -->
    <section class="book_section layout_padding partner_section" id="partenaire">
        <div class="container">
            <div class="heading_container fj-reveal">
                <h2 data-i18n="accueil.partenaireTitre">Rejoignez FiaJou3</h2>
            </div>
            <div class="row">
                <div class="col-md-6 fj-reveal">
                    <div class="box">
                        <i class="fa fa-cutlery fa-3x" aria-hidden="true" style="color:#B88618;"></i>
                        <h5 data-i18n="accueil.partenaireCuisinier">Devenir cuisinier partenaire</h5>
                        <p data-i18n="accueil.partenaireCuisinierTexte">Partagez vos recettes faites maison et vendez vos plats à de nouveaux clients chaque semaine.</p>
                        <button type="button" class="btn1" data-fj-partenaire="cuisinier" data-i18n="accueil.partenaireJeMinscris">Je m'inscris</button>
                    </div>
                </div>
                <div class="col-md-6 fj-reveal fj-reveal-delay-1">
                    <div class="box">
                        <i class="fa fa-motorcycle fa-3x" aria-hidden="true" style="color:#B88618;"></i>
                        <h5 data-i18n="accueil.partenaireLivreur">Devenir livreur partenaire</h5>
                        <p data-i18n="accueil.partenaireLivreurTexte">Livrez les commandes dans votre zone et organisez vos tournées selon vos disponibilités.</p>
                        <button type="button" class="btn1" data-fj-partenaire="livreur" data-i18n="accueil.partenaireJeMinscris">Je m'inscris</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end devenir partenaire section -->

    <!-- client section -->
    <section class="client_section layout_padding-bottom">
        <div class="container">
            <div class="heading_container heading_center psudo_white_primary mb_45 fj-reveal">
                <h2 data-i18n="accueil.clientsTitre">Ce que disent nos clients</h2>
            </div>
            <div class="carousel-wrap row fj-reveal fj-reveal-delay-1">
                <div class="owl-carousel client_owl-carousel">
                    <div class="item">
                        <div class="box">
                            <div class="detail-box">
                                <p data-i18n="accueil.temoignage1">
                                    Le tajine était exactement comme celui de ma grand-mère, livré chaud
                                    en moins de 40 minutes. Je recommande vivement !
                                </p>
                                <h6>Salma B.</h6>
                                <p data-i18n="accueil.clientsRegulier">Cliente régulière</p>
                            </div>
                            <div class="img-box">
                                <img src="<?php echo BASE_URL; ?>/assets/feane/images/mik.jpg" alt="" class="box-img">
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="box">
                            <div class="detail-box">
                                <p data-i18n="accueil.temoignage2">
                                    Simple, rapide et surtout de vrais plats faits maison. Le couscous
                                    du vendredi est devenu un rituel chez nous.
                                </p>
                                <h6>Dictator K.</h6>
                                <p data-i18n="accueil.clientsRegulier">Client régulier</p>
                            </div>
                            <div class="img-box">
                                <img src="<?php echo BASE_URL; ?>/assets/feane/images/dic.jpg" alt="" class="box-img">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end client section -->

    <!-- footer section -->
    <footer class="footer_section">
        <div class="container fj-reveal">
            <div class="row">
                <div class="col-md-4 footer-col">
                    <div class="footer_contact">
                        <h4 data-i18n="accueil.footerContact">Contactez-nous</h4>
                        <div class="contact_link_box">
                            <a href="#">
                                <i class="fa fa-map-marker" aria-hidden="true"></i>
                                <span data-i18n="accueil.footerMaroc">Maroc</span>
                            </a>
                            <a href="tel:+212000000000">
                                <i class="fa fa-phone" aria-hidden="true"></i>
                                <span>+212 626372836</span>
                            </a>
                            <a href="mailto:contact@fiajou3.ma">
                                <i class="fa fa-envelope" aria-hidden="true"></i>
                                <span>contact@fiajou3.ma</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 footer-col">
                    <div class="footer_detail">
                        <a href="<?php echo BASE_URL; ?>/index.php?route=accueil" class="footer-logo">
                            <?php echo htmlspecialchars(APP_NAME); ?>
                        </a>
                        <p data-i18n="accueil.footerTexte">
                            Des repas faits maison, préparés par des cuisiniers locaux et livrés
                            rapidement chez vous.
                        </p>
                        <div class="footer_social">
                            <a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                            <a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a>
                            <a href="#"><i class="fa fa-whatsapp" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 footer-col">
                    <h4 data-i18n="accueil.footerHoraires">Horaires de commande</h4>
                    <p data-i18n="accueil.footerTousLesJours">Tous les jours</p>
                    <p>11h00 - 22h00</p>
                </div>
            </div>
            <div class="footer-info">
                <p>
                    &copy; <span id="displayYear"></span> <?php echo htmlspecialchars(APP_NAME); ?>. <span data-i18n="accueil.footerDroits">Tous droits réservés.</span>
                </p>
            </div>
        </div>
    </footer>
    <!-- end footer section -->

    <?php if ($estClientConnecte) require ROOT_PATH . '/assets/inc/mini_panier.php'; ?>

    <script src="<?php echo BASE_URL; ?>/assets/feane/js/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="<?php echo BASE_URL; ?>/assets/feane/js/bootstrap.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://unpkg.com/isotope-layout@3.0.4/dist/isotope.pkgd.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/feane/js/custom.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/theme.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/profile-menu.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/i18n.js?<?php echo (int) @filemtime(ROOT_PATH . '/assets/js/i18n.js'); ?>"></script>
    <script>if (window.lucide) { lucide.createIcons(); }</script>

    <script>
        // Le header est en position fixed : on compense sa hauteur réelle
        // (variable selon la largeur d'écran / l'ouverture du menu mobile)
        // par un padding-top sur .hero_area pour ne pas masquer le contenu.
        (function () {
            var header = document.querySelector('.header_section');
            var hero = document.querySelector('.hero_area');
            if (!header || !hero) return;

            function ajusterEspaceHeader() {
                hero.style.paddingTop = header.offsetHeight + 'px';
            }

            ajusterEspaceHeader();
            window.addEventListener('resize', ajusterEspaceHeader);
            window.addEventListener('load', ajusterEspaceHeader);

            // Le menu mobile (Bootstrap collapse) change la hauteur du header
            // quand il s'ouvre/se ferme : on réajuste dans ces cas aussi.
            var menuMobile = document.getElementById('navbarSupportedContent');
            if (menuMobile && window.jQuery) {
                jQuery(menuMobile).on('shown.bs.collapse hidden.bs.collapse', ajusterEspaceHeader);
            }

            // Ombre portée du header quand on descend dans la page.
            function majOmbreHeader() {
                header.classList.toggle('is-scrolled', (window.scrollY || 0) > 12);
            }
            majOmbreHeader();
            window.addEventListener('scroll', majOmbreHeader, { passive: true });
        })();
    </script>

    <script>
        // Apparition douce des sections au scroll (fade + slide léger).
        // Désactivé si l'utilisateur préfère réduire les animations.
        (function () {
            var mq = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)');
            if (mq && mq.matches) return;

            document.documentElement.classList.add('fj-js');

            var elements = document.querySelectorAll('.fj-reveal');
            if (!elements.length) return;

            function rendreVisible(el) { el.classList.add('is-visible'); }

            if ('IntersectionObserver' in window) {
                var observer = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            rendreVisible(entry.target);
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

                elements.forEach(function (el) { observer.observe(el); });

                // Filet de sécurité : tout afficher même si l'observer échoue.
                setTimeout(function () {
                    elements.forEach(rendreVisible);
                }, 4500);
            } else {
                elements.forEach(rendreVisible);
            }
        })();
    </script>

    <script>
        // Modale "Menu du jour" : ouverture depuis les cartes du menu de la semaine.
        // Aucune navigation ni rechargement : les panneaux sont déjà rendus dans la page.
        (function () {
            var modale = document.getElementById('fj-modal');
            if (!modale) return;

            var panneaux = modale.querySelectorAll('[data-fj-modal-panel]');
            var declencheurActif = null;

            function ouvrir(jour, declencheur) {
                for (var i = 0; i < panneaux.length; i++) {
                    panneaux[i].classList.toggle('is-active', panneaux[i].getAttribute('data-fj-modal-panel') === jour);
                }
                declencheurActif = declencheur;
                modale.classList.add('is-open');
                document.body.classList.add('fj-modal-ouvert');
                var boutonFermer = modale.querySelector('.fj-modal-close');
                if (boutonFermer) boutonFermer.focus();
            }

            function fermer() {
                modale.classList.remove('is-open');
                document.body.classList.remove('fj-modal-ouvert');
                if (declencheurActif && declencheurActif.focus) declencheurActif.focus();
                declencheurActif = null;
            }

            // Ouverture : clic sur une carte du jour ou sur la section samedi.
            // Le bouton "+" (ajout direct existant) ne doit pas ouvrir la modale.
            document.addEventListener('click', function (e) {
                if (e.target.closest && e.target.closest('.menu-add-btn')) return;
                var declencheur = e.target.closest ? e.target.closest('[data-fj-modal-open]') : null;
                if (declencheur) ouvrir(declencheur.getAttribute('data-fj-modal-open'), declencheur);
            });

            // Fermeture : bouton X, clic sur le voile (extérieur), touche Échap.
            modale.addEventListener('click', function (e) {
                if (e.target === modale || (e.target.closest && e.target.closest('[data-fj-modal-close]'))) {
                    fermer();
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.target.closest && e.target.closest('.menu-add-btn')) return;
                if (e.key === 'Escape' && modale.classList.contains('is-open')) {
                    fermer();
                    return;
                }
                if ((e.key === 'Enter' || e.key === ' ') && e.target.closest && e.target.closest('[data-fj-modal-open]')) {
                    e.preventDefault();
                    var declencheur = e.target.closest('[data-fj-modal-open]');
                    ouvrir(declencheur.getAttribute('data-fj-modal-open'), declencheur);
                }
            });
        })();
    </script>

    <script>
        // Modale "Devenir partenaire" : demande d'email (cuisinier / livreur).
        // Ouverture depuis les deux boutons "Je m'inscris" de la section
        // "Rejoignez FiaJou3". Envoi AJAX vers la route partenaire/demande,
        // puis affichage du message de confirmation (email avec lien envoyé).
        (function () {
            var BASE_URL = '<?php echo BASE_URL; ?>';
            var modale = document.getElementById('fj-modal-partenaire');
            if (!modale) return;

            var titre = document.getElementById('fj-partenaire-titre');
            var sousTitre = document.getElementById('fj-partenaire-sous-titre');
            var champRole = document.getElementById('fj-partenaire-role');
            var email = document.getElementById('fj-partenaire-email');
            var formulaire = document.getElementById('fj-partenaire-form');
            var forme = document.getElementById('fj-partenaire-forme');
            var succes = document.getElementById('fj-partenaire-succes');
            var message = document.getElementById('fj-partenaire-message');
            var erreur = document.getElementById('fj-partenaire-erreur');
            var boutonContinuer = formulaire.querySelector('.fj-partenaire-continuer');

            // Lit le texte déjà traduit par i18n.js dans les éléments cachés
            // ci-dessus, plutôt que de coder les libellés en dur ici : la
            // modale reste correcte quelle que soit la langue active, y
            // compris après un changement de langue sans rechargement.
            function texteI18n(id, repli) {
                var el = document.getElementById(id);
                return (el && el.textContent) ? el.textContent : repli;
            }

            function libellesRole(role) {
                if (role === 'livreur') {
                    return {
                        titre: texteI18n('fj-i18n-titre-livreur', 'Devenir livreur partenaire'),
                        sub: texteI18n('fj-i18n-sub-livreur', 'Rejoignez FiaJou3 en tant que livreur.')
                    };
                }
                return {
                    titre: texteI18n('fj-i18n-titre-cuisinier', 'Devenir cuisinier partenaire'),
                    sub: texteI18n('fj-i18n-sub-cuisinier', 'Rejoignez FiaJou3 en tant que cuisinier.')
                };
            }

            function ouvrir(role) {
                var l = libellesRole(role);
                if (titre) titre.textContent = l.titre;
                if (sousTitre) sousTitre.textContent = l.sub;
                if (champRole) champRole.value = role;
                formulaire.reset();
                if (email) email.value = '';
                if (erreur) { erreur.hidden = true; erreur.textContent = ''; }
                forme.hidden = false;
                succes.hidden = true;
                modale.classList.add('is-open');
                modale.setAttribute('aria-hidden', 'false');
                document.body.classList.add('fj-modal-ouvert');
                if (email) email.focus();
            }

            function fermer() {
                modale.classList.remove('is-open');
                modale.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('fj-modal-ouvert');
            }

            document.addEventListener('click', function (e) {
                if (e.target.closest('[data-fj-partenaire]')) {
                    var role = e.target.closest('[data-fj-partenaire]').getAttribute('data-fj-partenaire');
                    ouvrir(role);
                    return;
                }
                if (e.target === modale || (e.target.closest && e.target.closest('[data-fj-partenaire-close]'))) {
                    fermer();
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && modale.classList.contains('is-open')) {
                    fermer();
                }
            });

            formulaire.addEventListener('submit', function (e) {
                e.preventDefault();
                var role = champRole.value;
                var valeur = email.value.trim();
                if (!valeur || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valeur)) {
                    if (erreur) {
                        erreur.textContent = texteI18n('fj-i18n-email-invalide', 'Veuillez saisir une adresse email valide.');
                        erreur.hidden = false;
                    }
                    return;
                }

                boutonContinuer.disabled = true;
                if (erreur) { erreur.hidden = true; }

                var corps = new URLSearchParams();
                corps.append('email', valeur);
                corps.append('role', role);

                fetch(BASE_URL + '/index.php?route=partenaire/demande', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: corps.toString()
                })
                    .then(function (resp) { return resp.json(); })
                    .then(function (data) {
                        if (data && data.ok) {
                            if (message) { message.innerHTML = data.message; }
                            forme.hidden = true;
                            succes.hidden = false;
                        } else {
                            if (erreur) {
                                erreur.innerHTML = (data && data.message) ? data.message : "Une erreur est survenue. Veuillez réessayer.";
                                erreur.hidden = false;
                            }
                        }
                    })
                    .catch(function () {
                        if (erreur) {
                            erreur.textContent = "Une erreur est survenue. Veuillez réessayer.";
                            erreur.hidden = false;
                        }
                    })
                    .finally(function () {
                        boutonContinuer.disabled = false;
                    });
            });
        })();
    </script>

</body>
</html>
