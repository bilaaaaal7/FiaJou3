<?php
$pageTitle = "Espace cuisinier - " . APP_NAME;
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<h1>Espace cuisinier</h1>

<?php
$quickAccessItems = [
    ['icon' => '👨‍🍳', 'label' => 'Commandes à préparer', 'route' => 'cuisinier'],
    ['icon' => '🕒', 'label' => 'Historique',            'route' => 'cuisinier/historique'],
];
require ROOT_PATH . '/assets/inc/quick_access.php';
?>

<div class="kpi-grid">
    <div class="kpi-card" style="border-left: 4px solid #6b5b3a;">
        <div class="kpi-label">À préparer</div>
        <div class="kpi-value" style="color: #6b5b3a;"><?php echo $nbAPreparer; ?></div>
    </div>
    <div class="kpi-card" style="border-left: 4px solid #9a6c11;">
        <div class="kpi-label">En préparation</div>
        <div class="kpi-value" style="color: #9a6c11;"><?php echo $nbEnPreparation; ?></div>
    </div>
</div>

<?php if (!empty($commandesEnAttente)): ?>
<div class="panel">
    <h2>Commandes en attente</h2>
    <?php foreach ($commandesEnAttente as $c): ?>
    <div style="border:1px solid var(--border); border-radius:10px; padding:16px; margin-bottom:12px; background:#fff;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; margin-bottom:8px;">
            <div>
                <strong>#<?php echo $c['id']; ?></strong> &mdash;
                <?php echo htmlspecialchars($c['prenom'] . ' ' . $c['nom']); ?>
            </div>
            <div>
                Livraison: <?php echo $c['date_livraison'] . ' ' . $c['heure_livraison']; ?>
                &middot; <?php echo number_format($c['total'], 2); ?> DH
            </div>
        </div>
        <?php if (!empty($itemsParCommande[$c['id']])): ?>
        <div style="margin-bottom:8px;">
            <small style="color:#8a8a8a;">Articles:</small>
            <?php foreach ($itemsParCommande[$c['id']] as $item): ?>
                <span style="display:inline-block; background:var(--gold-light); padding:2px 8px; border-radius:6px; font-size:0.8rem; margin:2px;">
                    <?php echo htmlspecialchars($item['plat_nom']); ?> x<?php echo $item['quantite']; ?>
                </span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($c['commentaire'])): ?>
        <div style="margin-bottom:8px;"><small style="color:#666;">Commentaire: <?php echo htmlspecialchars($c['commentaire']); ?></small></div>
        <?php endif; ?>
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=cuisinier" style="display:inline-flex; gap:6px; align-items:center; flex-wrap:wrap;">
            <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
            <input type="hidden" name="nouveau_statut" value="en_preparation">
            <input type="text" name="commentaire" placeholder="Remarque..." style="border:1px solid var(--border); border-radius:6px; padding:4px 8px; font-size:0.82rem; width:160px;">
            <button type="submit" name="avancerStatut" class="btn btn-gold btn-sm">Commencer la préparation</button>
        </form>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (!empty($commandesEnPreparation)): ?>
<div class="panel">
    <h2>En préparation</h2>
    <?php foreach ($commandesEnPreparation as $c): ?>
    <div style="border:1px solid var(--border); border-radius:10px; padding:16px; margin-bottom:12px; background:#fff;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; margin-bottom:8px;">
            <div>
                <strong>#<?php echo $c['id']; ?></strong> &mdash;
                <?php echo htmlspecialchars($c['prenom'] . ' ' . $c['nom']); ?>
            </div>
            <div>
                Livraison: <?php echo $c['date_livraison'] . ' ' . $c['heure_livraison']; ?>
                &middot; <?php echo number_format($c['total'], 2); ?> DH
            </div>
        </div>
        <?php if (!empty($itemsParCommande[$c['id']])): ?>
        <div style="margin-bottom:8px;">
            <small style="color:#8a8a8a;">Articles:</small>
            <?php foreach ($itemsParCommande[$c['id']] as $item): ?>
                <span style="display:inline-block; background:var(--gold-light); padding:2px 8px; border-radius:6px; font-size:0.8rem; margin:2px;">
                    <?php echo htmlspecialchars($item['plat_nom']); ?> x<?php echo $item['quantite']; ?>
                </span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($c['commentaire'])): ?>
        <div style="margin-bottom:8px;"><small style="color:#666;">Commentaire: <?php echo htmlspecialchars($c['commentaire']); ?></small></div>
        <?php endif; ?>
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=cuisinier" style="display:inline-flex; gap:6px; align-items:center; flex-wrap:wrap;">
            <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
            <input type="hidden" name="nouveau_statut" value="prete">
            <input type="text" name="commentaire" placeholder="Remarque..." style="border:1px solid var(--border); border-radius:6px; padding:4px 8px; font-size:0.82rem; width:160px;">
            <button type="submit" name="avancerStatut" class="btn btn-gold btn-sm">Marquer prête</button>
        </form>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (empty($commandesEnAttente) && empty($commandesEnPreparation)): ?>
<div class="panel">
    <div class="empty-state">Aucune commande à préparer pour le moment.</div>
</div>
<?php endif; ?>

<?php if (!empty($quantites)): ?>
<div class="panel">
    <h2>Quantités à produire aujourd'hui</h2>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr><th>Produit</th><th>Catégorie</th><th>Quantité totale</th></tr>
            </thead>
            <tbody>
            <?php foreach ($quantites as $q): ?>
                <tr>
                    <td><?php echo htmlspecialchars($q['nom']); ?></td>
                    <td><?php echo htmlspecialchars($q['categorie']); ?></td>
                    <td><strong><?php echo $q['total_quantite']; ?></strong></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
