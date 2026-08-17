<?php
$pageTitle = "Espace cuisinier - " . APP_NAME;
$i18nPage = 'cuisinier_dashboard';
$pageHeading = "Espace cuisinier";
$pageHeadingI18n = 'cuisinier_dashboard.pageHeading';
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';

$dateFr = date_bienvenue();
$prenom = trim((string) ($_SESSION['prenom'] ?? ''));

$statutI18n = [
    'en_attente' => 'common.enAttente',
    'confirmee' => 'common.confirmee',
    'en_preparation' => 'common.enPreparation',
    'prete' => 'common.pret',
    'en_livraison' => 'common.enLivraison',
    'livree' => 'common.livree',
    'annulee' => 'common.annulee',
];
?>

<?php if (isset($_GET['erreur']) && $_GET['erreur'] !== ''): ?>
    <div class="alert alert-danger py-2" role="alert"><?php echo render_i18n($_GET['erreur']); ?></div>
<?php endif; ?>

<div class="welcome-card">
    <div class="welcome-text">
        <h1><span data-i18n="cuisinier_dashboard.bonjour">Bonjour, </span><?php echo htmlspecialchars($prenom !== '' ? $prenom : __('dyn.cuisinier')); ?> 👨‍🍳</h1>
        <p class="welcome-date">
            <i data-lucide="calendar-days" aria-hidden="true"></i>
            <?php echo htmlspecialchars($dateFr); ?>
        </p>
    </div>
    <div class="welcome-actions">
        <a href="<?php echo BASE_URL; ?>/index.php?route=cuisinier/historique" class="btn btn-outline">
            <i data-lucide="history" aria-hidden="true"></i> <span data-i18n="common.monHistorique">Mon historique</span>
        </a>
        <a href="<?php echo BASE_URL; ?>/index.php?route=client/notifications" class="btn btn-gold">
            <i data-lucide="bell" aria-hidden="true"></i> <span data-i18n="common.notifications">Notifications</span>
            <?php if ($nbNotifsNonLues > 0): ?> (<?php echo $nbNotifsNonLues; ?>)<?php endif; ?>
        </a>
    </div>
</div>

<?php
$quickAccessItems = [
    ['icon' => 'cooking-pot', 'label' => 'Commandes à préparer', 'route' => 'cuisinier', 'i18n' => 'cuisinier_dashboard.commandesAPreparer'],
    ['icon' => 'history',     'label' => 'Historique',           'route' => 'cuisinier/historique', 'i18n' => 'nav.historique'],
    ['icon' => 'bell',        'label' => 'Notifications',        'route' => 'client/notifications', 'i18n' => 'common.notifications'],
];
require ROOT_PATH . '/assets/inc/quick_access.php';
?>

<div class="kpi-grid">
    <div class="kpi-card kpi-card-icon" style="--kpi-accent:#6b5b3a;">
        <span class="kpi-icon"><i data-lucide="cooking-pot" aria-hidden="true"></i></span>
        <div class="kpi-body">
            <div class="kpi-label" data-i18n="common.aPreparer">À préparer</div>
            <div class="kpi-value"><?php echo (int) $nbAPreparer; ?></div>
        </div>
    </div>
    <div class="kpi-card kpi-card-icon" style="--kpi-accent:#9a6c11;">
        <span class="kpi-icon"><i data-lucide="timer" aria-hidden="true"></i></span>
        <div class="kpi-body">
            <div class="kpi-label" data-i18n="common.enPreparation">En préparation</div>
            <div class="kpi-value"><?php echo (int) $nbEnPreparation; ?></div>
        </div>
    </div>
    <div class="kpi-card kpi-card-icon" style="--kpi-accent:#226b2e;">
        <span class="kpi-icon"><i data-lucide="package-check" aria-hidden="true"></i></span>
        <div class="kpi-body">
            <div class="kpi-label" data-i18n="cuisinier_dashboard.platsProduitsHist">Plats prêts (hist.)</div>
            <div class="kpi-value"><?php echo (int) $nbPreteesHistorique; ?></div>
        </div>
    </div>
    <div class="kpi-card kpi-card-icon gold">
        <span class="kpi-icon"><i data-lucide="calendar-clock" aria-hidden="true"></i></span>
        <div class="kpi-body">
            <div class="kpi-label" data-i18n="cuisinier_dashboard.commandesAujourdhui">Commandes aujourd'hui</div>
            <div class="kpi-value"><?php echo (int) $nbCommandesAujourdHui; ?></div>
        </div>
    </div>
</div>

<div class="grid-2">
    <div class="panel">
        <h2 data-i18n="cuisinier_dashboard.etatCommandesJour">État des commandes du jour</h2>
        <div class="statut-list">
            <div class="statut-row">
                <span class="badge-status st-en_attente" data-i18n="common.enAttente">En attente</span>
                <strong><?php echo (int) $repartitionJour['en_attente']; ?></strong>
            </div>
            <div class="statut-row">
                <span class="badge-status st-confirmee" data-i18n="common.confirmee">Confirmée</span>
                <strong><?php echo (int) $repartitionJour['confirmee']; ?></strong>
            </div>
            <div class="statut-row">
                <span class="badge-status st-en_preparation" data-i18n="common.enPreparation">En préparation</span>
                <strong><?php echo (int) $repartitionJour['en_preparation']; ?></strong>
            </div>
            <div class="statut-row">
                <span class="badge-status st-prete" data-i18n="common.pret">Prête</span>
                <strong><?php echo (int) $repartitionJour['prete']; ?></strong>
            </div>
        </div>
    </div>

    <div class="panel">
        <h2 data-i18n="cuisinier_dashboard.mesStatistiques">Mes statistiques</h2>
        <div class="statut-list">
            <div class="statut-row">
                <span data-i18n="cuisinier_dashboard.commandesATraiter">Commandes à traiter</span>
                <strong><?php echo (int) $nbAPreparer; ?></strong>
            </div>
            <div class="statut-row">
                <span data-i18n="cuisinier_dashboard.enCoursPreparation">En cours de préparation</span>
                <strong><?php echo (int) $nbEnPreparation; ?></strong>
            </div>
            <div class="statut-row">
                <span data-i18n="cuisinier_dashboard.commandesDuJour">Commandes du jour</span>
                <strong><?php echo (int) $nbCommandesAujourdHui; ?></strong>
            </div>
            <div class="statut-row">
                <span data-i18n="cuisinier_dashboard.platsProduitsHist">Plats produits (historique)</span>
                <strong><?php echo (int) $nbPreteesHistorique; ?></strong>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($commandesEnAttente)): ?>
<div class="panel">
    <h2 data-i18n="cuisinier_dashboard.commandesEnAttente">Commandes en attente</h2>
    <?php foreach ($commandesEnAttente as $c): ?>
    <div class="order-card">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; margin-bottom:8px;">
            <div>
                <strong><a href="<?php echo BASE_URL; ?>/index.php?route=cuisinier/commande&id=<?php echo (int) $c['id']; ?>" style="color:var(--gold-dark); text-decoration:none;">#<?php echo $c['id']; ?></a></strong> &mdash;
                <?php echo htmlspecialchars($c['prenom'] . ' ' . $c['nom']); ?>
                <?php if (!empty($c['priority'])): ?> <span class="badge-status st-en_attente" data-i18n="common.prioritaire">Prioritaire</span><?php endif; ?>
            </div>
            <div style="color:var(--text-soft); font-size:0.86rem;">
                <span data-i18n="cuisinier_dashboard.livraison">Livraison :</span> <?php echo $c['date_livraison'] . ' ' . $c['heure_livraison']; ?>
                &middot; <?php echo number_format($c['total'], 2); ?> DH
            </div>
        </div>
        <?php if (!empty($itemsParCommande[$c['id']])): ?>
        <div style="margin-bottom:8px;">
            <small style="color:var(--text-muted);" data-i18n="cuisinier_dashboard.articles">Articles :</small>
            <?php foreach ($itemsParCommande[$c['id']] as $item): ?>
                <span style="display:inline-block; background:var(--gold-light); padding:2px 8px; border-radius:6px; font-size:0.8rem; margin:2px;">
                    <?php echo htmlspecialchars($item['plat_nom']); ?> x<?php echo $item['quantite']; ?>
                </span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($c['commentaire'])): ?>
        <div style="margin-bottom:8px;"><small style="color:var(--text-muted);"><span data-i18n="common.commentaire">Commentaire</span>: <?php echo htmlspecialchars($c['commentaire']); ?></small></div>
        <?php endif; ?>
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=cuisinier" style="display:inline-flex; gap:6px; align-items:center; flex-wrap:wrap;">
            <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
            <input type="hidden" name="nouveau_statut" value="en_preparation">
            <input type="text" name="commentaire" placeholder="Remarque..." style="border:1px solid var(--border); border-radius:6px; padding:4px 8px; font-size:0.82rem; width:160px;" data-i18n-placeholder="common.remarquePlaceholder">
            <button type="submit" name="avancerStatut" class="btn btn-gold btn-sm" data-i18n="cuisinier_dashboard.commencerPreparation">Commencer la préparation</button>
            <a href="<?php echo BASE_URL; ?>/index.php?route=cuisinier/detail-commande&id=<?php echo (int) $c['id']; ?>" class="btn btn-outline btn-sm" data-i18n="common.voir">Voir</a>
        </form>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (!empty($commandesEnPreparation)): ?>
<div class="panel">
    <h2 data-i18n="cuisinier_dashboard.enCoursPreparation">En préparation</h2>
    <?php foreach ($commandesEnPreparation as $c): ?>
    <div class="order-card">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; margin-bottom:8px;">
            <div>
                <strong><a href="<?php echo BASE_URL; ?>/index.php?route=cuisinier/commande&id=<?php echo (int) $c['id']; ?>" style="color:var(--gold-dark); text-decoration:none;">#<?php echo $c['id']; ?></a></strong> &mdash;
                <?php echo htmlspecialchars($c['prenom'] . ' ' . $c['nom']); ?>
            </div>
            <div style="color:var(--text-soft); font-size:0.86rem;">
                <span data-i18n="cuisinier_dashboard.livraison">Livraison :</span> <?php echo $c['date_livraison'] . ' ' . $c['heure_livraison']; ?>
                &middot; <?php echo number_format($c['total'], 2); ?> DH
            </div>
        </div>
        <?php if (!empty($itemsParCommande[$c['id']])): ?>
        <div style="margin-bottom:8px;">
            <small style="color:var(--text-muted);" data-i18n="cuisinier_dashboard.articles">Articles :</small>
            <?php foreach ($itemsParCommande[$c['id']] as $item): ?>
                <span style="display:inline-block; background:var(--gold-light); padding:2px 8px; border-radius:6px; font-size:0.8rem; margin:2px;">
                    <?php echo htmlspecialchars($item['plat_nom']); ?> x<?php echo $item['quantite']; ?>
                </span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($c['commentaire'])): ?>
        <div style="margin-bottom:8px;"><small style="color:var(--text-muted);"><span data-i18n="common.commentaire">Commentaire</span>: <?php echo htmlspecialchars($c['commentaire']); ?></small></div>
        <?php endif; ?>
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=cuisinier" style="display:inline-flex; gap:6px; align-items:center; flex-wrap:wrap;">
            <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
            <input type="hidden" name="nouveau_statut" value="prete">
            <input type="text" name="commentaire" placeholder="Remarque..." style="border:1px solid var(--border); border-radius:6px; padding:4px 8px; font-size:0.82rem; width:160px;" data-i18n-placeholder="common.remarquePlaceholder">
            <button type="submit" name="avancerStatut" class="btn btn-gold btn-sm" data-i18n="common.marquerPret">Marquer prête</button>
            <a href="<?php echo BASE_URL; ?>/index.php?route=cuisinier/detail-commande&id=<?php echo (int) $c['id']; ?>" class="btn btn-outline btn-sm" data-i18n="common.voir">Voir</a>
        </form>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (empty($commandesEnAttente) && empty($commandesEnPreparation)): ?>
<div class="panel">
    <div class="empty-state" data-i18n="cuisinier_dashboard.aucuneCommandePreparer">Aucune commande à préparer pour le moment.</div>
</div>
<?php endif; ?>

<?php if (!empty($quantites)): ?>
<div class="panel">
    <h2 data-i18n="cuisinier_dashboard.quantitesAProduire">Quantités à produire aujourd'hui</h2>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr><th data-i18n="common.produit">Produit</th><th data-i18n="common.categorie">Catégorie</th><th data-i18n="cuisinier_dashboard.quantiteTotale">Quantité totale</th></tr>
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

<div class="grid-2">
    <div class="panel">
        <h2 data-i18n="cuisinier_dashboard.notificationsRecentes">Notifications récentes</h2>
        <?php if (empty($notificationsRecentes)): ?>
            <div class="empty-state" data-i18n="common.aucuneNotification">Aucune notification.</div>
        <?php else: ?>
            <?php foreach ($notificationsRecentes as $n): ?>
            <div class="notif-item <?php echo !$n['est_lu'] ? 'notif-unread' : ''; ?>">
                <div class="notif-title">
                    <i data-lucide="bell" aria-hidden="true"></i>
                    <?php echo render_i18n($n['titre']); ?>
                </div>
                <div class="notif-msg"><?php echo render_i18n($n['message']); ?></div>
                <small><?php echo date('d/m/Y H:i', strtotime($n['date_notification'])); ?></small>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="panel">
        <h2 data-i18n="cuisinier_dashboard.activiteRecente">Activité récente</h2>
        <?php if (empty($activiteRecente)): ?>
            <div class="empty-state" data-i18n="common.aucuneActivite">Aucune activité enregistrée.</div>
        <?php else: ?>
            <?php foreach ($activiteRecente as $a): ?>
            <div class="notif-item">
                <div class="notif-title">
                    <i data-lucide="activity" aria-hidden="true"></i>
                    <?php echo __('dyn.commande'); ?> #<?php echo (int) $a['order_id']; ?>
                    <?php if ($a['ancien_statut'] === $a['nouveau_statut']): ?>
                        <span class="badge-status st-annulee" data-i18n="common.signalement">Signalement</span>
                    <?php else: ?>
                        <span class="badge-status st-<?php echo htmlspecialchars($a['nouveau_statut']); ?>" data-i18n="<?php echo $statutI18n[$a['nouveau_statut']] ?? ''; ?>">
                            <?php echo STATUTS_COMMANDE[$a['nouveau_statut']] ?? $a['nouveau_statut']; ?>
                        </span>
                    <?php endif; ?>
                </div>
                <?php if (!empty($a['commentaire'])): ?>
                    <div class="notif-msg"><?php echo render_i18n($a['commentaire']); ?></div>
                <?php endif; ?>
                <small><?php echo date('d/m/Y H:i', strtotime($a['date_modification'])); ?></small>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
