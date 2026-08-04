<?php
/**
 * Mini-panier latéral (cahier des charges : panier visible dès l'ajout).
 * Rendu pour les clients uniquement ; s'ouvre automatiquement quand la page
 * est chargée avec ?panier=1 (après un ajout au panier).
 */

if (!est_connecte() || utilisateur_role() !== ROLE_CLIENT) {
    return;
}

require_once ROOT_PATH . '/modele/PanierModele.php';

$mpPanier = new PanierModele();
$mpDetails = $mpPanier->getDetails();
$mpArticles = $mpDetails['articles'];
$mpTotal = $mpDetails['total'];
$mpDate = $mpPanier->getDate();
$mpNb = $mpPanier->nombreArticles();
$mpOuvert = isset($_GET['panier']) && $_GET['panier'] === '1';
$mpRetour = preg_replace('/[^a-z0-9\/_-]/i', '', $_GET['route'] ?? 'client');
$mpRetour = $mpRetour !== '' ? $mpRetour : 'client';
if (strpos($mpRetour, 'client') !== 0) {
    $mpRetour = 'client';
}
?>
<style>
    .mp-toggle {
        position: fixed; right: 18px; bottom: 18px; z-index: 2050;
        background: var(--gold, #B88618); color: #fff; border: none; border-radius: 999px;
        padding: 12px 18px; font-weight: 700; box-shadow: 0 8px 22px rgba(0,0,0,0.28);
        cursor: pointer; display: inline-flex; align-items: center; gap: 8px; font-size: 0.9rem;
    }
    .mp-drawer {
        position: fixed; top: 0; right: 0; bottom: 0; width: 340px; max-width: 88vw;
        background: var(--surface); color: var(--text); z-index: 2100;
        transform: translateX(102%); transition: transform 0.28s ease;
        display: flex; flex-direction: column; box-shadow: -12px 0 30px rgba(0,0,0,0.22);
    }
    .mp-drawer.open { transform: translateX(0); }
    .mp-head {
        padding: 16px 18px; background: var(--dark); color: var(--cream);
        display: flex; align-items: center; justify-content: space-between; font-weight: 700;
    }
    .mp-close {
        background: none; border: none; color: var(--cream); font-size: 1.5rem; line-height: 1; cursor: pointer;
    }
    .mp-date {
        padding: 10px 18px; font-size: 0.8rem; color: var(--text-muted);
        border-bottom: 1px solid var(--border-soft); background: var(--surface);
    }
    .mp-body { flex: 1; overflow-y: auto; padding: 8px 18px; }
    .mp-item {
        display: flex; gap: 10px; align-items: center;
        padding: 10px 0; border-bottom: 1px dashed var(--border-soft); font-size: 0.9rem;
    }
    .mp-item img {
        width: 46px; height: 46px; object-fit: cover; border-radius: 8px; flex-shrink: 0;
    }
    .mp-item .mp-info { flex: 1; min-width: 0; }
    .mp-item .mp-nom { font-weight: 600; line-height: 1.25; }
    .mp-item .mp-prix { color: var(--text-muted); font-size: 0.8rem; }
    .mp-item .mp-ctrl {
        display: inline-flex; align-items: center; gap: 2px; margin-top: 4px;
    }
    .mp-item .mp-ctrl a {
        display: inline-flex; align-items: center; justify-content: center;
        width: 22px; height: 22px; border-radius: 6px; text-decoration: none;
        font-weight: 700; font-size: 0.85rem; line-height: 1;
        border: 1px solid var(--gold); color: var(--gold-dark); background: var(--surface);
    }
    .mp-item .mp-ctrl a.mp-del { border-color: var(--danger); color: var(--danger); }
    .mp-item .mp-qty { padding: 0 6px; font-weight: 700; color: var(--text); }
    .mp-empty { color: var(--text-muted); text-align: center; padding: 40px 10px; }
    .mp-foot {
        padding: 14px 18px; background: var(--surface); border-top: 1px solid var(--border-soft);
    }
    .mp-total { display: flex; justify-content: space-between; font-weight: 700; font-size: 1.05rem; margin-bottom: 12px; }
    .mp-actions { display: flex; gap: 8px; }
    .mp-actions a {
        flex: 1; text-align: center; padding: 10px 8px; border-radius: 8px;
        font-weight: 600; text-decoration: none; font-size: 0.85rem;
    }
    .mp-actions .mp-link { border: 1px solid var(--gold); color: var(--gold-dark); }
    .mp-actions .mp-cta { background: var(--gold); color: #fff; }
    .mp-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 2050;
        opacity: 0; pointer-events: none; transition: opacity 0.28s ease;
    }
    .mp-overlay.show { opacity: 1; pointer-events: auto; }
</style>

<button type="button" id="mp-toggle" class="mp-toggle" onclick="mpOuvrir()">
    Panier (<?php echo (int) $mpNb; ?>)
</button>

<div id="mp-overlay" class="mp-overlay" onclick="mpFermer()"></div>

<aside id="mp-drawer" class="mp-drawer<?php echo $mpOuvert ? ' open' : ''; ?>" aria-label="Mini panier">
    <div class="mp-head">
        <span>Mon panier</span>
        <button type="button" class="mp-close" onclick="mpFermer()" aria-label="Fermer">&times;</button>
    </div>

    <?php if ($mpDate): ?>
        <div class="mp-date">Livraison le <?php echo htmlspecialchars(date('d/m/Y', strtotime($mpDate))); ?></div>
    <?php endif; ?>

    <div class="mp-body">
        <?php if (empty($mpArticles)): ?>
            <div class="mp-empty">Votre panier est vide.</div>
        <?php else: ?>
            <?php foreach ($mpArticles as $mpArticle): ?>
                <div class="mp-item">
                    <img src="<?php echo UPLOADS_URL; ?>/<?php echo htmlspecialchars($mpArticle['image']); ?>" alt="<?php echo htmlspecialchars($mpArticle['nom']); ?>">
                    <div class="mp-info">
                        <div class="mp-nom"><?php echo htmlspecialchars($mpArticle['nom']); ?></div>
                        <div class="mp-prix"><?php echo number_format((float) $mpArticle['prix'], 2, ',', ' '); ?> DH</div>
                        <div class="mp-ctrl">
                            <a href="<?php echo BASE_URL; ?>/index.php?route=client/panier&moins=<?php echo (int) $mpArticle['id']; ?>&retour=<?php echo urlencode($mpRetour); ?>" title="Retirer une quantité">&minus;</a>
                            <span class="mp-qty"><?php echo (int) $mpArticle['quantite']; ?></span>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=client/panier&plus=<?php echo (int) $mpArticle['id']; ?>&retour=<?php echo urlencode($mpRetour); ?>" title="Ajouter une quantité">+</a>
                            <a class="mp-del" href="<?php echo BASE_URL; ?>/index.php?route=client/panier&supprimer=<?php echo (int) $mpArticle['id']; ?>&retour=<?php echo urlencode($mpRetour); ?>" title="Supprimer">&times;</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="mp-foot">
        <div class="mp-total">
            <span>Total</span>
            <span><?php echo number_format((float) $mpTotal, 2, ',', ' '); ?> DH</span>
        </div>
        <div class="mp-actions" style="margin-bottom:8px;">
            <a class="mp-link" href="<?php echo BASE_URL; ?>/index.php?route=client/panier">Voir le panier</a>
            <?php if (!empty($mpArticles)): ?>
                <a class="mp-cta" href="<?php echo BASE_URL; ?>/index.php?route=client/commander">Valider la commande</a>
            <?php endif; ?>
        </div>
        <button type="button" onclick="mpFermer()" style="width:100%; background:none; border:none; color:var(--text-muted); font-size:0.85rem; text-decoration:underline; cursor:pointer; padding:4px;">
            Continuer mes achats
        </button>
    </div>
</aside>

<script>
    function mpOuvrir() {
        document.getElementById('mp-drawer').classList.add('open');
        document.getElementById('mp-overlay').classList.add('show');
    }
    function mpFermer() {
        document.getElementById('mp-drawer').classList.remove('open');
        document.getElementById('mp-overlay').classList.remove('show');
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { mpFermer(); }
    });
</script>
