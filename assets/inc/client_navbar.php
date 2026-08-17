<?php
/**
 * Barre de navigation horizontale pour les pages client internes.
 * EXACTEMENT la même que la navbar de la page d'accueil (vue/accueil.php).
 * Aucune modification de design — extraction pure du code accueil.
 *
 * Ce fichier est INCLU après header.php (qui ouvre <body> + inclut mini_panier)
 * et AVANT le contenu de la page.
 */

$estClientConnecte = est_connecte() && utilisateur_role() === ROLE_CLIENT;
$panierNb = 0;
if ($estClientConnecte) {
    require_once ROOT_PATH . '/modele/PanierModele.php';
    $panierNb = (new PanierModele())->nombreArticles();
}
$lienCommander = $estClientConnecte
    ? BASE_URL . '/index.php?route=client'
    : url_connexion_avec_retour('client');
?>
        <!-- header -->
        <header class="header_section">
            <div class="container">
                <nav class="navbar navbar-expand-lg custom_nav-container">
                    <a class="navbar-brand" href="<?php echo BASE_URL; ?>/index.php?route=accueil">
                        <span class="logo-mark"><?php include ROOT_PATH . '/assets/inc/logo.php'; ?></span>
                    </a>

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class=""></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mx-auto">
                            <li class="nav-item active">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/index.php?route=accueil"><span data-i18n="accueil.navAccueil">Accueil</span> <span class="sr-only">(current)</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/index.php?route=accueil#menu"><span data-i18n="accueil.navMenuSemaine">Menu de la semaine</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/index.php?route=accueil#about"><span data-i18n="accueil.navAPropos">À propos</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/index.php?route=accueil#contact"><span data-i18n="accueil.navContact">Contact</span></a>
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

<script>
(function () {
    var header = document.querySelector('.header_section');
    if (!header) return;

    function ajusterEspaceHeader() {
        document.body.style.paddingTop = (header.offsetHeight + 16) + 'px';
    }
    ajusterEspaceHeader();
    window.addEventListener('resize', ajusterEspaceHeader);
    window.addEventListener('load', ajusterEspaceHeader);

    var menuMobile = document.getElementById('navbarSupportedContent');
    if (menuMobile) {
        menuMobile.addEventListener('shown.bs.collapse', ajusterEspaceHeader);
        menuMobile.addEventListener('hidden.bs.collapse', ajusterEspaceHeader);
    }

    function majOmbreHeader() {
        header.classList.toggle('is-scrolled', (window.scrollY || 0) > 12);
    }
    majOmbreHeader();
    window.addEventListener('scroll', majOmbreHeader, { passive: true });
})();
</script>
