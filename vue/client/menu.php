<?php
$pageTitle = "Menu - " . APP_NAME;
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<div style="max-width: 1100px; margin: 0 auto;">

    <div class="topbar">
        <h1>Notre Menu</h1>
    </div>

    <?php if (isset($_GET['erreur']) && $_GET['erreur'] === 'indisponible'): ?>
        <div class="alert alert-danger py-2" role="alert">Ce plat n'est plus disponible ou la quantité maximale (20) est atteinte.</div>
    <?php endif; ?>

    <?php
    $categoriesGrouped = [];
    foreach ($plats as $plat) {
        $cat = $plat['categorie'];
        $categoriesGrouped[$cat][] = $plat;
    }
    ?>

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
                    <div style="font-size: 0.8rem; color: #8a8a8a; margin-top: 2px;">
                        <?php echo htmlspecialchars($plat['description'] ?? ''); ?>
                    </div>
                    <div style="margin-top: 8px; font-weight: 700; color: var(--gold-dark);">
                        <?php echo number_format($plat['prix'], 2); ?> DH
                    </div>
                    <?php if ($plat['disponible']): ?>
                        <a href="<?php echo BASE_URL; ?>/index.php?route=client&ajouter=<?php echo $plat['id']; ?>"
                           class="btn btn-gold btn-sm btn-block" style="margin-top: 8px; width: 100%; text-align: center;">
                            Ajouter au panier
                        </a>
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
