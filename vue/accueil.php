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
        /* Petits ajustements propres à FiaJou3 */
        .food_section .box .price-tag { color: #B88618; font-weight: 700; }
        .food_section .box .categorie-tag {
            display: inline-block; font-size: 0.72rem; text-transform: uppercase;
            letter-spacing: 0.05em; color: #7a5810; background: #f4ecd8;
            border-radius: 20px; padding: 2px 10px; margin-bottom: 6px;
        }
        .menu-empty-state {
            text-align: center; color: #fff; opacity: 0.75; padding: 40px 20px;
        }
        .partner_section .box { text-align: center; padding: 30px 20px; }
        .partner_section .box h5 { margin: 14px 0 10px; }
        .partner_section .box a.btn1 {
            display: inline-block; margin-top: 10px;
        }
        .logo-mark { display: inline-flex; align-items: center; justify-content: center; }
        .logo-mark svg { display: block; width: 100%; height: 100%; }
        .logo-mark img { display: block; width: 100%; height: 100%; object-fit: contain; }
        .footer_detail .footer-logo { display: inline-flex; align-items: center; gap: 10px; }

        /* ---------- Hero sans photo (charte : Noir Charbon + Or Tajine) ---------- */
        .hero_area--branded { background: #171717; }

        .hero_pattern {
            position: absolute; inset: 0; opacity: 0.06; pointer-events: none;
        }

        .hero_glow {
            position: absolute; inset: 0; pointer-events: none;
            background: radial-gradient(circle at 78% 30%, rgba(184,134,24,0.30) 0%, rgba(184,134,24,0) 55%);
        }

        .slider_section--static { padding: 70px 0 90px; }
        .slider_section--static .detail-box { margin-bottom: 0; }

        .hero-eyebrow {
            display: inline-block; color: #B88618; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1.2px; font-size: 0.82rem;
            margin-bottom: 16px;
        }

        .hero_visual { display: flex; align-items: center; justify-content: center; }

        .hero_icon {
            width: 260px; height: 260px; max-width: 70vw; color: #F8F5EF;
            filter: drop-shadow(0 20px 40px rgba(0,0,0,0.35));
        }

        .hero_icon svg { display: block; width: 100%; height: 100%; }
        .hero_icon img { display: block; width: 100%; height: 100%; object-fit: contain; }

        @media (max-width: 767px) {
            .hero_visual { margin-top: 30px; }
            .hero_icon { width: 190px; height: 190px; }
        }
    </style>
</head>

<body>

    <div class="hero_area hero_area--branded">
        <div class="hero_pattern" aria-hidden="true">
            <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="fjDiamonds" x="0" y="0" width="64" height="64" patternUnits="userSpaceOnUse">
                        <rect x="28" y="28" width="8" height="8" fill="#B88618" transform="rotate(45 32 32)"/>
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
                    <a class="navbar-brand" href="<?php echo BASE_URL; ?>/index.php?route=accueil" style="display:inline-flex;align-items:center;gap:10px;">
                        <span class="logo-mark" style="width:34px;height:34px;color:#ffffff;flex-shrink:0;"><?php $logoSurFondSombre = true; include ROOT_PATH . '/assets/inc/logo.php'; ?></span>
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
                            <span class="hero-eyebrow">Repas marocain chaud, livré à l'heure</span>
                            <h1>Des repas faits maison, livrés chez vous</h1>
                            <p>
                                Commandez des plats préparés avec soin par des cuisiniers locaux
                                et recevez-les chauds directement à votre porte, en quelques clics.
                            </p>
                            <div class="btn-box">
                                <a href="<?php echo BASE_URL; ?>/index.php?route=inscription" class="btn1">
                                    Commencer à commander
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 col-lg-6 hero_visual">
                        <div class="hero_icon"><?php $logoSurFondSombre = true; include ROOT_PATH . '/assets/inc/logo.php'; ?></div>
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
                    <div class="col-md-6">
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
                    <div class="col-md-6">
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
            <div class="heading_container heading_center">
                <h2>Menu de la semaine</h2>
            </div>

            <?php if (!$hasMenu): ?>
                <div class="menu-empty-state">
                    Aucun menu n'est publié pour le moment. Revenez bientôt !
                </div>
            <?php else: ?>
                <ul class="filters_menu">
                    <li class="active" data-filter="*">Tous les jours</li>
                    <?php foreach ($jourLabels as $jourKey => $jourLabel): ?>
                        <?php if (!empty($itemsParJour[$jourKey])): ?>
                            <li data-filter=".<?php echo $jourKey; ?>"><?php echo $jourLabel; ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>

                <div class="filters-content">
                    <div class="row grid">
                        <?php foreach ($jourLabels as $jourKey => $jourLabel): ?>
                            <?php foreach (($itemsParJour[$jourKey] ?? []) as $item): ?>
                                <div class="col-sm-6 col-lg-4 all <?php echo $jourKey; ?>">
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

            <div class="btn-box">
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
                <div class="col-md-6">
                    <div class="img-box">
                        <img src="<?php echo BASE_URL; ?>/assets/feane/images/about-img.png" alt="À propos de <?php echo htmlspecialchars(APP_NAME); ?>">
                    </div>
                </div>
                <div class="col-md-6">
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
            <div class="heading_container">
                <h2>Rejoignez FiaJou3</h2>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="box">
                        <i class="fa fa-cutlery fa-3x" aria-hidden="true" style="color:#B88618;"></i>
                        <h5>Devenir cuisinier partenaire</h5>
                        <p>Partagez vos recettes faites maison et vendez vos plats à de nouveaux clients chaque semaine.</p>
                        <a href="<?php echo BASE_URL; ?>/index.php?route=inscription" class="btn1">Je m'inscris</a>
                    </div>
                </div>
                <div class="col-md-6">
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
            <div class="heading_container heading_center psudo_white_primary mb_45">
                <h2>Ce que disent nos clients</h2>
            </div>
            <div class="carousel-wrap row">
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
        <div class="container">
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

</body>
</html>
