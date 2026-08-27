<?php
$pageTitle = "Mon panier - " . APP_NAME;
$extraCss = ['admin.css', 'profile-menu.css', 'client-public.css'];
$bodyClass = 'client-public-layout';
$i18nPage = 'panier';
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/client_navbar.php';
?>

<div style="max-width: 1000px; margin: 0 auto;">

    <?php require ROOT_PATH . '/assets/inc/back_home.php'; ?>

    <div class="topbar">
        <h1 data-i18n="panier.titre">Mon panier</h1>
        <a href="<?php echo BASE_URL; ?>/index.php?route=client" class="btn btn-outline btn-sm" data-i18n="common.retourMenu">Retour au menu</a>
    </div>

    <?php if (!empty($panier)): ?>
    <div class="panel">

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th data-i18n="panier.image">Image</th>
                        <th data-i18n="panier.nom">Nom</th>
                        <th data-i18n="panier.prix">Prix</th>
                        <th data-i18n="panier.quantite">Quantité</th>
                        <th data-i18n="common.sousTotal">Sous-total</th>
                        <th data-i18n="panier.actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($panier as $plat): ?>
                    <tr>
                        <td>
                            <img class="thumb" src="<?php echo UPLOADS_URL; ?>/<?php echo htmlspecialchars($plat['image']); ?>" alt="<?php echo htmlspecialchars(localiser($plat, 'nom')); ?>">
                        </td>
                        <td><?php echo htmlspecialchars(localiser($plat, 'nom')); ?></td>
                        <td><?php echo number_format($plat['prix'], 2); ?> DH</td>
                        <td><?php echo $plat['quantite']; ?></td>
                        <td><?php echo number_format($plat['sous_total'], 2); ?> DH</td>
                        <td class="actions-cell">
                            <a href="<?php echo BASE_URL; ?>/index.php?route=client/panier&moins=<?php echo $plat['id']; ?>" class="btn btn-outline btn-sm">-</a>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=client/panier&plus=<?php echo $plat['id']; ?>" class="btn btn-outline btn-sm">+</a>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=client/panier&supprimer=<?php echo $plat['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce plat du panier ?')" data-i18n="common.supprimer">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px; flex-wrap: wrap; gap: 12px;">
            <div>
                <a href="<?php echo BASE_URL; ?>/index.php?route=client/panier&vider=1" class="btn btn-danger" onclick="return confirm('Voulez-vous vider le panier ?')" data-i18n="common.viderPanier">Vider le panier</a>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1.2rem; font-weight: 700; color: var(--gold-dark);">
                    <span data-i18n="common.total">Total :</span> <?php echo number_format($total, 2); ?> DH
                </div>
                <a href="<?php echo BASE_URL; ?>/index.php?route=client/commander" class="btn btn-gold" style="margin-top: 8px;" data-i18n="common.commander">Commander</a>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="panel">
        <div class="empty-state">
            <span data-i18n="panier.vide">Votre panier est vide.</span>
            <br><br>
            <a href="<?php echo BASE_URL; ?>/index.php?route=client" class="btn btn-gold" data-i18n="common.consulterMenu">Consulter le menu</a>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php require ROOT_PATH . '/assets/inc/client_footer.php'; ?>
