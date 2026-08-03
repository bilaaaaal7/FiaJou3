<?php
$pageTitle = "Espace livreur - " . APP_NAME;
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<h1>Espace livreur</h1>

<?php
$quickAccessItems = [
    ['icon' => '🛵', 'label' => 'Livraisons du jour', 'route' => 'livreur'],
    ['icon' => '🕒', 'label' => 'Historique',         'route' => 'livreur/historique'],
];
require ROOT_PATH . '/assets/inc/quick_access.php';
?>

<div class="kpi-grid">
    <div class="kpi-card" style="border-left: 4px solid #1d4e8f;">
        <div class="kpi-label">À livrer</div>
        <div class="kpi-value" style="color: #1d4e8f;"><?php echo $nbAPretee; ?></div>
    </div>
    <div class="kpi-card" style="border-left: 4px solid #5732a6;">
        <div class="kpi-label">En livraison</div>
        <div class="kpi-value" style="color: #5732a6;"><?php echo $nbEnLivraison; ?></div>
    </div>
    <div class="kpi-card" style="border-left: 4px solid #226b2e;">
        <div class="kpi-label">Livrées aujourd'hui</div>
        <div class="kpi-value" style="color: #226b2e;"><?php echo $nbLivrees; ?></div>
    </div>
</div>

<?php if (!empty($commandesAPretee)): ?>
<div class="panel">
    <h2>Commandes prêtes à livrer</h2>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr><th>ID</th><th>Client</th><th>Téléphone</th><th>Articles</th><th>Zone</th><th>Livraison</th><th>Total</th><th>Action</th></tr>
            </thead>
            <tbody>
            <?php foreach ($commandesAPretee as $c): ?>
                <tr>
                    <td><?php echo $c['id']; ?></td>
                    <td><?php echo htmlspecialchars($c['prenom'] . ' ' . $c['nom']); ?></td>
                    <td>
                        <?php if (!empty($c['telephone'])): ?>
                        <a href="tel:<?php echo htmlspecialchars($c['telephone']); ?>" style="color:var(--gold-dark); text-decoration:none;"><?php echo htmlspecialchars($c['telephone']); ?></a>
                        <?php else: ?>-<?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($itemsParCommande[$c['id']])): ?>
                        <?php foreach ($itemsParCommande[$c['id']] as $item): ?>
                            <span style="display:inline-block; background:var(--gold-light); padding:2px 6px; border-radius:6px; font-size:0.75rem; margin:1px;">
                                <?php echo htmlspecialchars($item['plat_nom']); ?> x<?php echo $item['quantite']; ?>
                            </span>
                        <?php endforeach; ?>
                        <?php else: ?>-<?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($c['zone_nom'] ?? '-'); ?></td>
                    <td><?php echo $c['date_livraison'] . ' ' . $c['heure_livraison']; ?></td>
                    <td><?php echo number_format($c['total'], 2); ?> DH</td>
                    <td>
                        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=livreur" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                            <button type="submit" name="demarrerLivraison" class="btn btn-gold btn-sm">Démarrer</button>
                        </form>
                        <a href="<?php echo BASE_URL; ?>/index.php?route=livreur/detail-commande&id=<?php echo (int) $c['id']; ?>" class="btn btn-outline btn-sm">Voir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($commandesEnLivraison)): ?>
<div class="panel">
    <h2>En livraison</h2>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr><th>ID</th><th>Client</th><th>Téléphone</th><th>Articles</th><th>Zone</th><th>Livraison</th><th>Total</th><th>Priorité</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($commandesEnLivraison as $c): ?>
                <tr>
                    <td><?php echo $c['id']; ?></td>
                    <td><?php echo htmlspecialchars($c['prenom'] . ' ' . $c['nom']); ?></td>
                    <td>
                        <?php if (!empty($c['telephone'])): ?>
                        <a href="tel:<?php echo htmlspecialchars($c['telephone']); ?>" style="color:var(--gold-dark); text-decoration:none;"><?php echo htmlspecialchars($c['telephone']); ?></a>
                        <?php else: ?>-<?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($itemsParCommande[$c['id']])): ?>
                        <?php foreach ($itemsParCommande[$c['id']] as $item): ?>
                            <span style="display:inline-block; background:var(--gold-light); padding:2px 6px; border-radius:6px; font-size:0.75rem; margin:1px;">
                                <?php echo htmlspecialchars($item['plat_nom']); ?> x<?php echo $item['quantite']; ?>
                            </span>
                        <?php endforeach; ?>
                        <?php else: ?>-<?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($c['zone_nom'] ?? '-'); ?></td>
                    <td><?php echo $c['date_livraison'] . ' ' . $c['heure_livraison']; ?></td>
                    <td><?php echo number_format($c['total'], 2); ?> DH</td>
                    <td><?php echo $c['priority'] ? '<span class="badge-status st-en_attente">Urgent</span>' : '-'; ?></td>
                    <td class="actions-cell" style="flex-direction:column; gap:4px;">
                        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=livreur" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                            <input type="text" name="commentaire" placeholder="Remarque..." style="border:1px solid var(--border); border-radius:6px; padding:4px 8px; font-size:0.82rem; width:120px;">
                            <button type="submit" name="confirmerLivraison" class="btn btn-gold btn-sm">Confirmer livraison</button>
                        </form>
                        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=livreur" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                            <input type="text" name="commentaire_probleme" placeholder="Décrire le problème..." style="border:1px solid #f1c3bd; border-radius:6px; padding:4px 8px; font-size:0.82rem; width:180px;">
                            <button type="submit" name="signalerProbleme" class="btn btn-danger btn-sm">Signaler problème</button>
                        </form>
                        <a href="<?php echo BASE_URL; ?>/index.php?route=livreur/detail-commande&id=<?php echo (int) $c['id']; ?>" class="btn btn-outline btn-sm">Voir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if (empty($commandesAPretee) && empty($commandesEnLivraison)): ?>
<div class="panel">
    <div class="empty-state">Aucune livraison en cours.</div>
</div>
<?php endif; ?>

<?php if (!empty($commandesLivreesAujourdHui)): ?>
<div class="panel">
    <h2>Livrées aujourd'hui</h2>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr><th>ID</th><th>Client</th><th>Articles</th><th>Heure</th><th>Total</th><th>Zone</th><th></th></tr>
            </thead>
            <tbody>
            <?php foreach ($commandesLivreesAujourdHui as $c): ?>
                <tr>
                    <td><?php echo $c['id']; ?></td>
                    <td><?php echo htmlspecialchars($c['prenom'] . ' ' . $c['nom']); ?></td>
                    <td>
                        <?php
                        if (!empty($itemsLivrees[$c['id']])):
                            foreach ($itemsLivrees[$c['id']] as $item):
                        ?>
                            <span style="display:inline-block; background:var(--gold-light); padding:2px 6px; border-radius:6px; font-size:0.75rem; margin:1px;">
                                <?php echo htmlspecialchars($item['plat_nom']); ?> x<?php echo $item['quantite']; ?>
                            </span>
                        <?php
                            endforeach;
                        else: ?>-<?php endif; ?>
                    </td>
                    <td><?php echo $c['heure_livraison']; ?></td>
                    <td><?php echo number_format($c['total'], 2); ?> DH</td>
                    <td><?php echo htmlspecialchars($c['zone_nom'] ?? '-'); ?></td>
                    <td><a href="<?php echo BASE_URL; ?>/index.php?route=livreur/detail-commande&id=<?php echo (int) $c['id']; ?>" class="btn btn-outline btn-sm">Voir</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
