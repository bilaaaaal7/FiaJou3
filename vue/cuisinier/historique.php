<?php
$pageTitle = "Historique cuisinier - " . APP_NAME;
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<h1>Historique de production</h1>

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
                </tr>
            <?php endforeach; ?>
            <?php if (empty($commandesLivrees)): ?>
                <tr><td colspan="6" class="empty-state">Aucune commande dans l'historique.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
