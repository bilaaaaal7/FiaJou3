<?php
$pageTitle = "Espace livreur - " . APP_NAME;
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';

$joursFr = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
$moisFr = [1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril', 5 => 'mai', 6 => 'juin',
    7 => 'juillet', 8 => 'août', 9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre'];
$dateFr = ucfirst($joursFr[(int) date('w')]) . ' ' . date('j') . ' ' . $moisFr[(int) date('n')] . ' ' . date('Y');
$prenom = trim((string) ($_SESSION['prenom'] ?? ''));
?>

<div class="welcome-card">
    <div class="welcome-text">
        <h1>Bonjour, <?php echo htmlspecialchars($prenom !== '' ? $prenom : 'livreur'); ?> 👋</h1>
        <p class="welcome-date">
            <i data-lucide="calendar-days" aria-hidden="true"></i>
            <?php echo htmlspecialchars($dateFr); ?>
        </p>
    </div>
    <div class="welcome-actions">
        <a href="<?php echo BASE_URL; ?>/index.php?route=livreur/historique" class="btn btn-outline">
            <i data-lucide="history" aria-hidden="true"></i> Mon historique
        </a>
        <a href="<?php echo BASE_URL; ?>/index.php?route=client/notifications" class="btn btn-gold">
            <i data-lucide="bell" aria-hidden="true"></i> Notifications
            <?php if ($nbNotifsNonLues > 0): ?> (<?php echo $nbNotifsNonLues; ?>)<?php endif; ?>
        </a>
    </div>
</div>

<?php
$quickAccessItems = [
    ['icon' => 'truck',   'label' => 'Livraisons du jour', 'route' => 'livreur'],
    ['icon' => 'history', 'label' => 'Historique',         'route' => 'livreur/historique'],
    ['icon' => 'bell',    'label' => 'Notifications',      'route' => 'client/notifications'],
];
require ROOT_PATH . '/assets/inc/quick_access.php';
?>

<div class="kpi-grid">
    <div class="kpi-card kpi-card-icon" style="--kpi-accent:#1d4e8f;">
        <span class="kpi-icon"><i data-lucide="truck" aria-hidden="true"></i></span>
        <div class="kpi-body">
            <div class="kpi-label">À livrer</div>
            <div class="kpi-value"><?php echo (int) $nbAPretee; ?></div>
        </div>
    </div>
    <div class="kpi-card kpi-card-icon" style="--kpi-accent:#5732a6;">
        <span class="kpi-icon"><i data-lucide="bike" aria-hidden="true"></i></span>
        <div class="kpi-body">
            <div class="kpi-label">En livraison</div>
            <div class="kpi-value"><?php echo (int) $nbEnLivraison; ?></div>
        </div>
    </div>
    <div class="kpi-card kpi-card-icon" style="--kpi-accent:#226b2e;">
        <span class="kpi-icon"><i data-lucide="package-check" aria-hidden="true"></i></span>
        <div class="kpi-body">
            <div class="kpi-label">Livrées aujourd'hui</div>
            <div class="kpi-value"><?php echo (int) $nbLivrees; ?></div>
        </div>
    </div>
    <div class="kpi-card kpi-card-icon gold">
        <span class="kpi-icon"><i data-lucide="badge-check" aria-hidden="true"></i></span>
        <div class="kpi-body">
            <div class="kpi-label">Livraisons au total</div>
            <div class="kpi-value"><?php echo (int) $totalLivraisons; ?></div>
        </div>
    </div>
</div>

<div class="grid-2">
    <div class="panel">
        <h2>Répartition du jour</h2>
        <div class="statut-list">
            <div class="statut-row">
                <span class="badge-status st-prete">À livrer</span>
                <strong><?php echo (int) $repartitionJour['prete']; ?></strong>
            </div>
            <div class="statut-row">
                <span class="badge-status st-en_livraison">En livraison</span>
                <strong><?php echo (int) $repartitionJour['en_livraison']; ?></strong>
            </div>
            <div class="statut-row">
                <span class="badge-status st-livree">Livrées aujourd'hui</span>
                <strong><?php echo (int) $repartitionJour['livree']; ?></strong>
            </div>
            <div class="statut-row">
                <span>Commandes en cours</span>
                <strong><?php echo (int) $totalEnCours; ?></strong>
            </div>
        </div>
    </div>

    <div class="panel">
        <h2>Mes statistiques</h2>
        <div class="statut-list">
            <div class="statut-row">
                <span>Total livraisons effectuées</span>
                <strong><?php echo (int) $totalLivraisons; ?></strong>
            </div>
            <div class="statut-row">
                <span>Chiffre d'affaires livré</span>
                <strong><?php echo number_format((float) $totalCA, 2, ',', ' '); ?> DH</strong>
            </div>
            <div class="statut-row">
                <span>En cours actuellement</span>
                <strong><?php echo (int) $totalEnCours; ?></strong>
            </div>
        </div>
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
                        <a href="<?php echo BASE_URL; ?>/index.php?route=livreur/commande&id=<?php echo (int) $c['id']; ?>" class="btn btn-outline btn-sm">Détail</a>
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
                        <a href="<?php echo BASE_URL; ?>/index.php?route=livreur/commande&id=<?php echo (int) $c['id']; ?>" class="btn btn-outline btn-sm">Détail</a>
                        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=livreur" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                            <input type="text" name="commentaire" placeholder="Remarque..." style="border:1px solid var(--border); border-radius:6px; padding:4px 8px; font-size:0.82rem; width:120px;">
                            <button type="submit" name="confirmerLivraison" class="btn btn-gold btn-sm">Confirmer livraison</button>
                        </form>
                        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=livreur" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                            <input type="text" name="commentaire_probleme" placeholder="Décrire le problème..." style="border:1px solid var(--danger); border-radius:6px; padding:4px 8px; font-size:0.82rem; width:180px;">
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

<div class="grid-2">
    <div class="panel">
        <h2>Notifications récentes</h2>
        <?php if (empty($notificationsRecentes)): ?>
            <div class="empty-state">Aucune notification.</div>
        <?php else: ?>
            <?php foreach ($notificationsRecentes as $n): ?>
            <div class="notif-item <?php echo !$n['est_lu'] ? 'notif-unread' : ''; ?>">
                <div class="notif-title">
                    <i data-lucide="bell" aria-hidden="true"></i>
                    <?php echo htmlspecialchars($n['titre']); ?>
                </div>
                <div class="notif-msg"><?php echo htmlspecialchars($n['message']); ?></div>
                <small><?php echo date('d/m/Y H:i', strtotime($n['date_notification'])); ?></small>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="panel">
        <h2>Activité récente</h2>
        <?php if (empty($activiteRecente)): ?>
            <div class="empty-state">Aucune activité enregistrée.</div>
        <?php else: ?>
            <?php foreach ($activiteRecente as $a): ?>
            <div class="notif-item">
                <div class="notif-title">
                    <i data-lucide="activity" aria-hidden="true"></i>
                    Commande #<?php echo (int) $a['order_id']; ?>
                    <?php if ($a['ancien_statut'] === $a['nouveau_statut']): ?>
                        <span class="badge-status st-annulee">Signalement</span>
                    <?php else: ?>
                        <span class="badge-status st-<?php echo htmlspecialchars($a['nouveau_statut']); ?>">
                            <?php echo STATUTS_COMMANDE[$a['nouveau_statut']] ?? $a['nouveau_statut']; ?>
                        </span>
                    <?php endif; ?>
                </div>
                <?php if (!empty($a['commentaire'])): ?>
                    <div class="notif-msg"><?php echo htmlspecialchars($a['commentaire']); ?></div>
                <?php endif; ?>
                <small><?php echo date('d/m/Y H:i', strtotime($a['date_modification'])); ?></small>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
