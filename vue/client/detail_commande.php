<?php
$pageTitle = "Commande #" . (int) $commande['id'] . " - " . APP_NAME;
$extraCss = ['admin.css', 'profile-menu.css', 'client-public.css'];
$bodyClass = 'client-public-layout';
$i18nPage = 'detail_commande';
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/client_navbar.php';

$cleStatutCommande = [
    'en_attente'    => 'mes_commandes.statutEnAttente',
    'confirmee'     => 'mes_commandes.statutConfirmee',
    'en_preparation'=> 'mes_commandes.statutEnPreparation',
    'prete'         => 'mes_commandes.statutPrete',
    'en_livraison'  => 'mes_commandes.statutEnLivraison',
    'livree'        => 'mes_commandes.statutLivree',
    'annulee'       => 'mes_commandes.statutAnnulee',
];

$prenomDetail = trim((string) ($_SESSION['prenom'] ?? ''));
$nomDetail    = trim((string) ($_SESSION['nom'] ?? ''));
$emailDetail  = (string) ($_SESSION['email'] ?? '');
$initialesDetail = mb_strtoupper(mb_substr($prenomDetail, 0, 1) . mb_substr($nomDetail, 0, 1));
if ($initialesDetail === '') {
    $initialesDetail = '?';
}
?>

<div class="page-profil">

    <?php require ROOT_PATH . '/assets/inc/back_home.php'; ?>

    <div class="topbar">
        <h1><span data-i18n="detail_commande.titre">Commande</span> #<?php echo (int) $commande['id']; ?></h1>
        <div class="topbar-actions">
            <p class="profil-subtitle" data-i18n="detail_commande.subtitre">Consultez les détails et le suivi de votre commande.</p>
            <a href="<?php echo BASE_URL; ?>/index.php?route=client/mes-commandes" class="btn btn-outline btn-sm" data-i18n="detail_commande.retour">Retour</a>
        </div>
    </div>

    <div class="profil-hero">
        <span class="profil-hero-avatar"><?php echo htmlspecialchars($initialesDetail); ?></span>
        <div class="profil-hero-info">
            <strong><?php echo htmlspecialchars(trim($prenomDetail . ' ' . $nomDetail)); ?></strong>
            <span><?php echo htmlspecialchars($emailDetail); ?></span>
        </div>
    </div>

    <div class="panel profil-card">
        <div class="profil-card-head">
            <i data-lucide="shopping-bag" aria-hidden="true"></i>
            <h2 data-i18n="detail_commande.infosTitre">Informations de la commande</h2>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <tbody>
                    <tr>
                        <td style="font-weight: 600;" data-i18n="detail_commande.statut">Statut</td>
                        <td>
                            <span class="badge-status st-<?php echo htmlspecialchars($commande['statut']); ?>"
                                  data-i18n="<?php echo $cleStatutCommande[$commande['statut']] ?? ''; ?>">
                                <?php echo STATUTS_COMMANDE[$commande['statut']] ?? $commande['statut']; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: 600;" data-i18n="detail_commande.dateCommande">Date de commande</td>
                        <td><?php echo htmlspecialchars($commande['date_commande']); ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight: 600;" data-i18n="detail_commande.dateLivraison">Date de livraison</td>
                        <td><?php echo htmlspecialchars($commande['date_livraison']); ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight: 600;" data-i18n="detail_commande.heureLivraison">Heure de livraison</td>
                        <td><?php echo htmlspecialchars($commande['heure_livraison']); ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight: 600;" data-i18n="detail_commande.zone">Zone</td>
                        <td><?php echo htmlspecialchars($commande['zone_nom'] ?? '-'); ?></td>
                    </tr>
                    <?php if (!empty($commande['priority'])): ?>
                    <tr>
                        <td style="font-weight: 600;" data-i18n="detail_commande.prioritaire">Prioritaire</td>
                        <td><span class="badge-status st-confirmee" data-i18n="detail_commande.oui">Oui</span></td>
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
                        <td style="font-weight: 600;" data-i18n="detail_commande.commentaire">Commentaire</td>
                        <td><?php echo htmlspecialchars($commande['commentaire']); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td style="font-weight: 600;" data-i18n="detail_commande.total">Total</td>
                        <td style="font-weight: 700; color: var(--gold-dark); font-size: 1.1rem;">
                            <?php echo number_format((float) $commande['total'], 2, ',', ' '); ?> DH
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel profil-card">
        <div class="profil-card-head">
            <i data-lucide="package" aria-hidden="true"></i>
            <h2 data-i18n="detail_commande.articlesTitre">Articles commandés</h2>
        </div>
        <?php if (!empty($items)): ?>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th data-i18n="detail_commande.image">Image</th>
                        <th data-i18n="detail_commande.produit">Produit</th>
                        <th data-i18n="detail_commande.categorie">Catégorie</th>
                        <th data-i18n="detail_commande.prixUnitaire">Prix unitaire</th>
                        <th data-i18n="detail_commande.quantite">Quantité</th>
                        <th data-i18n="detail_commande.sousTotal">Sous-total</th>
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
        <div class="empty-state" data-i18n="detail_commande.aucunArticle">Aucun article.</div>
        <?php endif; ?>
    </div>

    <div class="panel profil-card">
        <div class="profil-card-head">
            <i data-lucide="history" aria-hidden="true"></i>
            <h2 data-i18n="detail_commande.chronologieTitre">Chronologie du statut</h2>
        </div>
        <?php if (!empty($historique)): ?>
            <?php foreach ($historique as $event): ?>
            <div style="padding: 12px 0; border-bottom: 1px solid var(--border-soft);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <span class="badge-status st-<?php echo htmlspecialchars($event['nouveau_statut']); ?>"
                          data-i18n="<?php echo $cleStatutCommande[$event['nouveau_statut']] ?? ''; ?>">
                        <?php echo STATUTS_COMMANDE[$event['nouveau_statut']] ?? $event['nouveau_statut']; ?>
                    </span>
                    <small style="color: var(--text-muted);">
                        <?php echo htmlspecialchars($event['date_modification']); ?>
                    </small>
                </div>
                <?php if (!empty($event['ancien_statut'])): ?>
                    <small style="color: var(--text-muted);">
                        <span data-i18n="detail_commande.changeDe">Changé de</span>
                        <strong data-i18n="<?php echo $cleStatutCommande[$event['ancien_statut']] ?? ''; ?>"><?php echo STATUTS_COMMANDE[$event['ancien_statut']] ?? $event['ancien_statut']; ?></strong>
                    </small>
                <?php endif; ?>
                <?php if (!empty($event['prenom'])): ?>
                    <br><small style="color: var(--text-muted);">
                        <span data-i18n="detail_commande.par">par</span> <?php echo htmlspecialchars($event['prenom'] . ' ' . $event['nom']); ?>
                    </small>
                <?php endif; ?>
                <?php if (!empty($event['commentaire'])): ?>
                    <br><small style="color: var(--text-muted);">
                        "<?php echo htmlspecialchars($event['commentaire']); ?>"
                    </small>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state" data-i18n="detail_commande.aucunHistorique">Aucun historique de statut.</div>
        <?php endif; ?>
    </div>

</div>

<?php require ROOT_PATH . '/assets/inc/client_footer.php'; ?>