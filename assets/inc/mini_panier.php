<?php
/**
 * Panier latéral (Drawer) — nouvelle UX du panier côté client.
 *
 * Au lieu d'une page dédiée, le panier s'ouvre dans un panneau latéral droit,
 * sans quitter la page (Accueil, Menu, …). L'icône Panier de la navbar
 * (accueil) et de la barre supérieure (app-shell) déclenchent l'ouverture.
 *
 * Composant autonome : son CSS/JS sont embarqués (préfixe .fj-cart-*), il
 * fonctionne aussi bien sur la landing page (vue/accueil.php, qui n'utilise
 * pas app.css) que sur les pages de l'espace client (app-shell).
 *
 * Fonctionnalités conservées (logique métier inchangée, même route d'actions) :
 *   - produits, quantités (+/-), suppression, sous-total, total
 *   - vider le panier, passer la commande
 *   - s'ouvre automatiquement quand la page est chargée avec ?panier=1
 *     (après un ajout au panier)
 *
 * Les actions (+/- / supprimer / vider) sont envoyées en AJAX vers la route
 * existante client/panier : la page ne se recharge pas, seule la drawer est
 * réactualisée. Fermeture : bouton X, clic à l'extérieur, touche Échap.
 */

if (!est_connecte() || utilisateur_role() !== ROLE_CLIENT) {
    return;
}

require_once ROOT_PATH . '/modele/PanierModele.php';

$fjcPanier = new PanierModele();
$fjcDetails = $fjcPanier->getDetails();
$fjcArticles = $fjcDetails['articles'];
$fjcTotal = $fjcDetails['total'];
$fjcDate = $fjcPanier->getDate();
$fjcNb = $fjcPanier->nombreArticles();
$fjcOuvert = isset($_GET['panier']) && $_GET['panier'] === '1';

// Route de retour : si on agit depuis la Home (accueil), on revient sur le
// Menu client (la Home n'est pas une route « client/* » acceptée par la
// route d'actions). Les pages de l'espace client gardent leur route courante.
$fjcRetour = preg_replace('/[^a-z0-9\/_-]/i', '', $_GET['route'] ?? 'client');
$fjcRetour = $fjcRetour !== '' ? $fjcRetour : 'client';
if (strpos($fjcRetour, 'client') !== 0) {
    $fjcRetour = 'client';
}

$fjcFormaterPrix = static fn($montant) => number_format((float) $montant, 2, ',', ' ') . ' DH';
?>
<style>
    /* Palette du composant (Light / Dark), préfixe unique --fjc-* */
    :root {
        --fjc-bg: #ffffff;
        --fjc-surface: #f7f5ef;
        --fjc-text: #171717;
        --fjc-muted: #706B62;
        --fjc-border: #e6e0d2;
        --fjc-gold: #B88618;
        --fjc-gold-dark: #7a5810;
        --fjc-gold-soft: #f6eeda;
        --fjc-danger: #c0392b;
        --fjc-danger-soft: #fbe9e6;
        --fjc-overlay: rgba(23, 23, 23, 0.5);
        --fjc-shadow: rgba(23, 23, 23, 0.22);
    }
    html[data-theme="dark"] {
        --fjc-bg: #1c1c22;
        --fjc-surface: #232329;
        --fjc-text: #f2efe8;
        --fjc-muted: #a49d92;
        --fjc-border: #2e2e36;
        --fjc-gold: #e0b14d;
        --fjc-gold-dark: #f0c97c;
        --fjc-gold-soft: #2e2818;
        --fjc-danger: #ff9a8d;
        --fjc-danger-soft: rgba(255, 120, 100, 0.12);
        --fjc-overlay: rgba(0, 0, 0, 0.6);
        --fjc-shadow: rgba(0, 0, 0, 0.5);
    }

    [data-fj-cart-badge][hidden] { display: none !important; }

    /* ---------- Voile d'arrière-plan ---------- */
    .fj-cart-overlay {
        position: fixed;
        inset: 0;
        z-index: 2100;
        background: var(--fjc-overlay);
        opacity: 0;
        visibility: hidden;
        transition: opacity .28s ease, visibility .28s ease;
    }
    .fj-cart-overlay.show { opacity: 1; visibility: visible; }

    /* ---------- Drawer ---------- */
    .fj-cart-drawer {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        width: 440px;
        max-width: 100vw;
        z-index: 2101;
        display: flex;
        flex-direction: column;
        background: var(--fjc-bg);
        color: var(--fjc-text);
        font-family: 'Poppins', 'Open Sans', sans-serif;
        border-left: 1px solid var(--fjc-border);
        box-shadow: -14px 0 36px var(--fjc-shadow);
        transform: translateX(102%);
        visibility: hidden;
        transition: transform .32s cubic-bezier(.22, .61, .36, 1), visibility .32s ease;
    }
    .fj-cart-drawer.open {
        transform: translateX(0);
        visibility: visible;
    }
    .fj-cart-drawer.is-loading { opacity: .7; pointer-events: none; }

    /* ---------- En-tête ---------- */
    .fj-cart-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--fjc-border);
        flex-shrink: 0;
    }
    .fj-cart-head h2 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--fjc-text);
        letter-spacing: .2px;
    }
    .fj-cart-close {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 1px solid var(--fjc-border);
        background: transparent;
        color: var(--fjc-muted);
        font-size: 1.35rem;
        line-height: 1;
        cursor: pointer;
        padding: 0;
        transition: border-color .18s ease, color .18s ease, background .18s ease, transform .18s ease;
    }
    .fj-cart-close:hover {
        border-color: var(--fjc-gold);
        color: var(--fjc-gold-dark);
        background: var(--fjc-gold-soft);
        transform: rotate(90deg);
    }
    .fj-cart-close:focus-visible { outline: 2px solid var(--fjc-gold); outline-offset: 2px; }

    /* ---------- Date de livraison ---------- */
    .fj-cart-date {
        padding: 10px 20px;
        font-size: .8rem;
        color: var(--fjc-muted);
        background: var(--fjc-surface);
        border-bottom: 1px solid var(--fjc-border);
        flex-shrink: 0;
    }

    /* ---------- Corps (scrollable) ---------- */
    .fj-cart-body {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        padding: 6px 20px;
    }
    .fj-cart-body::-webkit-scrollbar { width: 8px; }
    .fj-cart-body::-webkit-scrollbar-thumb { background: var(--fjc-border); border-radius: 999px; }

    .fj-cart-item {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        padding: 14px 0;
        border-bottom: 1px dashed var(--fjc-border);
    }
    .fj-cart-item img {
        width: 56px;
        height: 56px;
        object-fit: cover;
        border-radius: 10px;
        flex-shrink: 0;
        background: var(--fjc-surface);
    }
    .fj-cart-item-main { flex: 1; min-width: 0; }
    .fj-cart-item-nom {
        font-weight: 600;
        font-size: .92rem;
        line-height: 1.3;
        color: var(--fjc-text);
    }
    .fj-cart-item-prix { font-size: .8rem; color: var(--fjc-muted); margin-top: 2px; }
    .fj-cart-item-ctrl {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
    }
    .fj-cart-item-ctrl a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 27px;
        height: 27px;
        border: 1px solid var(--fjc-border);
        border-radius: 7px;
        background: var(--fjc-bg);
        color: var(--fjc-text);
        font-size: 1rem;
        font-weight: 700;
        line-height: 1;
        text-decoration: none;
        user-select: none;
        transition: border-color .15s ease, color .15s ease, background .15s ease, transform .15s ease;
    }
    .fj-cart-item-ctrl a:hover {
        border-color: var(--fjc-gold);
        color: var(--fjc-gold-dark);
        background: var(--fjc-gold-soft);
    }
    .fj-cart-item-ctrl a:active { transform: scale(.92); }
    .fj-cart-item-ctrl a.is-busy { opacity: .45; pointer-events: none; }
    .fj-cart-item-qty {
        min-width: 22px;
        text-align: center;
        font-weight: 700;
        font-size: .9rem;
        color: var(--fjc-text);
    }
    .fj-cart-item-del {
        border-color: var(--fjc-danger) !important;
        color: var(--fjc-danger) !important;
        background: var(--fjc-danger-soft) !important;
    }
    .fj-cart-item-del:hover { filter: brightness(.96); }
    .fj-cart-item-subtotal {
        font-weight: 700;
        font-size: .85rem;
        white-space: nowrap;
        color: var(--fjc-gold-dark);
        padding-top: 2px;
    }

    .fj-cart-empty {
        text-align: center;
        padding: 48px 12px;
        color: var(--fjc-muted);
    }
    .fj-cart-empty-icon {
        font-size: 2.2rem;
        display: block;
        margin-bottom: 10px;
        color: var(--fjc-gold);
    }
    .fj-cart-empty p { margin: 0 0 16px; font-size: .95rem; }
    .fj-cart-empty-cta {
        display: inline-block;
        padding: 10px 24px;
        border-radius: 9px;
        background: var(--fjc-gold);
        color: #fff;
        font-weight: 600;
        font-size: .88rem;
        text-decoration: none;
        transition: background .18s ease, transform .18s ease;
    }
    .fj-cart-empty-cta:hover { background: var(--fjc-gold-dark); color: #fff; transform: translateY(-1px); }

    /* ---------- Pied (totaux + actions) ---------- */
    .fj-cart-foot {
        flex-shrink: 0;
        padding: 16px 20px 18px;
        background: var(--fjc-surface);
        border-top: 1px solid var(--fjc-border);
    }
    .fj-cart-sous-total,
    .fj-cart-total {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 12px;
    }
    .fj-cart-sous-total { font-size: .9rem; color: var(--fjc-muted); margin-bottom: 6px; }
    .fj-cart-total { font-weight: 700; font-size: 1.12rem; color: var(--fjc-text); margin-bottom: 14px; }
    .fj-cart-actions { display: flex; flex-direction: column; gap: 8px; }
    .fj-cart-actions a,
    .fj-cart-actions button {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 12px 14px;
        border-radius: 9px;
        font-family: 'Poppins', 'Open Sans', sans-serif;
        font-size: .9rem;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: background .18s ease, border-color .18s ease, color .18s ease, transform .18s ease;
    }
    .fj-cart-actions a.is-busy, .fj-cart-actions button.is-busy { opacity: .55; pointer-events: none; }
    .fj-cart-vider {
        background: transparent;
        border: 1px solid var(--fjc-danger);
        color: var(--fjc-danger);
    }
    .fj-cart-vider:hover { background: var(--fjc-danger-soft); }
    .fj-cart-cta {
        background: var(--fjc-gold);
        border: 1px solid var(--fjc-gold);
        color: #fff;
    }
    .fj-cart-cta:hover { background: var(--fjc-gold-dark); border-color: var(--fjc-gold-dark); color: #fff; transform: translateY(-1px); }
    .fj-cart-keep {
        display: block;
        width: 100%;
        margin-top: 10px;
        background: none;
        border: none;
        color: var(--fjc-muted);
        font-size: .82rem;
        text-decoration: underline;
        cursor: pointer;
        padding: 4px;
    }
    .fj-cart-keep:hover { color: var(--fjc-gold-dark); }

    /* ---------- Responsive ---------- */
    @media (max-width: 575.98px) {
        .fj-cart-drawer { width: 100vw; }
        .fj-cart-head { padding: 16px 16px; }
        .fj-cart-body { padding: 6px 16px; }
        .fj-cart-foot { padding: 14px 16px 16px; }
    }

    /* Bouton nav en mode button (app-shell) : annule les styles par défaut */
    button.topheader-notif { padding: 0; cursor: pointer; }

    /* ---------- RTL (Arabic) ---------- */
    html[dir="rtl"] .fj-cart-drawer {
        right: auto;
        left: 0;
        border-left: 0;
        border-right: 1px solid var(--fjc-border);
        box-shadow: 14px 0 36px var(--fjc-shadow);
        transform: translateX(-102%);
    }
    html[dir="rtl"] .fj-cart-drawer.open {
        transform: translateX(0);
    }
</style>

<div id="fj-cart-overlay" class="fj-cart-overlay"></div>

<aside id="fj-cart-drawer" class="fj-cart-drawer<?php echo $fjcOuvert ? ' open' : ''; ?>"
       role="dialog" aria-modal="true" aria-label="Mon panier" data-i18n-aria="panier.titre"<?php echo $fjcOuvert ? '' : ' aria-hidden="true"'; ?>>
    <div class="fj-cart-head">
        <h2 data-i18n="panier.titre">Mon panier</h2>
        <button type="button" class="fj-cart-close" data-fj-cart-close aria-label="Fermer le panier" data-i18n-aria="common.fermer">&times;</button>
    </div>

    <?php if ($fjcDate): ?>
        <div class="fj-cart-date">
            <span data-i18n="common.livraisonPrevueLe">Livraison prévue le</span> <strong><?php echo htmlspecialchars(date('d/m/Y', strtotime($fjcDate))); ?></strong>
        </div>
    <?php endif; ?>

    <div class="fj-cart-body" id="fj-cart-body">
        <?php if (empty($fjcArticles)): ?>
            <div class="fj-cart-empty">
                <span class="fj-cart-empty-icon" aria-hidden="true">&#128722;</span>
                <p data-i18n="panier.vide">Votre panier est vide.</p>
                <a class="fj-cart-empty-cta" href="<?php echo BASE_URL; ?>/index.php?route=client" data-i18n="common.consulterMenu">Consulter le menu</a>
            </div>
        <?php else: ?>
            <?php foreach ($fjcArticles as $fjcArticle): ?>
                <div class="fj-cart-item">
                    <img src="<?php echo UPLOADS_URL; ?>/<?php echo htmlspecialchars($fjcArticle['image']); ?>"
                         alt="<?php echo htmlspecialchars(localiser($fjcArticle, 'nom')); ?>">
                    <div class="fj-cart-item-main">
                        <div class="fj-cart-item-nom"><?php echo htmlspecialchars(localiser($fjcArticle, 'nom')); ?></div>
                        <div class="fj-cart-item-prix"><?php echo $fjcFormaterPrix($fjcArticle['prix']); ?> <span data-i18n="common.unite">/ unité</span></div>
                        <div class="fj-cart-item-ctrl">
                            <a href="<?php echo BASE_URL; ?>/index.php?route=client/panier&moins=<?php echo (int) $fjcArticle['id']; ?>&retour=<?php echo urlencode($fjcRetour); ?>"
                               data-fj-cart-action aria-label="Retirer une quantité" title="Retirer une quantité" data-i18n-aria="panier.retirerQuantite">&minus;</a>
                            <span class="fj-cart-item-qty"><?php echo (int) $fjcArticle['quantite']; ?></span>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=client/panier&plus=<?php echo (int) $fjcArticle['id']; ?>&retour=<?php echo urlencode($fjcRetour); ?>"
                               data-fj-cart-action aria-label="Ajouter une quantité" title="Ajouter une quantité" data-i18n-aria="panier.ajouterQuantite">+</a>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=client/panier&supprimer=<?php echo (int) $fjcArticle['id']; ?>&retour=<?php echo urlencode($fjcRetour); ?>"
                               data-fj-cart-action class="fj-cart-item-del" aria-label="Supprimer cet article" title="Supprimer" data-i18n-aria="common.supprimer">&times;</a>
                        </div>
                    </div>
                    <div class="fj-cart-item-subtotal"><?php echo $fjcFormaterPrix($fjcArticle['sous_total']); ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="fj-cart-foot" id="fj-cart-foot">
        <?php if (empty($fjcArticles)): ?>
            <button type="button" class="fj-cart-keep" data-fj-cart-close data-i18n="common.continuerAchats">Continuer mes achats</button>
        <?php else: ?>
            <div class="fj-cart-sous-total">
                <span data-i18n="common.sousTotal">Sous-total</span>
                <span><?php echo $fjcFormaterPrix($fjcTotal); ?></span>
            </div>
            <div class="fj-cart-total">
                <span data-i18n="common.total">Total</span>
                <span><?php echo $fjcFormaterPrix($fjcTotal); ?></span>
            </div>
            <div class="fj-cart-actions">
                <a href="<?php echo BASE_URL; ?>/index.php?route=client/panier&vider=1&retour=<?php echo urlencode($fjcRetour); ?>"
                   data-fj-cart-action data-fj-cart-confirm="Voulez-vous vraiment vider le panier ?"
                   class="fj-cart-vider" title="Vider le panier" data-i18n="common.viderPanier">Vider le panier</a>
                <a href="<?php echo BASE_URL; ?>/index.php?route=client/commander" class="fj-cart-cta" data-i18n="common.passerCommande">Passer la commande</a>
            </div>
            <button type="button" class="fj-cart-keep" data-fj-cart-close data-i18n="common.continuerAchats">Continuer mes achats</button>
        <?php endif; ?>
    </div>
</aside>

<span id="fj-cart-count" hidden><?php echo (int) $fjcNb; ?></span>

<script>
(function () {
    if (window.fjCartInit) { return; }
    window.fjCartInit = true;

    function drawerEl() { return document.getElementById('fj-cart-drawer'); }
    function overlayEl() { return document.getElementById('fj-cart-overlay'); }
    function bodyEl() { return document.getElementById('fj-cart-body'); }
    function footEl() { return document.getElementById('fj-cart-foot'); }
    function countEl() { return document.getElementById('fj-cart-count'); }

    var lock = false;
    var prevOverflow = '';

    window.fjCartOuvrir = function () {
        var d = drawerEl();
        if (!d) { return; }
        d.classList.add('open');
        d.setAttribute('aria-hidden', 'false');
        var o = overlayEl();
        if (o) { o.classList.add('show'); }
        if (!lock) {
            prevOverflow = document.body.style.overflow;
            document.body.style.overflow = 'hidden';
            lock = true;
        }
    };

    window.fjCartFermer = function () {
        var d = drawerEl();
        if (!d) { return; }
        d.classList.remove('open');
        d.setAttribute('aria-hidden', 'true');
        var o = overlayEl();
        if (o) { o.classList.remove('show'); }
        if (lock) {
            document.body.style.overflow = prevOverflow;
            lock = false;
        }
    };

    window.fjCartSyncBadges = function () {
        var c = countEl();
        var n = c ? (parseInt(c.textContent, 10) || 0) : 0;
        var badges = document.querySelectorAll('[data-fj-cart-badge]');
        for (var i = 0; i < badges.length; i++) {
            if (n > 0) {
                badges[i].textContent = n > 9 ? '9+' : String(n);
                badges[i].removeAttribute('hidden');
            } else {
                badges[i].setAttribute('hidden', '');
            }
        }
    };

    document.addEventListener('click', function (e) {
        // Fermeture : bouton X, « Continuer mes achats », voile
        if (e.target.closest('[data-fj-cart-close]') || e.target.closest('#fj-cart-overlay')) {
            window.fjCartFermer();
            return;
        }

        // Actions du panier (plus / moins / supprimer / vider) : AJAX sans rechargement
        var action = e.target.closest('[data-fj-cart-action]');
        if (!action) { return; }
        e.preventDefault();

        if (action.hasAttribute('data-fj-cart-confirm')) {
            var message = action.getAttribute('data-fj-cart-confirm');
            if (message && !window.confirm(message)) { return; }
        }

        var url = action.getAttribute('href');
        if (!url) { return; }
        if (action.classList.contains('is-busy')) { return; }
        action.classList.add('is-busy');
        var d = drawerEl();
        if (d) { d.classList.add('is-loading'); }

        fetch(url, { credentials: 'same-origin' })
            .then(function (resp) {
                if (!resp.ok) { throw new Error('HTTP ' + resp.status); }
                return resp.text();
            })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var nb = doc.getElementById('fj-cart-body');
                var nf = doc.getElementById('fj-cart-foot');
                var nc = doc.getElementById('fj-cart-count');
                if (!nb || !nf || !bodyEl() || !footEl()) {
                    window.location.href = url;
                    return;
                }
                bodyEl().innerHTML = nb.innerHTML;
                footEl().innerHTML = nf.innerHTML;
                if (countEl() && nc) { countEl().textContent = nc.textContent; }
                window.fjCartSyncBadges();
                if (window.fjI18nAppliquer) { window.fjI18nAppliquer(); }
            })
            .catch(function () {
                window.location.href = url;
            })
            .finally(function () {
                action.classList.remove('is-busy');
                if (d) { d.classList.remove('is-loading'); }
            });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            window.fjCartFermer();
        }
    });

    // Ouverture automatique quand la page arrive avec ?panier=1 (après ajout)
    var d = drawerEl();
    if (d && d.classList.contains('open')) {
        window.fjCartOuvrir();
    }
    window.fjCartSyncBadges();
})();
</script>
