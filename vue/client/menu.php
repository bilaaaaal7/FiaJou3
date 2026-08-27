<?php
$pageTitle = "Menu - " . APP_NAME;
$extraCss = ['admin.css', 'profile-menu.css', 'client-public.css'];
$bodyClass = 'client-public-layout';
$i18nPage = 'menu';
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/client_navbar.php';

$messagesErreur = [
    'indisponible' => 'Ce plat n\'est plus disponible ou la quantité maximale (20) est atteinte.',
    'horsmenu'     => 'Ce plat ne fait pas partie du menu de la semaine publié : il est disponible uniquement en consultation.',
    'fermee'       => 'Les commandes pour cette date sont clôturées (limite ' . HEURE_LIMITE_COMMANDE . ' la veille).',
    'quantite_max' => 'La quantité maximale (20) est atteinte pour ce plat.',
];
$erreurI18n = [
    'indisponible' => 'produit.erreurIndisponible',
    'horsmenu'     => 'produit.erreurHorsMenu',
    'quantite_max' => 'produit.erreurQuantiteMax',
];
$jourLabel = [
    'lundi' => 'Lundi', 'mardi' => 'Mardi', 'mercredi' => 'Mercredi',
    'jeudi' => 'Jeudi', 'vendredi' => 'Vendredi',
];
?>

<div style="max-width: 1100px; margin: 0 auto;">

    <?php require ROOT_PATH . '/assets/inc/back_home.php'; ?>

    <div class="topbar">
        <h1 data-i18n="menu.titre">Notre Menu</h1>
    </div>

    <?php if (isset($_GET['erreur']) && isset($messagesErreur[$_GET['erreur']])): ?>
        <?php if ($_GET['erreur'] === 'fermee'): ?>
            <div class="alert alert-danger py-2" role="alert">
                <span data-i18n="produit.erreurCloturees">Les commandes pour cette date sont clôturées (limite</span>
                <strong><?php echo HEURE_LIMITE_COMMANDE; ?></strong>
                <span data-i18n="produit.erreurClotureesFin">la veille).</span>
            </div>
        <?php else: ?>
            <div class="alert alert-danger py-2" role="alert" data-i18n="<?php echo htmlspecialchars($erreurI18n[$_GET['erreur']] ?? ''); ?>"><?php echo htmlspecialchars($messagesErreur[$_GET['erreur']]); ?></div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($panierModele->nombreArticles() > 0): ?>
        <div class="alert alert-info py-2" role="alert">
            <span data-i18n="common.nbArticlesPanier">Articles dans le panier :</span> <strong><?php echo $panierModele->nombreArticles(); ?></strong>
            <a href="<?php echo BASE_URL; ?>/index.php?route=client/panier" style="color: var(--gold-dark); margin-left: 8px;" data-i18n="common.voirPanier">Voir le panier</a>
        </div>
    <?php endif; ?>

    <?php if ($menu && array_sum(array_map('count', $itemsParJour)) > 0): ?>
    <div class="panel" style="border: 2px solid var(--gold);">
        <h2 style="color: var(--gold-dark);"><span data-i18n="nav.menuSemaine">Menu de la semaine</span> — <?php echo htmlspecialchars($menu['nom']); ?></h2>
        <?php if ($libelleSemaine !== ''): ?>
            <p style="color: var(--gold-dark); font-weight: 600; margin: 0 0 4px;">
                <?php echo htmlspecialchars($libelleSemaine); ?>
            </p>
        <?php endif; ?>
        <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 16px;">
            <span data-i18n="menu.infoCmdAvant">Commandez avant</span> <?php echo HEURE_LIMITE_COMMANDE; ?>
            <span data-i18n="menu.infoCmdAvantFin">pour une livraison le lendemain (lundi-samedi).</span>
            <span data-i18n="commander.livraisonInfoFin">Le samedi, le menu est libre : tous les plats de la semaine sont commandables.</span>
        </p>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
            <?php foreach (JOURS_MENU as $jour): ?>
                <?php if (empty($itemsParJour[$jour])): continue; endif; ?>
                <div style="background: var(--gold-light); border: 1px solid var(--border); border-radius: 12px; padding: 12px;">
                    <div style="font-weight: 700; color: var(--gold-dark); margin-bottom: 8px;" data-i18n="jours.<?php echo $jour; ?>"><?php echo $jourLabel[$jour]; ?></div>
                    <?php foreach ($itemsParJour[$jour] as $item): ?>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                            <div style="flex: 1;">
                                <div style="font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($item['plat_nom']); ?></div>
                                <div style="font-size: 0.78rem; color: var(--text-muted);">
                                    <?php echo htmlspecialchars($item['categorie'] ?? ''); ?> · <?php echo number_format((float) $item['prix'], 2, ',', ' '); ?> DH
                                </div>
                            </div>
                            <?php if ($item['disponible'] && !empty($ouvertParJour[$jour])): ?>
                                <a href="<?php echo BASE_URL; ?>/index.php?route=client&ajouter=<?php echo (int) $item['product_id']; ?>"
                                   class="btn btn-gold btn-sm" data-i18n-aria="common.ajouterPanier">+</a>
                            <?php else: ?>
                                <span class="badge-status st-annulee" data-i18n="<?php echo ($ouvertParJour[$jour] ?? true) ? 'common.indisponible' : 'common.cloture'; ?>">
                                    <?php echo ($ouvertParJour[$jour] ?? true) ? 'Indisponible' : 'Clôturé'; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php
        $itemsSamedi = [];
        foreach (JOURS_MENU as $jour) {
            foreach (($itemsParJour[$jour] ?? []) as $item) {
                $itemsSamedi[$item['product_id']] = $item;
            }
        }
        ?>
        <?php if (!empty($itemsSamedi)): ?>
        <div style="margin-top: 16px; background: var(--gold); border-radius: 12px; padding: 16px; color: var(--dark);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 10px;">
                <div style="font-weight: 700; font-size: 1.05rem;" data-i18n="jours.samediMenuLibre">Samedi — Menu libre</div>
            </div>
            <p style="font-size: 0.82rem; margin: 0 0 10px; opacity: 0.85;" data-i18n="accueil.samediDesc">Aucun menu spécifique le samedi : choisissez librement parmi tous les plats de la semaine.</p>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px;">
                <?php foreach ($itemsSamedi as $item): ?>
                    <div style="background: var(--surface); border-radius: 10px; padding: 10px; display: flex; align-items: center; gap: 8px;">
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-weight: 600; font-size: 0.88rem;"><?php echo htmlspecialchars($item['plat_nom']); ?></div>
                            <div style="font-size: 0.78rem; color: var(--text-muted);">
                                <?php echo htmlspecialchars($item['categorie'] ?? ''); ?> · <?php echo number_format((float) $item['prix'], 2, ',', ' '); ?> DH
                            </div>
                        </div>
                        <?php if ($item['disponible'] && !empty($ouvertParJour[JOUR_MENU_LIBRE])): ?>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=client&ajouter=<?php echo (int) $item['product_id']; ?>"
                               class="btn btn-sm" style="background: var(--dark); color: var(--cream); border: none;" data-i18n-aria="common.ajouterPanier">+</a>
                        <?php else: ?>
                            <span class="badge-status st-annulee" data-i18n="<?php echo ($ouvertParJour[JOUR_MENU_LIBRE] ?? true) ? 'common.indisponible' : 'common.cloture'; ?>">
                                <?php echo ($ouvertParJour[JOUR_MENU_LIBRE] ?? true) ? 'Indisponible' : 'Clôturé'; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</div>

<?php require ROOT_PATH . '/assets/inc/client_footer.php'; ?>
