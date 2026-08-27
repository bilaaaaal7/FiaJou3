<?php
$pageTitle = __('dyn.commande') . ' #' . (int) $commande['id'] . ' - ' . APP_NAME;
$i18nPage = 'cuisinier_commande';
$pageHeading = __('dyn.commande') . ' #' . (int) $commande['id'];
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

<div style="max-width: 1000px; margin: 0 auto;">

    <div class="topbar" style="justify-content:flex-end;">
        <a href="<?php echo BASE_URL; ?>/index.php?route=cuisinier" class="btn btn-outline btn-sm" data-i18n="detail_commande.retour">Retour</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2" role="alert"><?php echo render_i18n($error); ?></div>
    <?php endif; ?>

    <div class="two-col">

        <div>
            <div class="panel">
                <h2 data-i18n="cuisinier_commande.infosTitre">Informations de la commande</h2>
                <div class="table-wrap">
                    <table class="data-table">
                        <tbody>
                            <tr>
                                <td style="font-weight: 600;" data-i18n="common.statut">Statut</td>
                                <td>
                                    <span class="badge-status st-<?php echo htmlspecialchars($commande['statut']); ?>" data-i18n="<?php echo $statutI18n[$commande['statut']] ?? ''; ?>">
                                        <?php echo STATUTS_COMMANDE[$commande['statut']] ?? $commande['statut']; ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600;" data-i18n="common.client">Client</td>
                                <td><?php echo htmlspecialchars($commande['prenom'] . ' ' . $commande['nom']); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600;" data-i18n="common.livraisonPrevue">Livraison prévue</td>
                                <td><?php echo htmlspecialchars(($commande['date_livraison'] ?? '-') . ' à ' . $commande['heure_livraison']); ?></td>
                            </tr>
                            <?php if (!empty($commande['priority'])): ?>
                            <tr>
                                <td style="font-weight: 600;" data-i18n="common.prioritaire">Prioritaire</td>
                                <td><span class="badge-status st-en_attente" data-i18n="common.urgent">Urgent</span></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($commande['commentaire'])): ?>
                            <tr>
                                <td style="font-weight: 600;" data-i18n="cuisinier_commande.observations">Observations</td>
                                <td><?php echo htmlspecialchars($commande['commentaire']); ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td style="font-weight: 600;" data-i18n="common.total">Total</td>
                                <td style="font-weight: 700; color: var(--gold-dark); font-size: 1.1rem;">
                                    <?php echo number_format((float) $commande['total'], 2, ',', ' '); ?> DH
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel">
                <h2 data-i18n="cuisinier_commande.platsAPreparer">Plats à préparer</h2>
                <?php if (!empty($items)): ?>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th data-i18n="common.produit">Produit</th>
                                <th data-i18n="common.categorie">Catégorie</th>
                                <th data-i18n="common.prixUnitaire">Prix unitaire</th>
                                <th data-i18n="common.quantite">Quantité</th>
                                <th data-i18n="common.sousTotal">Sous-total</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['plat_nom']); ?></td>
                                <td><?php echo htmlspecialchars($item['categorie'] ?? '-'); ?></td>
                                <td><?php echo number_format((float) $item['prix'], 2, ',', ' '); ?> DH</td>
                                <td><strong><?php echo (int) $item['quantite']; ?></strong></td>
                                <td><?php echo number_format((float) $item['prix'] * (int) $item['quantite'], 2, ',', ' '); ?> DH</td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state" data-i18n="common.aucunArticle">Aucun article.</div>
                <?php endif; ?>
            </div>

            <?php if ($commande['statut'] === 'en_attente'): ?>
            <div class="panel">
                <h2 data-i18n="admin_assignation.action">Action</h2>
                <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=cuisinier/commande&id=<?php echo (int) $commande['id']; ?>">
                    <input type="hidden" name="nouveau_statut" value="en_preparation">
                    <div class="form-group">
                        <label data-i18n="cuisinier_commande.remarqueOptionnel">Remarque (optionnel)</label>
                        <input type="text" name="commentaire" placeholder="Remarque..." data-i18n-placeholder="common.remarquePlaceholder">
                    </div>
                    <button type="submit" name="avancerStatut" class="btn btn-gold" data-i18n="cuisinier_dashboard.commencerPreparation">Commencer la préparation</button>
                </form>
            </div>
            <?php elseif ($commande['statut'] === 'en_preparation'): ?>
            <div class="panel">
                <h2 data-i18n="admin_assignation.action">Action</h2>
                <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=cuisinier/commande&id=<?php echo (int) $commande['id']; ?>">
                    <input type="hidden" name="nouveau_statut" value="prete">
                    <div class="form-group">
                        <label data-i18n="cuisinier_commande.remarqueOptionnel">Remarque (optionnel)</label>
                        <input type="text" name="commentaire" placeholder="Remarque..." data-i18n-placeholder="common.remarquePlaceholder">
                    </div>
                    <button type="submit" name="avancerStatut" class="btn btn-gold" data-i18n="common.marquerPret">Marquer prête</button>
                </form>
            </div>
            <?php endif; ?>
        </div>

        <div>
            <div class="panel">
                <h2 data-i18n="common.chronologie">Chronologie du statut</h2>
                <?php if (!empty($historique)): ?>
                    <?php foreach ($historique as $event): ?>
                    <div style="padding: 12px 0; border-bottom: 1px solid var(--border-soft);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                            <span class="badge-status st-<?php echo htmlspecialchars($event['nouveau_statut']); ?>" data-i18n="<?php echo $statutI18n[$event['nouveau_statut']] ?? ''; ?>">
                                <?php echo STATUTS_COMMANDE[$event['nouveau_statut']] ?? $event['nouveau_statut']; ?>
                            </span>
                            <small style="color: var(--text-muted);">
                                <?php echo htmlspecialchars($event['date_modification']); ?>
                            </small>
                        </div>
                        <?php if (!empty($event['commentaire'])): ?>
                            <small style="color: var(--text-muted);">
                                "<?php echo render_i18n($event['commentaire']); ?>"
                            </small>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state" data-i18n="common.aucunHistorique">Aucun historique de statut.</div>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
