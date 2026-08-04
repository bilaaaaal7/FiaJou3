<?php
$pageTitle = "Historique cuisinier - " . APP_NAME;
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<h1>Historique de production</h1>

<div class="panel">
    <h2>Mon activité récente</h2>
    <?php if (empty($activite)): ?>
        <div class="empty-state">Aucune activité enregistrée pour le moment.</div>
    <?php else: ?>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Commande</th>
                    <th>Client</th>
                    <th>Action</th>
                    <th>Remarque</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($activite as $a): ?>
                <tr>
                    <td><?php echo date('d/m/Y H:i', strtotime($a['date_modification'])); ?></td>
                    <td><a href="<?php echo BASE_URL; ?>/index.php?route=cuisinier/detail-commande&id=<?php echo (int) $a['order_id']; ?>">#<?php echo (int) $a['order_id']; ?></a></td>
                    <td><?php echo htmlspecialchars($a['client_prenom'] . ' ' . $a['client_nom']); ?></td>
                    <td>
                        <?php if ($a['ancien_statut'] === $a['nouveau_statut']): ?>
                            <span style="color:var(--text-muted);">Remarque ajoutée</span>
                        <?php else: ?>
                            <span class="badge-status st-<?php echo htmlspecialchars($a['nouveau_statut']); ?>">
                                <?php echo STATUTS_COMMANDE[$a['nouveau_statut']] ?? $a['nouveau_statut']; ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $a['commentaire'] ? htmlspecialchars($a['commentaire']) : '—'; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<div class="panel">
    <h2>Commandes terminées</h2>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Date livraison</th>
                    <th>Heure</th>
                    <th>Total</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($commandesLivrees as $c): ?>
                <tr>
                    <td><?php echo $c['id']; ?></td>
                    <td><?php echo htmlspecialchars($c['prenom'] . ' ' . $c['nom']); ?></td>
                    <td><?php echo $c['date_livraison']; ?></td>
                    <td><?php echo $c['heure_livraison']; ?></td>
                    <td><?php echo number_format($c['total'], 2); ?> DH</td>
                    <td><span class="badge-status st-<?php echo $c['statut']; ?>"><?php echo STATUTS_COMMANDE[$c['statut']] ?? $c['statut']; ?></span></td>
                    <td><a href="<?php echo BASE_URL; ?>/index.php?route=cuisinier/detail-commande&id=<?php echo (int) $c['id']; ?>" class="btn btn-outline btn-sm">Voir</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($commandesLivrees)): ?>
                <tr><td colspan="7" class="empty-state">Aucune commande dans l'historique.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
