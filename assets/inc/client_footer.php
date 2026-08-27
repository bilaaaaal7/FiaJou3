<?php
/**
 * Pied de page client (public) — même design que la page d'accueil.
 * Inclut le footer section, le mini-panier, et tous les scripts partagés.
 *
 * Ce fichier remplace footer.php pour les pages client internes qui utilisent
 * la navbar horizontale (client_navbar.php) au lieu de la sidebar.
 */
?>

<!-- footer section -->
<footer class="footer_section" id="contact">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>
<script src="<?php echo BASE_URL; ?>/assets/js/theme.js"></script>
<script src="<?php echo BASE_URL; ?>/assets/js/profile-menu.js"></script>
<script src="<?php echo BASE_URL; ?>/assets/js/i18n.js?<?php echo (int) @filemtime(ROOT_PATH . '/assets/js/i18n.js'); ?>"></script>
<script src="<?php echo BASE_URL; ?>/assets/js/honeypot.js?v=<?php echo (int) @filemtime(ROOT_PATH . '/assets/js/honeypot.js'); ?>"></script>
<script>if (window.lucide) { lucide.createIcons(); }</script>
<script>
    document.getElementById('displayYear') && (document.getElementById('displayYear').textContent = new Date().getFullYear());
</script>
</body>
</html>
