<?php
$pageTitle = "Tableau de bord - " . APP_NAME;
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<div style="max-width: 1100px; margin: 0 auto;">

    <div class="topbar">
        <h1>Tableau de bord</h1>
    </div>

    <?php
    $quickAccessItems = [
        ['icon' => 'utensils-crossed', 'label' => 'Menu',               'route' => 'client'],
        ['icon' => 'calendar-days',    'label' => 'Menu de la semaine', 'route' => 'client/menu-semaine'],
        ['icon' => 'shopping-cart',    'label' => 'Panier',             'route' => 'client/panier'],
        ['icon' => 'package',          'label' => 'Mes commandes',      'route' => 'client/mes-commandes'],
        ['icon' => 'user',             'label' => 'Profil',             'route' => 'client/profil'],
        ['icon' => 'bell',             'label' => 'Notifications',      'route' => 'client/notifications'],
    ];
    require ROOT_PATH . '/assets/inc/quick_access.php';
    ?>

    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-label">Total commandes</div>
            <div class="kpi-value"><?php echo (int) $stats['total_commandes']; ?></div>
        </div>
        <div class="kpi-card gold">
            <div class="kpi-label">Total dépensé</div>
            <div class="kpi-value"><?php echo number_format((float) $stats['total_depense'], 2, ',', ' '); ?> DH</div>
        </div>
    </div>

    <?php if ($prochaineLivraison): ?>
    <div class="panel" style="border-left: 4px solid var(--gold);">
        <h2>Prochaine livraison</h2>
        <div style="display: flex; gap: 30px; flex-wrap: wrap; align-items: center;">
            <div>
                <div class="kpi-label">Date</div>
                <div style="font-size: 1.1rem; font-weight: 600;">
                    <?php echo htmlspecialchars($prochaineLivraison['date_livraison']); ?>
                </div>
            </div>
            <div>
                <div class="kpi-label">Heure</div>
                <div style="font-size: 1.1rem; font-weight: 600;">
                    <?php echo htmlspecialchars($prochaineLivraison['heure_livraison']); ?>
                </div>
            </div>
            <div>
                <div class="kpi-label">Statut</div>
                <span class="badge-status st-<?php echo htmlspecialchars($prochaineLivraison['statut']); ?>">
                    <?php echo STATUTS_COMMANDE[$prochaineLivraison['statut']] ?? $prochaineLivraison['statut']; ?>
                </span>
            </div>
            <div>
                <div class="kpi-label">Total</div>
                <div style="font-size: 1.1rem; font-weight: 600; color: var(--gold-dark);">
                    <?php echo number_format((float) $prochaineLivraison['total'], 2, ',', ' '); ?> DH
                </div>
            </div>
            <a href="<?php echo BASE_URL; ?>/index.php?route=client/detail-commande&id=<?php echo (int) $prochaineLivraison['id']; ?>"
               class="btn btn-gold btn-sm">Voir détails</a>
        </div>
    </div>
    <?php else: ?>
    <div class="panel">
        <div class="empty-state">
            Aucune livraison à venir.
            <br><br>
            <a href="<?php echo BASE_URL; ?>/index.php?route=client" class="btn btn-gold">Consulter le menu</a>
        </div>
    </div>
    <?php endif; ?>

    <div class="panel">
        <h2>Commandes récentes</h2>

        <?php if (!empty($commandesRecentes)): ?>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date commande</th>
                        <th>Date livraison</th>
                        <th>Heure</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($commandesRecentes as $cmd): ?>
                    <tr>
                        <td><?php echo (int) $cmd['id']; ?></td>
                        <td><?php echo htmlspecialchars($cmd['date_commande']); ?></td>
                        <td><?php echo htmlspecialchars($cmd['date_livraison']); ?></td>
                        <td><?php echo htmlspecialchars($cmd['heure_livraison']); ?></td>
                        <td><?php echo number_format((float) $cmd['total'], 2, ',', ' '); ?> DH</td>
                        <td>
                            <span class="badge-status st-<?php echo htmlspecialchars($cmd['statut']); ?>">
                                <?php echo STATUTS_COMMANDE[$cmd['statut']] ?? $cmd['statut']; ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=client/detail-commande&id=<?php echo (int) $cmd['id']; ?>"
                               class="btn btn-outline btn-sm">Détail</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state">Aucune commande pour le moment.</div>
        <?php endif; ?>
    </div>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
