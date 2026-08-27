<?php
$pageTitle = "Menu de la semaine - " . APP_NAME;
$extraCss = ['admin.css', 'profile-menu.css', 'client-public.css'];
$bodyClass = 'client-public-layout';
$i18nPage = 'menu_semaine';
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/client_navbar.php';
?>

<div style="max-width: 1100px; margin: 0 auto;">

    <?php require ROOT_PATH . '/assets/inc/back_home.php'; ?>

    <div class="topbar">
        <h1 data-i18n="menu_semaine.titre">Menu de la semaine</h1>
    </div>

    <?php if ($menu): ?>
        <div class="panel" style="margin-bottom: 26px;">
            <h2><?php echo htmlspecialchars($menu['nom']); ?></h2>
            <?php if ($libelleSemaine !== ''): ?>
                <p style="color: var(--gold-dark); font-weight: 600; margin: 4px 0 0;">
                    <?php echo htmlspecialchars($libelleSemaine); ?>
                </p>
            <?php endif; ?>
            <p style="color: var(--text-muted); margin: 0;">
                Publié le <?php echo htmlspecialchars($menu['date_creation']); ?>
            </p>
        </div>

        <?php
        $joursAffichage = [
            'lundi'    => 'Lundi',
            'mardi'    => 'Mardi',
            'mercredi' => 'Mercredi',
            'jeudi'    => 'Jeudi',
            'vendredi' => 'Vendredi',
        ];
        ?>

        <?php if (isset($_GET['erreur']) && in_array($_GET['erreur'], ['horsmenu', 'fermee', 'indisponible'], true)): ?>
            <div class="alert alert-danger py-2" role="alert">
                <?php echo $_GET['erreur'] === 'fermee'
                    ? 'Les commandes pour cette date sont clôturées (limite ' . HEURE_LIMITE_COMMANDE . ' la veille).'
                    : ($_GET['erreur'] === 'horsmenu'
                        ? 'Ce plat ne fait pas partie du menu de la semaine publié.'
                        : 'Ce plat n\'est plus disponible ou la quantité maximale (20) est atteinte.'); ?>
            </div>
        <?php endif; ?>

        <?php foreach ($joursAffichage as $cle => $label): ?>
            <?php if (!empty($itemsParJour[$cle])): ?>
            <div class="panel">
                <h2><span data-i18n="jours.<?php echo $cle; ?>"><?php echo $label; ?></span>
                    <?php if (isset($datesParJour[$cle])): ?>
                        <span style="font-weight:400; color:var(--text-muted); font-size:0.85rem;">
                            — <span data-i18n="common.livraisonLe">livraison le</span> <?php echo date('d/m/Y', strtotime($datesParJour[$cle])); ?>
                        </span>
                    <?php endif; ?>
                </h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
                    <?php foreach ($itemsParJour[$cle] as $item): ?>
                    <div class="menu-week-card" style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; overflow: hidden;">
                        <img src="<?php echo UPLOADS_URL; ?>/<?php echo htmlspecialchars($item['image']); ?>"
                             alt="<?php echo htmlspecialchars($item['plat_nom']); ?>"
                             style="width: 100%; height: 140px; object-fit: cover;">
                        <div style="padding: 12px;">
                            <div style="font-weight: 600; font-size: 0.95rem;">
                                <?php echo htmlspecialchars($item['plat_nom']); ?>
                            </div>
                            <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 2px;">
                                <?php echo htmlspecialchars($item['categorie']); ?>
                            </div>
                            <div style="margin-top: 8px; font-weight: 700; color: var(--gold-dark);">
                                <?php echo number_format((float) $item['prix'], 2, ',', ' '); ?> DH
                            </div>
                            <?php if ($item['disponible'] && !empty($datesParJour[$cle]) && $ouvertParJour[$cle]): ?>
                                <a href="<?php echo BASE_URL; ?>/index.php?route=client&ajouter=<?php echo (int) $item['product_id']; ?>&date=<?php echo $datesParJour[$cle]; ?>"
                                   class="btn btn-gold btn-sm btn-block" style="margin-top: 8px;" data-i18n="common.ajouterPanier">Ajouter au panier</a>
                            <?php else: ?>
                                <span class="badge-status st-annulee" style="margin-top: 8px; display: inline-block;" data-i18n="<?php echo ($ouvertParJour[$cle] ?? true) ? 'common.indisponible' : 'common.commandesCloturees'; ?>">
                                    <?php echo ($ouvertParJour[$cle] ?? true) ? 'Indisponible' : 'Commandes clôturées'; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <div class="panel" style="border: 2px solid var(--gold);">
            <h2 style="color: var(--gold-dark);" data-i18n="jours.samediMenuLibre">Samedi — Menu libre</h2>
            <p style="color: var(--text-muted); margin-top: -8px;">
                Aucun menu spécifique le samedi : choisissez librement parmi tous les plats de la semaine.
            </p>
            <?php if (!empty($itemsSamedi)): ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
                    <?php foreach ($itemsSamedi as $item): ?>
                    <div class="menu-week-card" style="background: var(--gold-light); border: 1px solid var(--border); border-radius: 12px; padding: 12px;">
                        <div style="font-weight: 600; font-size: 0.95rem;"><?php echo htmlspecialchars($item['plat_nom']); ?></div>
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 2px;">
                            <?php echo htmlspecialchars($item['categorie']); ?>
                        </div>
                        <div style="margin-top: 8px; font-weight: 700; color: var(--gold-dark);">
                            <?php echo number_format((float) $item['prix'], 2, ',', ' '); ?> DH
                        </div>
                        <?php if ($item['disponible'] && !empty($datesParJour[JOUR_MENU_LIBRE]) && $ouvertParJour[JOUR_MENU_LIBRE]): ?>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=client&ajouter=<?php echo (int) $item['product_id']; ?>&date=<?php echo $datesParJour[JOUR_MENU_LIBRE]; ?>"
                               class="btn btn-gold btn-sm btn-block" style="margin-top: 8px;"
                               title="Livraison le samedi <?php echo date('d/m/Y', strtotime($datesParJour[JOUR_MENU_LIBRE])); ?>" data-i18n="common.ajouterPanier">Ajouter au panier</a>
                        <?php else: ?>
                            <span class="badge-status st-annulee" style="margin-top: 8px; display: inline-block;" data-i18n="<?php echo ($ouvertParJour[JOUR_MENU_LIBRE] ?? true) ? 'common.indisponible' : 'common.commandesCloturees'; ?>">
                                <?php echo ($ouvertParJour[JOUR_MENU_LIBRE] ?? true) ? 'Indisponible' : 'Commandes clôturées'; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color:var(--text-muted); padding:10px 0;" data-i18n="menu_semaine.videTexte">Aucun plat dans le menu de la semaine pour le moment.</p>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <div class="panel">
            <div class="empty-state">
                Aucun menu de la semaine n'est publié pour le moment.
                <br><br>
                <a href="<?php echo BASE_URL; ?>/index.php?route=client" class="btn btn-gold" data-i18n="common.consulterMenu">Consulter le menu</a>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php require ROOT_PATH . '/assets/inc/client_footer.php'; ?>
