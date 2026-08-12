// to get current year
function getYear() {
    var currentDate = new Date();
    var currentYear = currentDate.getFullYear();
    var el = document.querySelector("#displayYear");
    if (el) el.innerHTML = currentYear;
}
getYear();

// isotope js (filtre par jour dans le menu de la semaine)
$(window).on('load', function () {
    var $grid = $(".grid").isotope({
        itemSelector: ".all",
        percentPosition: false,
        masonry: {
            columnWidth: ".all"
        }
    });

    $('.filters_menu li').click(function () {
        $('.filters_menu li').removeClass('active');
        $(this).addClass('active');

        var data = $(this).attr('data-filter');
        $grid.isotope({ filter: data });
    });
});

// client section owl carousel
(function () {
    var $carousel = $(".client_owl-carousel");
    if (!$carousel.length) return;

    // Copie « propre » du balisage d'origine des témoignages (avant toute
    // manipulation par Owl Carousel : pas de classes owl-*, pas d'éléments
    // clonés pour le mode loop, pas de largeurs/transform calculés). On s'en
    // sert pour repartir d'un état sain à chaque changement de sens.
    //
    // Pourquoi : owlCarousel('destroy') est censé restaurer le balisage
    // d'origine, mais avec loop:true les éléments clonés et les styles
    // inline (largeur, marges, transform) qu'Owl calcule pour un sens ne
    // sont pas toujours proprement nettoyés avant la ré-initialisation dans
    // l'autre sens. Résultat observé en arabe : les flèches de navigation
    // s'affichent (elles sont recréées par l'init) mais les cartes elles-
    // mêmes restent invisibles, car positionnées avec des valeurs calculées
    // pour l'ancien sens. Repartir systématiquement du balisage d'origine
    // élimine ce risque, quel que soit l'ordre des changements de langue.
    var balisageOrigine = $carousel.html();

    // La direction est portée par <html dir="..."> (renseigné côté serveur et
    // mis à jour dynamiquement par i18n.js lors d'un changement de langue).
    function estRTL() {
        return document.documentElement.getAttribute('dir') === 'rtl';
    }

    function initCarousel() {
        $carousel.owlCarousel({
            rtl: estRTL(),
            loop: true,
            margin: 0,
            dots: false,
            nav: true,
            navText: [
                '<i class="fa fa-angle-left" aria-hidden="true"></i>',
                '<i class="fa fa-angle-right" aria-hidden="true"></i>'
            ],
            autoplay: true,
            autoplayHoverPause: true,
            responsive: {
                0: { items: 1 },
                768: { items: 2 },
                1000: { items: 2 }
            }
        });
    }

    function redemarrerCarousel() {
        if ($carousel.data('owl.carousel')) {
            $carousel.owlCarousel('destroy');
        }

        // Repart toujours du balisage d'origine plutôt que de faire
        // confiance à la restauration d'Owl : garantit qu'aucune carte
        // clonée, largeur ou transform calculé pour l'ancien sens ne
        // subsiste et ne masque les témoignages.
        $carousel
            .removeClass('owl-loaded owl-loading owl-drag owl-grab owl-rtl owl-hidden owl-text-select-on')
            .removeAttr('style')
            .html(balisageOrigine);

        // Le balisage restauré est celui d'origine (langue de rendu serveur) :
        // sans ce ré-appel, les témoignages redeviennent français après un
        // changement de langue, alors même que le reste de la page reste
        // dans la langue choisie.
        if (window.fjI18nAppliquer) {
            window.fjI18nAppliquer();
        }

        initCarousel();
    }

    initCarousel();

    // Changement de langue (i18n.js) : bascule dynamique LTR <-> RTL.
    // On redémarre le carrousel pour que direction et sens de défilement
    // restent cohérents (et que les flèches conservent leur rôle), sans
    // nécessiter de rechargement de la page.
    var derniereRtl = estRTL();
    if ('MutationObserver' in window) {
        var observateur = new MutationObserver(function () {
            var rtl = estRTL();
            if (rtl !== derniereRtl) {
                derniereRtl = rtl;
                redemarrerCarousel();
            }
        });
        observateur.observe(document.documentElement, { attributes: true, attributeFilter: ['dir'] });
    }
})();
