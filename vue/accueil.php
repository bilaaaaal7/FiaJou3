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
$photoCouscous = UPLOADS_URL . '/couscous.jpg';
$photoViande   = UPLOADS_URL . '/viande-hachee-poulet.jpg';

$hasMenu = $menu && !empty($itemsParJour) && array_sum(array_map('count', $itemsParJour)) > 0;
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <script>
        (function () {
            var t = null;
            try { t = localStorage.getItem('fiajou3-theme'); } catch (e) { /* ignore */ }
            if (t !== 'dark') { t = 'light'; }
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Commandez des repas faits maison préparés par des cuisiniers locaux et faites-vous livrer rapidement avec <?php echo APP_NAME; ?>.">
    <meta name="keywords" content="repas maison, livraison de repas, cuisine locale, commande de repas, <?php echo APP_NAME; ?>">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>

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
            padding: 12px 0;
            background: rgba(23, 23, 23, 0.92);
            -webkit-backdrop-filter: blur(12px);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            transition: background-color .3s ease, box-shadow .3s ease, padding .3s ease;
        }
        .header_section.is-scrolled {
            background: rgba(23, 23, 23, 0.97);
            padding: 8px 0;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.35);
        }

        /* ---------- Barre de navigation : alignement + espacements ---------- */
        .custom_nav-container .navbar-nav {
            padding-left: 0;
            gap: 4px;
            align-items: center;
        }

        .navbar-brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 0;
        }
        .logo-mark { display: inline-flex; align-items: center; justify-content: center; }
        .logo-mark svg { display: block; width: 100%; height: 100%; }
        .logo-mark img { display: block; width: 100%; height: 100%; object-fit: contain; }
        .navbar-brand .logo-mark { width: 38px; height: 38px; flex-shrink: 0; }
        .navbar-brand span {
            font-weight: 800;
            font-size: 26px;
            letter-spacing: 0.4px;
            color: #ffffff;
            line-height: 1;
        }

        .custom_nav-container .navbar-nav .nav-item .nav-link {
            position: relative;
            padding: 9px 18px;
            margin: 0 2px;
            color: rgba(255, 255, 255, 0.85);
            text-transform: uppercase;
            font-weight: 600;
            font-size: 0.93rem;
            letter-spacing: 0.5px;
            border-radius: 30px;
            transition: color .3s ease, background-color .3s ease;
        }
        .custom_nav-container .navbar-nav .nav-item .nav-link::after {
            content: "";
            position: absolute;
            left: 18px;
            right: 18px;
            bottom: 4px;
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

        .user_option { gap: 10px; }

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
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.35);
            color: #f5f5f5;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background-color .3s ease, border-color .3s ease, color .3s ease, transform .3s ease;
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
            animation: fj-float 6s ease-in-out infinite;
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

        /* ---------- Boutons (partagés par la page d'accueil) ---------- */
        .slider_section .detail-box a.btn1,
        .offer_section .box .detail-box a,
        .food_section .btn-box a,
        .about_section .detail-box a,
        .partner_section .box a.btn1 {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px 34px;
            margin-top: 0;
            border: none;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.3px;
            color: #ffffff;
            background: linear-gradient(135deg, #c8931f, var(--fj-or) 55%, var(--fj-or-sombre));
            box-shadow: var(--fj-ombre-or);
            transition: transform .25s ease, box-shadow .25s ease, background .25s ease, color .25s ease;
        }
        .slider_section .detail-box a.btn1:hover,
        .offer_section .box .detail-box a:hover,
        .food_section .btn-box a:hover,
        .about_section .detail-box a:hover,
        .partner_section .box a.btn1:hover {
            transform: translateY(-3px);
            background: linear-gradient(135deg, #d8a52b, #c8931f 55%, #a37209);
            box-shadow: 0 14px 30px rgba(184, 134, 24, 0.5);
            color: #ffffff;
        }
        .slider_section .detail-box a.btn1:active,
        .offer_section .box .detail-box a:active,
        .food_section .btn-box a:active,
        .about_section .detail-box a:active,
        .partner_section .box a.btn1:active { transform: translateY(-1px); }

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
        .partner_section .box a.btn1 { margin-top: 14px; }

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

        /* Navbar claire */
        [data-theme="light"] .header_section {
            background: rgba(255, 255, 255, 0.92);
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }
        [data-theme="light"] .header_section.is-scrolled {
            background: rgba(255, 255, 255, 0.97);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
        }
        [data-theme="light"] .navbar-brand span { color: #171717; }

        /* Liens du menu en clair : texte noir, actif en pastille or */
        [data-theme="light"] .custom_nav-container .navbar-nav .nav-item .nav-link {
            color: rgba(23, 23, 23, 0.82);
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
        }

        /* Menu mobile (collapse) en clair */
        [data-theme="light"] .custom_nav-container .navbar-collapse {
            background: rgba(255, 255, 255, 0.98);
            border-color: rgba(0, 0, 0, 0.08);
            box-shadow: 0 24px 48px rgba(0, 0, 0, 0.15);
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
            .custom_nav-container .navbar-collapse {
                background: rgba(23, 23, 23, 0.97);
                margin-top: 10px;
                padding: 16px 18px 22px;
                border-radius: 16px;
                border: 1px solid rgba(255, 255, 255, 0.08);
                box-shadow: 0 24px 48px rgba(0, 0, 0, 0.4);
            }
            .custom_nav-container .navbar-nav {
                padding-left: 0;
                align-items: center;
                gap: 2px;
            }
            .custom_nav-container .navbar-nav .nav-item .nav-link {
                padding: 10px 20px;
                margin: 2px 0;
                text-align: center;
            }
            .user_option {
                justify-content: center;
                margin-top: 12px;
                gap: 8px;
            }
            .hero_area--branded { min-height: auto; }
        }

        @media (max-width: 767px) {
            .navbar-brand span { font-size: 22px; }
            .navbar-brand .logo-mark { width: 32px; height: 32px; }
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
    </style>
</head>

<body>

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
                        <span><?php echo htmlspecialchars(APP_NAME); ?></span>
                    </a>

                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class=""></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mx-auto">
                            <li class="nav-item active">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/index.php?route=accueil">Accueil <span class="sr-only">(current)</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#menu">Menu de la semaine</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#about">À propos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#partenaire">Devenir partenaire</a>
                            </li>
                        </ul>
                        <div class="user_option">
                            <?php $themeToggleClass = 'header-theme-toggle'; require ROOT_PATH . '/assets/inc/theme_toggle.php'; ?>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=inscription" class="order_online">
                                Commander
                            </a>
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
                            <span class="hero-eyebrow fj-reveal">Repas marocain chaud, livré à l'heure</span>
                            <h1 class="fj-reveal fj-reveal-delay-1">Des repas faits maison, livrés chez vous</h1>
                            <p class="fj-reveal fj-reveal-delay-2">
                                Commandez des plats préparés avec soin par des cuisiniers locaux
                                et recevez-les chauds directement à votre porte, en quelques clics.
                            </p>
                            <div class="btn-box fj-reveal fj-reveal-delay-3">
                                <a href="<?php echo BASE_URL; ?>/index.php?route=inscription" class="btn1">
                                    Commencer à commander
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
                                <h5>Cuisine 100% locale</h5>
                                <h6><span>Fait</span> maison</h6>
                                <a href="<?php echo BASE_URL; ?>/index.php?route=inscription">
                                    Commander <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
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
                                <h5>Livraison rapide</h5>
                                <h6><span>Chaud</span> et à l'heure</h6>
                                <a href="<?php echo BASE_URL; ?>/index.php?route=inscription">
                                    Commander <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
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
                <h2>Menu de la semaine</h2>
            </div>

            <?php if (!$hasMenu): ?>
                <div class="menu-empty-state fj-reveal">
                    Aucun menu n'est publié pour le moment. Revenez bientôt !
                </div>
            <?php else: ?>
                <ul class="filters_menu fj-reveal">
                    <li class="active" data-filter="*">Tous les jours</li>
                    <?php foreach ($jourLabels as $jourKey => $jourLabel): ?>
                        <?php if (!empty($itemsParJour[$jourKey])): ?>
                            <li data-filter=".<?php echo $jourKey; ?>"><?php echo $jourLabel; ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <li data-filter=".samedi">Samedi · Menu libre</li>
                </ul>

                <p class="menu-samedi-note fj-reveal fj-reveal-delay-1">
                    Le samedi, le menu est libre : tous les plats de la semaine sont commandables.
                </p>

                <div class="filters-content fj-reveal fj-reveal-delay-2">
                    <div class="row grid">
                        <?php foreach ($jourLabels as $jourKey => $jourLabel): ?>
                            <?php foreach (($itemsParJour[$jourKey] ?? []) as $item): ?>
                                <div class="col-sm-6 col-lg-4 all samedi <?php echo $jourKey; ?>">
                                    <div class="box">
                                        <div>
                                            <div class="img-box">
                                                <?php if (!empty($item['image'])): ?>
                                                    <img src="<?php echo UPLOADS_URL . '/' . rawurlencode($item['image']); ?>" alt="<?php echo htmlspecialchars($item['plat_nom']); ?>">
                                                <?php else: ?>
                                                    <img src="<?php echo $photoTajine; ?>" alt="<?php echo htmlspecialchars($item['plat_nom']); ?>">
                                                <?php endif; ?>
                                            </div>
                                            <div class="detail-box">
                                                <span class="categorie-tag"><?php echo htmlspecialchars($jourLabel); ?><?php echo !empty($item['categorie']) ? ' · ' . htmlspecialchars($item['categorie']) : ''; ?></span>
                                                <h5><?php echo htmlspecialchars($item['plat_nom']); ?></h5>
                                                <div class="options">
                                                    <h6 class="price-tag">
                                                        <?php echo isset($item['prix']) ? number_format((float) $item['prix'], 2) . ' MAD' : ''; ?>
                                                    </h6>
                                                    <a href="<?php echo BASE_URL; ?>/index.php?route=inscription">
                                                        <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="btn-box fj-reveal">
                <a href="<?php echo BASE_URL; ?>/index.php?route=inscription">
                    Créer un compte pour commander
                </a>
            </div>
        </div>
    </section>
    <!-- end food section -->

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
                            <h2>Qui sommes-nous</h2>
                        </div>
                        <p>
                            <?php echo htmlspecialchars(APP_NAME); ?> met en relation des cuisiniers locaux passionnés
                            avec des gourmands pressés. Chaque plat est préparé à la commande, comme à la maison,
                            puis livré rapidement par nos livreurs partenaires près de chez vous.
                        </p>
                        <a href="#partenaire">
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
                <h2>Rejoignez FiaJou3</h2>
            </div>
            <div class="row">
                <div class="col-md-6 fj-reveal">
                    <div class="box">
                        <i class="fa fa-cutlery fa-3x" aria-hidden="true" style="color:#B88618;"></i>
                        <h5>Devenir cuisinier partenaire</h5>
                        <p>Partagez vos recettes faites maison et vendez vos plats à de nouveaux clients chaque semaine.</p>
                        <a href="<?php echo BASE_URL; ?>/index.php?route=inscription" class="btn1">Je m'inscris</a>
                    </div>
                </div>
                <div class="col-md-6 fj-reveal fj-reveal-delay-1">
                    <div class="box">
                        <i class="fa fa-motorcycle fa-3x" aria-hidden="true" style="color:#B88618;"></i>
                        <h5>Devenir livreur partenaire</h5>
                        <p>Livrez les commandes dans votre zone et organisez vos tournées selon vos disponibilités.</p>
                        <a href="<?php echo BASE_URL; ?>/index.php?route=inscription" class="btn1">Je m'inscris</a>
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
                <h2>Ce que disent nos clients</h2>
            </div>
            <div class="carousel-wrap row fj-reveal fj-reveal-delay-1">
                <div class="owl-carousel client_owl-carousel">
                    <div class="item">
                        <div class="box">
                            <div class="detail-box">
                                <p>
                                    Le tajine était exactement comme celui de ma grand-mère, livré chaud
                                    en moins de 40 minutes. Je recommande vivement !
                                </p>
                                <h6>Salma B.</h6>
                                <p>Cliente régulière</p>
                            </div>
                            <div class="img-box">
                                <img src="<?php echo BASE_URL; ?>/assets/feane/images/mik.jpg" alt="" class="box-img">
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="box">
                            <div class="detail-box">
                                <p>
                                    Simple, rapide et surtout de vrais plats faits maison. Le couscous
                                    du vendredi est devenu un rituel chez nous.
                                </p>
                                <h6>Dictator K.</h6>
                                <p>Client régulier</p>
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
                        <h4>Contactez-nous</h4>
                        <div class="contact_link_box">
                            <a href="#">
                                <i class="fa fa-map-marker" aria-hidden="true"></i>
                                <span>Maroc</span>
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
                        <p>
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
                    <h4>Horaires de commande</h4>
                    <p>Tous les jours</p>
                    <p>11h00 - 22h00</p>
                </div>
            </div>
            <div class="footer-info">
                <p>
                    &copy; <span id="displayYear"></span> <?php echo htmlspecialchars(APP_NAME); ?>. Tous droits réservés.
                </p>
            </div>
        </div>
    </footer>
    <!-- end footer section -->

    <script src="<?php echo BASE_URL; ?>/assets/feane/js/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="<?php echo BASE_URL; ?>/assets/feane/js/bootstrap.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://unpkg.com/isotope-layout@3.0.4/dist/isotope.pkgd.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/feane/js/custom.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/theme.js"></script>
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

</body>
</html>
