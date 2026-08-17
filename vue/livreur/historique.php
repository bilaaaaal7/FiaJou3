<?php
$pageTitle = "Historique livreur - " . APP_NAME;
$i18nPage = 'livreur_historique';
$pageHeading = "Historique de livraison";
$pageHeadingI18n = 'livreur_historique.pageHeading';
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';

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

<div class="panel">
    <h2 data-i18n="livreur_historique.monActiviteRecente">Mon activité récente</h2>
    <?php if (empty($activite)): ?>
        <div class="empty-state" data-i18n="livreur_historique.aucuneActiviteMoment">Aucune activité enregistrée pour le moment.</div>
    <?php else: ?>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th data-i18n="livreur_historique.date">Date</th>
                    <th data-i18n="common.commande">Commande</th>
                    <th data-i18n="common.client">Client</th>
                    <th data-i18n="livreur_historique.action">Action</th>
                    <th data-i18n="common.remarque">Remarque</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($activite as $a): ?>
                <tr>
                    <td><?php echo date('d/m/Y H:i', strtotime($a['date_modification'])); ?></td>
                    <td><a href="<?php echo BASE_URL; ?>/index.php?route=livreur/detail-commande&id=<?php echo (int) $a['order_id']; ?>">#<?php echo (int) $a['order_id']; ?></a></td>
                    <td><?php echo htmlspecialchars($a['client_prenom'] . ' ' . $a['client_nom']); ?></td>
                    <td>
                        <?php if ($a['ancien_statut'] === $a['nouveau_statut']): ?>
                            <span style="color:var(--text-muted);" data-i18n="common.signalement">Signalement</span>
                        <?php else: ?>
                            <span class="badge-status st-<?php echo htmlspecialchars($a['nouveau_statut']); ?>" data-i18n="<?php echo $statutI18n[$a['nouveau_statut']] ?? ''; ?>">
                                <?php echo STATUTS_COMMANDE[$a['nouveau_statut']] ?? $a['nouveau_statut']; ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $a['commentaire'] ? render_i18n($a['commentaire']) : '—'; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<div class="panel">
    <h2 data-i18n="livreur_historique.livraisonsTerminees">Livraisons terminées</h2>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th data-i18n="common.client">Client</th>
                    <th data-i18n="common.dateLivraison">Date livraison</th>
                    <th data-i18n="common.heure">Heure</th>
                    <th data-i18n="common.total">Total</th>
                    <th data-i18n="common.zone">Zone</th>
                    <th data-i18n="common.statut">Statut</th>
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
                    <td><?php echo htmlspecialchars($c['zone_nom'] ?? '-'); ?></td>
                    <td><span class="badge-status st-<?php echo $c['statut']; ?>" data-i18n="<?php echo $statutI18n[$c['statut']] ?? ''; ?>"><?php echo STATUTS_COMMANDE[$c['statut']] ?? $c['statut']; ?></span></td>
                    <td><a href="<?php echo BASE_URL; ?>/index.php?route=livreur/detail-commande&id=<?php echo (int) $c['id']; ?>" class="btn btn-outline btn-sm" data-i18n="common.voir">Voir</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($commandesLivrees)): ?>
                <tr><td colspan="8" class="empty-state" data-i18n="livreur_historique.aucunHistorique">Aucune livraison dans l'historique.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
