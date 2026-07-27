<?php
$pageTitle = "Commande #" . (int) $commande['id'] . " - " . APP_NAME;
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<div style="max-width: 1000px; margin: 0 auto;">

    <div class="topbar">
        <h1>Commande #<?php echo (int) $commande['id']; ?></h1>
        <a href="<?php echo BASE_URL; ?>/index.php?route=client/mes-commandes" class="btn btn-outline btn-sm">Retour</a>
    </div>

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
                                <td style="font-weight: 600;">Date de commande</td>
                                <td><?php echo htmlspecialchars($commande['date_commande']); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600;">Date de livraison</td>
                                <td><?php echo htmlspecialchars($commande['date_livraison']); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600;">Heure de livraison</td>
                                <td><?php echo htmlspecialchars($commande['heure_livraison']); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600;">Zone</td>
                                <td><?php echo htmlspecialchars($commande['zone_nom'] ?? '-'); ?></td>
                            </tr>
                            <?php if (!empty($commande['priority'])): ?>
                            <tr>
                                <td style="font-weight: 600;">Prioritaire</td>
                                <td><span class="badge-status st-confirmee">Oui</span></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($commande['pause'])): ?>
                            <tr>
                                <td style="font-weight: 600;">Pause</td>
                                <td><?php echo htmlspecialchars($commande['pause']); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($commande['commentaire'])): ?>
                            <tr>
                                <td style="font-weight: 600;">Commentaire</td>
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
                <h2>Articles commandés</h2>
                <?php if (!empty($items)): ?>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Image</th>
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
                                <td>
                                    <img src="<?php echo UPLOADS_URL; ?>/<?php echo htmlspecialchars($item['image']); ?>"
                                         alt="<?php echo htmlspecialchars($item['plat_nom']); ?>"
                                         class="thumb">
                                </td>
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
                <div class="empty-state">Aucun article.</div>
                <?php endif; ?>
            </div>
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
                            <small style="color: #8a8a8a;">
                                <?php echo htmlspecialchars($event['date_modification']); ?>
                            </small>
                        </div>
                        <?php if (!empty($event['ancien_statut'])): ?>
                            <small style="color: #8a8a8a;">
                                Changé de
                                <strong><?php echo STATUTS_COMMANDE[$event['ancien_statut']] ?? $event['ancien_statut']; ?></strong>
                            </small>
                        <?php endif; ?>
                        <?php if (!empty($event['prenom'])): ?>
                            <br><small style="color: #8a8a8a;">
                                par <?php echo htmlspecialchars($event['prenom'] . ' ' . $event['nom']); ?>
                            </small>
                        <?php endif; ?>
                        <?php if (!empty($event['commentaire'])): ?>
                            <br><small style="color: #666;">
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
