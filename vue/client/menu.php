<?php
$pageTitle = "Menu - " . APP_NAME;
$extraCss = ['admin.css'];
$bodyClass = 'profil-sans-sidebar';
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';

$messagesErreur = [
    'indisponible' => 'Ce plat n\'est plus disponible ou la quantité maximale (20) est atteinte.',
    'horsmenu'     => 'Ce plat ne fait pas partie du menu de la semaine publié : il est disponible uniquement en consultation.',
    'fermee'       => 'Les commandes pour cette date sont clôturées (limite ' . HEURE_LIMITE_COMMANDE . ' la veille).',
    'quantite_max' => 'La quantité maximale (20) est atteinte pour ce plat.',
];
$jourLabel = [
    'lundi' => 'Lundi', 'mardi' => 'Mardi', 'mercredi' => 'Mercredi',
    'jeudi' => 'Jeudi', 'vendredi' => 'Vendredi', 'dimanche' => 'Dimanche',
];
?>

<div style="max-width: 1100px; margin: 0 auto;">

    <?php require ROOT_PATH . '/assets/inc/back_home.php'; ?>

    <div class="topbar">
        <h1>Notre Menu</h1>
    </div>

    <?php if (isset($_GET['erreur']) && isset($messagesErreur[$_GET['erreur']])): ?>
        <div class="alert alert-danger py-2" role="alert"><?php echo htmlspecialchars($messagesErreur[$_GET['erreur']]); ?></div>
    <?php endif; ?>

    <?php if ($panierModele->getDate()): ?>
        <div class="alert alert-info py-2" role="alert">
            Date de livraison du panier : <strong><?php echo htmlspecialchars(date('d/m/Y', strtotime($panierModele->getDate()))); ?></strong>
        </div>
    <?php endif; ?>

    <?php if ($menu && array_sum(array_map('count', $itemsParJour)) > 0): ?>
    <div class="panel" style="border: 2px solid var(--gold);">
        <h2 style="color: var(--gold-dark);">Menu de la semaine — <?php echo htmlspecialchars($menu['nom']); ?></h2>
        <?php if ($libelleSemaine !== ''): ?>
            <p style="color: var(--gold-dark); font-weight: 600; margin: 0 0 4px;">
                <?php echo htmlspecialchars($libelleSemaine); ?>
            </p>
        <?php endif; ?>
        <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 16px;">
            Commandez avant <?php echo HEURE_LIMITE_COMMANDE; ?> pour une livraison le lendemain (7j/7).
            Le samedi, le menu est libre : tous les plats de la semaine sont commandables.
        </p>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
            <?php foreach (JOURS_MENU as $jour): ?>
                <?php if (empty($itemsParJour[$jour])): continue; endif; ?>
                <?php
                $dateJour = $menuSemaineModele->prochaineDatePourJour($jour);
                [$jourOuvert] = $dateJour ? $menuSemaineModele->dateLivraisonValide($dateJour) : [false];
                ?>
                <div style="background: var(--gold-light); border: 1px solid var(--border); border-radius: 12px; padding: 12px;">
                    <div style="font-weight: 700; color: var(--gold-dark); margin-bottom: 8px;"><?php echo $jourLabel[$jour]; ?></div>
                    <?php foreach ($itemsParJour[$jour] as $item): ?>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                            <div style="flex: 1;">
                                <div style="font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($item['plat_nom']); ?></div>
                                <div style="font-size: 0.78rem; color: var(--text-muted);">
                                    <?php echo htmlspecialchars($item['categorie'] ?? ''); ?> · <?php echo number_format((float) $item['prix'], 2, ',', ' '); ?> DH
                                </div>
                            </div>
                            <?php if ($item['disponible'] && $jourOuvert): ?>
                                <a href="<?php echo BASE_URL; ?>/index.php?route=client&ajouter=<?php echo (int) $item['product_id']; ?>&date=<?php echo $dateJour; ?>"
                                   class="btn btn-gold btn-sm" title="Livraison le <?php echo date('d/m/Y', strtotime($dateJour)); ?>">+</a>
                            <?php else: ?>
                                <span class="badge-status st-annulee">
                                    <?php echo $jourOuvert ? 'Indisponible' : 'Clôturé'; ?>
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
        $dateSamedi = $menuSemaineModele->prochaineDatePourJour(JOUR_MENU_LIBRE);
        [$samediOuvert] = $dateSamedi ? $menuSemaineModele->dateLivraisonValide($dateSamedi) : [false];
        ?>
        <?php if (!empty($itemsSamedi)): ?>
        <div style="margin-top: 16px; background: var(--gold); border-radius: 12px; padding: 16px; color: var(--dark);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 10px;">
                <div style="font-weight: 700; font-size: 1.05rem;">Samedi — Menu libre</div>
                <?php if ($samediOuvert): ?>
                    <div style="font-size: 0.82rem; font-weight: 600;">
                        Livraison le <?php echo date('d/m/Y', strtotime($dateSamedi)); ?>
                    </div>
                <?php endif; ?>
            </div>
            <p style="font-size: 0.82rem; margin: 0 0 10px; opacity: 0.85;">
                Aucun menu spécifique le samedi : choisissez librement parmi tous les plats de la semaine.
            </p>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px;">
                <?php foreach ($itemsSamedi as $item): ?>
                    <div style="background: var(--surface); border-radius: 10px; padding: 10px; display: flex; align-items: center; gap: 8px;">
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-weight: 600; font-size: 0.88rem;"><?php echo htmlspecialchars($item['plat_nom']); ?></div>
                            <div style="font-size: 0.78rem; color: var(--text-muted);">
                                <?php echo htmlspecialchars($item['categorie'] ?? ''); ?> · <?php echo number_format((float) $item['prix'], 2, ',', ' '); ?> DH
                            </div>
                        </div>
                        <?php if ($item['disponible'] && $samediOuvert): ?>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=client&ajouter=<?php echo (int) $item['product_id']; ?>&date=<?php echo $dateSamedi; ?>"
                               class="btn btn-sm" style="background: var(--dark); color: var(--cream); border: none;" title="Livraison le samedi <?php echo date('d/m/Y', strtotime($dateSamedi)); ?>">+</a>
                        <?php else: ?>
                            <span class="badge-status st-annulee">Indisponible</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
