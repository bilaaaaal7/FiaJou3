<?php
$pageTitle = "Commande #" . (int) $commande['id'] . " - " . APP_NAME;
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<div style="max-width: 1000px; margin: 0 auto;">

    <div class="topbar">
        <h1>Commande #<?php echo (int) $commande['id']; ?></h1>
        <a href="<?php echo BASE_URL; ?>/index.php?route=cuisinier" class="btn btn-outline btn-sm">Retour</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2" role="alert"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="two-col">

        <div>
            <div class="panel">
                <h2>Informations de la commande</h2>
                <div class="table-wrap">
                    <table class="data-table">
                        <tbody>
                            <tr>
                                <td style="font-weight: 600;">Statut</td>
                                <td>
                                    <span class="badge-status st-<?php echo htmlspecialchars($commande['statut']); ?>">
                                        <?php echo STATUTS_COMMANDE[$commande['statut']] ?? $commande['statut']; ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600;">Client</td>
                                <td><?php echo htmlspecialchars($commande['prenom'] . ' ' . $commande['nom']); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600;">Livraison prévue</td>
                                <td><?php echo htmlspecialchars($commande['date_livraison'] . ' à ' . $commande['heure_livraison']); ?></td>
                            </tr>
                            <?php if (!empty($commande['priority'])): ?>
                            <tr>
                                <td style="font-weight: 600;">Prioritaire</td>
                                <td><span class="badge-status st-en_attente">Urgent</span></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($commande['commentaire'])): ?>
                            <tr>
                                <td style="font-weight: 600;">Observations</td>
                                <td><?php echo htmlspecialchars($commande['commentaire']); ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td style="font-weight: 600;">Total</td>
                                <td style="font-weight: 700; color: var(--gold-dark); font-size: 1.1rem;">
                                    <?php echo number_format((float) $commande['total'], 2, ',', ' '); ?> DH
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel">
                <h2>Plats à préparer</h2>
                <?php if (!empty($items)): ?>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Catégorie</th>
                                <th>Prix unitaire</th>
                                <th>Quantité</th>
                                <th>Sous-total</th>
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
                <div class="empty-state">Aucun article.</div>
                <?php endif; ?>
            </div>

            <?php if ($commande['statut'] === 'en_attente'): ?>
            <div class="panel">
                <h2>Action</h2>
                <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=cuisinier/commande&id=<?php echo (int) $commande['id']; ?>">
                    <input type="hidden" name="nouveau_statut" value="en_preparation">
                    <div class="form-group">
                        <label>Remarque (optionnel)</label>
                        <input type="text" name="commentaire" placeholder="Remarque...">
                    </div>
                    <button type="submit" name="avancerStatut" class="btn btn-gold">Commencer la préparation</button>
                </form>
            </div>
            <?php elseif ($commande['statut'] === 'en_preparation'): ?>
            <div class="panel">
                <h2>Action</h2>
                <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=cuisinier/commande&id=<?php echo (int) $commande['id']; ?>">
                    <input type="hidden" name="nouveau_statut" value="prete">
                    <div class="form-group">
                        <label>Remarque (optionnel)</label>
                        <input type="text" name="commentaire" placeholder="Remarque...">
                    </div>
                    <button type="submit" name="avancerStatut" class="btn btn-gold">Marquer prête</button>
                </form>
            </div>
            <?php endif; ?>
        </div>

        <div>
            <div class="panel">
                <h2>Chronologie du statut</h2>
                <?php if (!empty($historique)): ?>
                    <?php foreach ($historique as $event): ?>
                    <div style="padding: 12px 0; border-bottom: 1px solid #f0ece2;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                            <span class="badge-status st-<?php echo htmlspecialchars($event['nouveau_statut']); ?>">
                                <?php echo STATUTS_COMMANDE[$event['nouveau_statut']] ?? $event['nouveau_statut']; ?>
                            </span>
                            <small style="color: var(--text-muted);">
                                <?php echo htmlspecialchars($event['date_modification']); ?>
                            </small>
                        </div>
                        <?php if (!empty($event['commentaire'])): ?>
                            <small style="color: var(--text-muted);">
                                "<?php echo htmlspecialchars($event['commentaire']); ?>"
                            </small>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">Aucun historique de statut.</div>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
