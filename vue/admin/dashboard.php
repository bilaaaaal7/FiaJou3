<?php
$pageTitle = "Dashboard Admin - " . APP_NAME;
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<h1>Tableau de bord administrateur</h1>

<?php
$quickAccessItems = [
    ['icon' => '🍽️', 'label' => 'Produits',          'route' => 'admin/plats'],
    ['icon' => '🏷️', 'label' => 'Catégories',         'route' => 'admin/categories'],
    ['icon' => '📦', 'label' => 'Commandes',          'route' => 'admin/commandes'],
    ['icon' => '🚚', 'label' => 'Affectations',       'route' => 'admin/assignation'],
    ['icon' => '👥', 'label' => 'Clients',            'route' => 'admin/utilisateurs'],
    ['icon' => '👨‍🍳', 'label' => 'Cuisiniers',         'route' => 'admin/cuisiniers'],
    ['icon' => '🛵', 'label' => 'Livreurs',           'route' => 'admin/livreurs'],
    ['icon' => '📍', 'label' => 'Zones de livraison', 'route' => 'admin/zones'],
    ['icon' => '📅', 'label' => 'Menu de la semaine', 'route' => 'admin/menu-semaine'],
];
require ROOT_PATH . '/assets/inc/quick_access.php';
?>

<div class="kpi-grid">
    <div class="kpi-card gold">
        <div class="kpi-label">Chiffre d'affaires</div>
        <div class="kpi-value"><?php echo number_format($chiffreAffaires, 2, ',', ' '); ?> DH</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Commandes totales</div>
        <div class="kpi-value"><?php echo $nbOrders; ?></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Commandes aujourd'hui</div>
        <div class="kpi-value"><?php echo count($commandesAujourdHui); ?></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Clients</div>
        <div class="kpi-value"><?php echo $nbClients; ?></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Cuisiniers</div>
        <div class="kpi-value"><?php echo $nbCuisiniers; ?></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Livreurs</div>
        <div class="kpi-value"><?php echo $nbLivreurs; ?></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Plats</div>
        <div class="kpi-value"><?php echo $nbPlats; ?></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Catégories</div>
        <div class="kpi-value"><?php echo $nbCategories; ?></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Zones</div>
        <div class="kpi-value"><?php echo $nbZones; ?></div>
    </div>
</div>

<div class="kpi-grid" style="margin-bottom: 24px;">
    <div class="kpi-card" style="border-left: 4px solid #6b5b3a;">
        <div class="kpi-label">En attente</div>
        <div class="kpi-value" style="color: #6b5b3a;"><?php echo $commandesEnAttente; ?></div>
    </div>
    <div class="kpi-card" style="border-left: 4px solid #9a6c11;">
        <div class="kpi-label">En préparation</div>
        <div class="kpi-value" style="color: #9a6c11;"><?php echo $commandesEnPreparation; ?></div>
    </div>
    <div class="kpi-card" style="border-left: 4px solid #5732a6;">
        <div class="kpi-label">En livraison</div>
        <div class="kpi-value" style="color: #5732a6;"><?php echo $commandesEnLivraison; ?></div>
    </div>
    <div class="kpi-card" style="border-left: 4px solid #226b2e;">
        <div class="kpi-label">Livrées</div>
        <div class="kpi-value" style="color: #226b2e;"><?php echo $commandesLivrees; ?></div>
    </div>
</div>

<?php
$maxNb = max(1, max(array_column($stats7Jours, 'nb')));
$maxCa = max(1, max(array_column($stats7Jours, 'ca')));
?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 22px; margin-bottom: 22px;">
    <div class="panel">
        <h2>Commandes — 7 derniers jours</h2>
        <div style="display: flex; align-items: flex-end; gap: 10px; height: 200px; padding: 10px 4px 0;">
            <?php foreach ($stats7Jours as $stat): ?>
                <?php
                $hauteur = round(($stat['nb'] / $maxNb) * 100);
                $jourCourant = $stat['date'] === date('Y-m-d');
                ?>
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 6px;">
                    <span style="font-size: 0.78rem; font-weight: 700; color: var(--gold-dark);"><?php echo $stat['nb']; ?></span>
                    <div title="<?php echo $stat['date']; ?>"
                         style="width: 100%; max-width: 46px; height: <?php echo $hauteur; ?>%; min-height: 4px;
                                background: <?php echo $jourCourant ? 'var(--gold)' : '#D8C79B'; ?>;
                                border-radius: 6px 6px 0 0;"></div>
                    <span style="font-size: 0.72rem; color: var(--text-muted);"><?php echo htmlspecialchars($stat['label']); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="panel">
        <h2>Chiffre d'affaires — 7 derniers jours</h2>
        <div style="display: flex; align-items: flex-end; gap: 10px; height: 200px; padding: 10px 4px 0;">
            <?php foreach ($stats7Jours as $stat): ?>
                <?php
                $hauteur = round(($stat['ca'] / $maxCa) * 100);
                $jourCourant = $stat['date'] === date('Y-m-d');
                ?>
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 6px;">
                    <span style="font-size: 0.78rem; font-weight: 700; color: var(--gold-dark);"><?php echo $stat['ca'] > 0 ? number_format($stat['ca'], 0, ',', ' ') : ''; ?></span>
                    <div title="<?php echo $stat['date'] . ' : ' . number_format($stat['ca'], 2, ',', ' ') . ' DH'; ?>"
                         style="width: 100%; max-width: 46px; height: <?php echo $hauteur; ?>%; min-height: 4px;
                                background: <?php echo $jourCourant ? 'var(--gold-dark)' : '#B9A06B'; ?>;
                                border-radius: 6px 6px 0 0;"></div>
                    <span style="font-size: 0.72rem; color: var(--text-muted);"><?php echo htmlspecialchars($stat['label']); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 22px;">
    <div class="panel">
        <h2>Commandes du jour</h2>
        <?php if (!empty($commandesAujourdHui)): ?>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr><th>#</th><th>Client</th><th>Total</th><th>Statut</th></tr>
                </thead>
                <tbody>
                <?php foreach (array_slice($commandesAujourdHui, 0, 8) as $cmd): ?>
                    <tr>
                        <td><?php echo $cmd['id']; ?></td>
                        <td><?php echo htmlspecialchars($cmd['prenom'] . ' ' . $cmd['nom']); ?></td>
                        <td><?php echo number_format($cmd['total'], 2); ?> DH</td>
                        <td><span class="badge-status st-<?php echo $cmd['statut']; ?>"><?php echo STATUTS_COMMANDE[$cmd['statut']] ?? $cmd['statut']; ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="empty-state">Aucune commande aujourd'hui.</div>
        <?php endif; ?>
    </div>

    <div class="panel">
        <h2>Produits les plus commandés</h2>
        <?php if (!empty($produitsPopulaires)): ?>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr><th>Produit</th><th>Quantité</th><th>CA</th></tr>
                </thead>
                <tbody>
                <?php foreach ($produitsPopulaires as $p): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['nom']); ?></td>
                        <td><?php echo $p['total_qte']; ?></td>
                        <td><?php echo number_format($p['total_ca'], 2); ?> DH</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="empty-state">Aucune donnée disponible.</div>
        <?php endif; ?>
    </div>
</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
