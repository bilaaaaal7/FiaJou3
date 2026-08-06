<?php
$pageTitle = "Dashboard Admin - " . APP_NAME;
$pageHeading = "Tableau de bord";
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';

$joursFr = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
$moisFr = [1 => 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
$dateBienvenue = ucfirst($joursFr[(int) date('w')]) . ' ' . date('j') . ' ' . $moisFr[(int) date('n')] . ' ' . date('Y');
$prenomAdmin = trim((string) ($_SESSION['prenom'] ?? 'Administrateur'));
?>

<div class="welcome-card">
    <div class="welcome-text">
        <h1>Bonjour, <?php echo htmlspecialchars($prenomAdmin); ?></h1>
        <p class="welcome-date">
            <i data-lucide="calendar" aria-hidden="true"></i>
            <span><?php echo htmlspecialchars($dateBienvenue); ?></span>
        </p>
    </div>
    <div class="welcome-actions">
        <?php if (!$menuActif): ?>
            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine" class="btn btn-gold">
                <i data-lucide="calendar-plus" aria-hidden="true"></i> Publier le menu de la semaine
            </a>
        <?php else: ?>
            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/commandes" class="btn btn-gold">
                <i data-lucide="shopping-bag" aria-hidden="true"></i> Voir les commandes
            </a>
        <?php endif; ?>
    </div>
</div>

<?php
$quickAccessItems = [
    ['icon' => 'utensils',         'label' => 'Produits',          'route' => 'admin/plats'],
    ['icon' => 'tags',             'label' => 'Catégories',        'route' => 'admin/categories'],
    ['icon' => 'shopping-bag',     'label' => 'Commandes',         'route' => 'admin/commandes'],
    ['icon' => 'users',            'label' => 'Clients',           'route' => 'admin/utilisateurs'],
    ['icon' => 'chef-hat',         'label' => 'Cuisiniers',        'route' => 'admin/cuisiniers'],
    ['icon' => 'bike',             'label' => 'Livreurs',          'route' => 'admin/livreurs'],
    ['icon' => 'map-pin',          'label' => 'Zones de livraison','route' => 'admin/zones'],
    ['icon' => 'calendar-days',    'label' => 'Menu de la semaine','route' => 'admin/menu-semaine'],
];
require ROOT_PATH . '/assets/inc/quick_access.php';
?>

<div class="kpi-grid">
    <div class="kpi-card kpi-card-icon gold">
        <span class="kpi-icon"><i data-lucide="wallet" aria-hidden="true"></i></span>
        <div class="kpi-body">
            <div class="kpi-label">Chiffre d'affaires</div>
            <div class="kpi-value"><?php echo number_format($chiffreAffaires, 2, ',', ' '); ?> DH</div>
        </div>
    </div>
    <div class="kpi-card kpi-card-icon">
        <span class="kpi-icon"><i data-lucide="shopping-bag" aria-hidden="true"></i></span>
        <div class="kpi-body">
            <div class="kpi-label">Commandes totales</div>
            <div class="kpi-value"><?php echo $nbOrders; ?></div>
        </div>
    </div>
    <div class="kpi-card kpi-card-icon">
        <span class="kpi-icon"><i data-lucide="calendar-clock" aria-hidden="true"></i></span>
        <div class="kpi-body">
            <div class="kpi-label">Commandes aujourd'hui</div>
            <div class="kpi-value"><?php echo count($commandesAujourdHui); ?></div>
        </div>
    </div>
    <div class="kpi-card kpi-card-icon">
        <span class="kpi-icon"><i data-lucide="users" aria-hidden="true"></i></span>
        <div class="kpi-body">
            <div class="kpi-label">Clients</div>
            <div class="kpi-value"><?php echo $nbClients; ?></div>
        </div>
    </div>
    <div class="kpi-card kpi-card-icon">
        <span class="kpi-icon"><i data-lucide="chef-hat" aria-hidden="true"></i></span>
        <div class="kpi-body">
            <div class="kpi-label">Cuisiniers</div>
            <div class="kpi-value"><?php echo $nbCuisiniers; ?></div>
        </div>
    </div>
    <div class="kpi-card kpi-card-icon">
        <span class="kpi-icon"><i data-lucide="bike" aria-hidden="true"></i></span>
        <div class="kpi-body">
            <div class="kpi-label">Livreurs</div>
            <div class="kpi-value"><?php echo $nbLivreurs; ?></div>
        </div>
    </div>
    <div class="kpi-card kpi-card-icon">
        <span class="kpi-icon"><i data-lucide="utensils" aria-hidden="true"></i></span>
        <div class="kpi-body">
            <div class="kpi-label">Plats</div>
            <div class="kpi-value"><?php echo $nbPlats; ?></div>
        </div>
    </div>
    <div class="kpi-card kpi-card-icon">
        <span class="kpi-icon"><i data-lucide="tags" aria-hidden="true"></i></span>
        <div class="kpi-body">
            <div class="kpi-label">Catégories</div>
            <div class="kpi-value"><?php echo $nbCategories; ?></div>
        </div>
    </div>
    <div class="kpi-card kpi-card-icon">
        <span class="kpi-icon"><i data-lucide="map-pin" aria-hidden="true"></i></span>
        <div class="kpi-body">
            <div class="kpi-label">Zones</div>
            <div class="kpi-value"><?php echo $nbZones; ?></div>
        </div>
    </div>
</div>

<div class="kpi-grid">
    <div class="kpi-card kpi-card-icon" style="--kpi-accent:#6b5b3a;">
        <span class="kpi-icon"><i data-lucide="clock" aria-hidden="true"></i></span>
        <div class="kpi-body">
            <div class="kpi-label">En attente</div>
            <div class="kpi-value"><?php echo $commandesEnAttente; ?></div>
        </div>
    </div>
    <div class="kpi-card kpi-card-icon" style="--kpi-accent:#9a6c11;">
        <span class="kpi-icon"><i data-lucide="cooking-pot" aria-hidden="true"></i></span>
        <div class="kpi-body">
            <div class="kpi-label">En préparation</div>
            <div class="kpi-value"><?php echo $commandesEnPreparation; ?></div>
        </div>
    </div>
    <div class="kpi-card kpi-card-icon" style="--kpi-accent:#5732a6;">
        <span class="kpi-icon"><i data-lucide="truck" aria-hidden="true"></i></span>
        <div class="kpi-body">
            <div class="kpi-label">En livraison</div>
            <div class="kpi-value"><?php echo $commandesEnLivraison; ?></div>
        </div>
    </div>
    <div class="kpi-card kpi-card-icon" style="--kpi-accent:#226b2e;">
        <span class="kpi-icon"><i data-lucide="package-check" aria-hidden="true"></i></span>
        <div class="kpi-body">
            <div class="kpi-label">Livrées</div>
            <div class="kpi-value"><?php echo $commandesLivrees; ?></div>
        </div>
    </div>
</div>

<?php $alertes = []; ?>
<?php if (!$menuActif): ?>
    <?php $alertes[] = 'Aucun menu hebdomadaire publié pour la semaine en cours. Publiez un menu depuis l\'espace « Menu de la semaine ».'; ?>
<?php endif; ?>
<?php foreach ($commandesEnRetard as $retard): ?>
    <?php $alertes[] = 'Commande #' . $retard['id'] . ' (' . htmlspecialchars($retard['prenom'] . ' ' . $retard['nom']) . ') en retard — livraison prévue le ' . $retard['date_livraison'] . ' à ' . $retard['heure_livraison'] . '. Prévoyez le rattrapage.'; ?>
<?php endforeach; ?>
<?php if (!empty($alertes)): ?>
<div class="panel alert-panel">
    <h2 class="alert-title">
        <i data-lucide="triangle-alert" aria-hidden="true"></i> Alertes opérationnelles
    </h2>
    <ul class="alert-list">
        <?php foreach ($alertes as $alerte): ?>
            <li><?php echo $alerte; ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php
$maxNb = max(1, max(array_column($stats7Jours, 'nb')));
$maxCa = max(1, max(array_column($stats7Jours, 'ca')));
?>

<div class="grid-2">
    <div class="panel">
        <h2>Commandes — 7 derniers jours</h2>
        <div class="bar-chart">
            <?php foreach ($stats7Jours as $stat): ?>
                <?php
                $hauteur = round(($stat['nb'] / $maxNb) * 100);
                $jourCourant = $stat['date'] === date('Y-m-d');
                ?>
                <div class="bar-col">
                    <span class="bar-value"><?php echo $stat['nb']; ?></span>
                    <div class="bar<?php echo $jourCourant ? ' is-today' : ''; ?>" title="<?php echo $stat['date']; ?>"
                         style="height: <?php echo $hauteur; ?>%; background: <?php echo $jourCourant ? 'var(--gold)' : '#D8C79B'; ?>;"></div>
                    <span class="bar-day"><?php echo htmlspecialchars($stat['label']); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="panel">
        <h2>Chiffre d'affaires — 7 derniers jours</h2>
        <div class="bar-chart">
            <?php foreach ($stats7Jours as $stat): ?>
                <?php
                $hauteur = round(($stat['ca'] / $maxCa) * 100);
                $jourCourant = $stat['date'] === date('Y-m-d');
                ?>
                <div class="bar-col">
                    <span class="bar-value"><?php echo $stat['ca'] > 0 ? number_format($stat['ca'], 0, ',', ' ') : ''; ?></span>
                    <div class="bar<?php echo $jourCourant ? ' is-today' : ''; ?>" title="<?php echo $stat['date'] . ' : ' . number_format($stat['ca'], 2, ',', ' ') . ' DH'; ?>"
                         style="height: <?php echo $hauteur; ?>%; background: <?php echo $jourCourant ? 'var(--gold-dark)' : '#B9A06B'; ?>;"></div>
                    <span class="bar-day"><?php echo htmlspecialchars($stat['label']); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="grid-2">
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

<?php
$couleursStatuts = [
    'en_attente'     => '#6b5b3a',
    'confirmee'      => '#8a6d1f',
    'en_preparation' => '#9a6c11',
    'prete'          => '#b88618',
    'en_livraison'   => '#5732a6',
    'livree'         => '#226b2e',
    'annulee'        => '#c0392b',
];
?>

<div class="grid-2">
    <div class="panel">
        <h2>Répartition par statut</h2>
        <?php if ($totalCommandesTousStatuts > 0): ?>
            <div class="chart-stack">
                <?php foreach ($statutRepartition as $cle => $nb): ?>
                    <?php if ($nb > 0): ?>
                        <div style="width: <?php echo round($nb / $totalCommandesTousStatuts * 100, 2); ?>%; background: <?php echo $couleursStatuts[$cle] ?? '#999'; ?>;"
                             title="<?php echo STATUTS_COMMANDE[$cle] . ' : ' . $nb; ?>"></div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="chart-legend">
                <?php foreach ($statutRepartition as $cle => $nb): ?>
                    <?php if ($nb > 0): ?>
                        <span class="legend-item">
                            <span class="legend-dot" style="background: <?php echo $couleursStatuts[$cle] ?? '#999'; ?>;"></span>
                            <?php echo STATUTS_COMMANDE[$cle]; ?> : <strong><?php echo $nb; ?></strong>
                        </span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">Aucune commande.</div>
        <?php endif; ?>
    </div>

    <div class="panel">
        <h2>Prochaines livraisons</h2>
        <?php if (!empty($prochainesLivraisons)): ?>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr><th>#</th><th>Client</th><th>Livraison</th><th>Zone</th><th>Priorité</th></tr>
                </thead>
                <tbody>
                <?php foreach ($prochainesLivraisons as $pl): ?>
                    <tr>
                        <td><?php echo $pl['id']; ?></td>
                        <td><?php echo htmlspecialchars($pl['prenom'] . ' ' . $pl['nom']); ?></td>
                        <td><?php echo $pl['date_livraison'] . ' ' . $pl['heure_livraison']; ?></td>
                        <td><?php echo htmlspecialchars($pl['zone_nom'] ?? '-'); ?></td>
                        <td><?php echo $pl['priority'] ? '<span class="badge-status st-en_attente">Urgent</span>' : '-'; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="empty-state">Aucune livraison à venir.</div>
        <?php endif; ?>
    </div>
</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
