<?php
$pageTitle = "Menu - " . APP_NAME;
$extraCss = ['admin.css'];
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
    'jeudi' => 'Jeudi', 'vendredi' => 'Vendredi',
];
?>

<div style="max-width: 1100px; margin: 0 auto;">

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
        <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 16px;">
            Commandez avant <?php echo HEURE_LIMITE_COMMANDE; ?> pour une livraison le lendemain (lundi à vendredi).
        </p>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
            <?php foreach (JOURS_LIVRAISON as $jour): ?>
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
    </div>
    <?php endif; ?>

    <?php
    $categoriesGrouped = [];
    foreach ($plats as $plat) {
        $categoriesGrouped[$plat['categorie']][] = $plat;
    }
    ?>

    <div class="panel">
        <h2>Catalogue complet</h2>
        <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: -6px;">
            Tous nos produits sont consultables. Seuls ceux du menu de la semaine publié sont commandables.
        </p>
    </div>

    <?php foreach ($categoriesGrouped as $catNom => $catPlats): ?>
    <div class="panel">
        <h2><?php echo htmlspecialchars($catNom); ?></h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px;">
            <?php foreach ($catPlats as $plat): ?>
            <div style="background: #fff; border: 1px solid var(--border); border-radius: 12px; overflow: hidden;">
                <a href="<?php echo BASE_URL; ?>/index.php?route=client/produit&id=<?php echo $plat['id']; ?>">
                    <img src="<?php echo UPLOADS_URL; ?>/<?php echo htmlspecialchars($plat['image']); ?>"
                         alt="<?php echo htmlspecialchars($plat['nom']); ?>"
                         style="width: 100%; height: 140px; object-fit: cover; <?php echo $plat['disponible'] ? '' : 'opacity: 0.5;'; ?>">
                </a>
                <div style="padding: 12px;">
                    <div style="font-weight: 600; font-size: 0.95rem;">
                        <a href="<?php echo BASE_URL; ?>/index.php?route=client/produit&id=<?php echo $plat['id']; ?>" style="color: inherit; text-decoration: none;">
                            <?php echo htmlspecialchars($plat['nom']); ?>
                        </a>
                    </div>
                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 2px;">
                        <?php echo htmlspecialchars($plat['description'] ?? ''); ?>
                    </div>
                    <div style="margin-top: 8px; font-weight: 700; color: var(--gold-dark);">
                        <?php echo number_format($plat['prix'], 2); ?> DH
                    </div>
                    <?php if ($plat['disponible'] && !empty($dateCommandeParPlat[$plat['id']])): ?>
                        <a href="<?php echo BASE_URL; ?>/index.php?route=client&ajouter=<?php echo $plat['id']; ?>&date=<?php echo $dateCommandeParPlat[$plat['id']]; ?>"
                           class="btn btn-gold btn-sm btn-block" style="margin-top: 8px; width: 100%; text-align: center;">
                            Ajouter au panier
                        </a>
                    <?php elseif ($plat['disponible']): ?>
                        <span class="btn btn-outline btn-sm btn-block" style="margin-top: 8px; width: 100%; text-align: center; opacity: 0.7; cursor: not-allowed;">
                            Consultation uniquement
                        </span>
                    <?php else: ?>
                        <span class="btn btn-outline btn-sm btn-block" style="margin-top: 8px; width: 100%; text-align: center; opacity: 0.6; cursor: not-allowed;">
                            Indisponible
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>

    <?php if (empty($plats)): ?>
    <div class="panel">
        <div class="empty-state">Aucun plat disponible pour le moment.</div>
    </div>
    <?php endif; ?>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
