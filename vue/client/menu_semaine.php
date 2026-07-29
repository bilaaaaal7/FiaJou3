<?php
$pageTitle = "Menu de la semaine - " . APP_NAME;
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<div style="max-width: 1100px; margin: 0 auto;">

    <div class="topbar">
        <h1>Menu de la semaine</h1>
    </div>

    <?php if ($menu): ?>
        <div class="panel" style="margin-bottom: 26px;">
            <h2><?php echo htmlspecialchars($menu['nom']); ?></h2>
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
            'samedi'   => 'Samedi',
            'dimanche' => 'Dimanche',
        ];
        ?>

        <?php foreach ($joursAffichage as $cle => $label): ?>
            <?php if (!empty($itemsParJour[$cle])): ?>
            <div class="panel">
                <h2><?php echo $label; ?></h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
                    <?php foreach ($itemsParJour[$cle] as $item): ?>
                    <div style="background: #fff; border: 1px solid var(--border); border-radius: 12px; overflow: hidden;">
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
                            <?php if ($item['disponible']): ?>
                                <a href="<?php echo BASE_URL; ?>/index.php?route=client&ajouter=<?php echo (int) $item['product_id']; ?>"
                                   class="btn btn-gold btn-sm btn-block" style="margin-top: 8px;">Ajouter au panier</a>
                            <?php else: ?>
                                <span class="badge-status st-annulee" style="margin-top: 8px; display: inline-block;">Indisponible</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>

    <?php else: ?>
        <div class="panel">
            <div class="empty-state">
                Aucun menu de la semaine n'est publié pour le moment.
                <br><br>
                <a href="<?php echo BASE_URL; ?>/index.php?route=client" class="btn btn-gold">Consulter le menu</a>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
