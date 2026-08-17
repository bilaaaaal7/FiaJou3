<?php
$pageTitle = htmlspecialchars(localiser($plat, 'nom')) . " - " . APP_NAME;
$extraCss = ['admin.css', 'profile-menu.css', 'client-public.css'];
$bodyClass = 'client-public-layout';
$i18nPage = 'produit';
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/client_navbar.php';
?>

<div style="max-width: 900px; margin: 0 auto;">

    <?php require ROOT_PATH . '/assets/inc/back_home.php'; ?>

    <?php if (isset($_GET['erreur']) && $_GET['erreur'] === 'indisponible'): ?>
        <div class="alert alert-danger py-2" role="alert" style="margin-top: 12px;" data-i18n="produit.erreurIndisponible">Ce plat n'est plus disponible ou la quantité maximale (20) est atteinte.</div>
    <?php endif; ?>
    <?php if (isset($_GET['erreur']) && in_array($_GET['erreur'], ['horsmenu', 'fermee'], true)): ?>
        <div class="alert alert-danger py-2" role="alert" style="margin-top: 12px;">
            <?php if ($_GET['erreur'] === 'fermee'): ?>
                <span data-i18n="produit.erreurCloturees">Les commandes pour cette date sont clôturées (limite</span>
                <strong><?php echo HEURE_LIMITE_COMMANDE; ?></strong>
                <span data-i18n="produit.erreurClotureesFin">la veille).</span>
            <?php else: ?>
                <span data-i18n="produit.erreurHorsMenu">Ce plat ne fait pas partie du menu de la semaine publié : il est disponible uniquement en consultation.</span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="panel" style="margin-top: 16px; display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start;">
        <img src="<?php echo UPLOADS_URL; ?>/<?php echo htmlspecialchars($plat['image']); ?>"
             alt="<?php echo htmlspecialchars(localiser($plat, 'nom')); ?>"
             style="width: 100%; border-radius: 12px; object-fit: cover; max-height: 360px; <?php echo $plat['disponible'] ? '' : 'opacity: 0.5;'; ?>">

        <div>
            <?php if ($categorie): ?>
                <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">
                    <?php echo htmlspecialchars(localiser($categorie, 'nom')); ?>
                </div>
            <?php endif; ?>

            <h1 style="margin: 4px 0 12px;"><?php echo htmlspecialchars(localiser($plat, 'nom')); ?></h1>

            <p style="color: var(--text-muted); line-height: 1.6;">
                <?php echo nl2br(htmlspecialchars(localiser($plat, 'description'))); ?>
            </p>

            <div style="font-size: 1.6rem; font-weight: 700; color: var(--gold-dark); margin: 16px 0;">
                <?php echo number_format($plat['prix'], 2); ?> DH
            </div>

            <div style="margin-bottom: 16px;">
                <?php if ($plat['disponible']): ?>
                    <span class="badge-status st-confirmee" data-i18n="produit.disponible">Disponible</span>
                <?php else: ?>
                    <span class="badge-status st-annulee" data-i18n="produit.indisponible">Indisponible</span>
                <?php endif; ?>
            </div>

            <?php if ($plat['disponible'] && $dateCommande): ?>
                <a href="<?php echo BASE_URL; ?>/index.php?route=client&ajouter=<?php echo $plat['id']; ?>&date=<?php echo $dateCommande; ?>"
                   class="btn btn-gold" style="padding: 10px 24px;">
                    <span data-i18n="produit.ajouterPanier">Ajouter au panier — livraison le</span>
                    <strong><?php echo date('d/m/Y', strtotime($dateCommande)); ?></strong>
                </a>
            <?php elseif ($plat['disponible']): ?>
                <span class="btn btn-outline" style="padding: 10px 24px; opacity: 0.7; cursor: not-allowed;" data-i18n="produit.consultationSeule">
                    Consultation uniquement — hors menu de la semaine
                </span>
            <?php else: ?>
                <span class="btn btn-outline" style="padding: 10px 24px; opacity: 0.6; cursor: not-allowed;" data-i18n="produit.indisponibleMoment">
                    Indisponible pour le moment
                </span>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/assets/inc/client_footer.php'; ?>
