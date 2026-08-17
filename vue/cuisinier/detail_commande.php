<?php
$pageTitle = __('dyn.commande') . ' #' . (int) $commande['id'] . ' - ' . APP_NAME;
$i18nPage = 'cuisinier_detail';
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

    <div class="two-col">

        <div>
            <div class="panel">
                <h2 data-i18n="cuisinier_detail.infosTitre">Informations de la commande</h2>
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
                                <td style="font-weight: 600;" data-i18n="common.telephone">Téléphone</td>
                                <td><?php echo htmlspecialchars($commande['telephone'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600;" data-i18n="common.adresse">Adresse</td>
                                <td><?php echo htmlspecialchars($commande['adresse'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600;" data-i18n="common.zone">Zone</td>
                                <td><?php echo htmlspecialchars($commande['zone_nom'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600;" data-i18n="common.livraison">Livraison</td>
                                <td><?php echo htmlspecialchars($commande['date_livraison'] . ' ' . $commande['heure_livraison']); ?></td>
                            </tr>
                            <?php if (!empty($commande['priority'])): ?>
                            <tr>
                                <td style="font-weight: 600;" data-i18n="common.prioritaire">Prioritaire</td>
                                <td><span class="badge-status st-confirmee" data-i18n="common.oui">Oui</span></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($commande['pause'])): ?>
                            <tr>
                                <td style="font-weight: 600;" data-i18n="detail_commande.pause">Pause</td>
                                <td><?php echo htmlspecialchars($commande['pause']); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($commande['commentaire'])): ?>
                            <tr>
                                <td style="font-weight: 600;" data-i18n="common.commentaire">Commentaire</td>
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
                <h2 data-i18n="cuisinier_detail.articlesAPreparer">Articles à préparer</h2>
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
                                <td><?php echo htmlspecialchars($item['categorie']); ?></td>
                                <td><?php echo number_format((float) $item['prix'], 2, ',', ' '); ?> DH</td>
                                <td><?php echo (int) $item['quantite']; ?></td>
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
                        <?php if (!empty($event['ancien_statut']) && $event['ancien_statut'] !== $event['nouveau_statut']): ?>
                            <small style="color: var(--text-muted);">
                                <span data-i18n="common.changeDe">Changé de</span>
                                <strong data-i18n="<?php echo $statutI18n[$event['ancien_statut']] ?? ''; ?>"><?php echo STATUTS_COMMANDE[$event['ancien_statut']] ?? $event['ancien_statut']; ?></strong>
                            </small>
                        <?php endif; ?>
                        <?php if (!empty($event['prenom'])): ?>
                            <br><small style="color: var(--text-muted);">
                                <span data-i18n="common.par">par</span> <?php echo htmlspecialchars($event['prenom'] . ' ' . $event['nom']); ?>
                            </small>
                        <?php endif; ?>
                        <?php if (!empty($event['commentaire'])): ?>
                            <br><small style="color: var(--text-muted);">
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
