<?php
$pageTitle = APP_NAME . " - Repas faits maison, livrés chez vous";
$extraCss = ['auth.css', 'admin.css'];
require ROOT_PATH . '/assets/inc/header.php';

$jourLabels = [
    'lundi' => 'Lundi', 'mardi' => 'Mardi', 'mercredi' => 'Mercredi',
    'jeudi' => 'Jeudi', 'vendredi' => 'Vendredi',
];
?>

<nav class="navbar navbar-expand-lg fiajou-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo BASE_URL; ?>/index.php?route=accueil">
            <?php echo APP_NAME; ?>
        </a>
        <div>
            <a href="<?php echo BASE_URL; ?>/index.php?route=connexion" class="btn btn-outline btn-sm">Connexion</a>
            <a href="<?php echo BASE_URL; ?>/index.php?route=inscription" class="btn btn-gold btn-sm">Inscription</a>
        </div>
    </div>
</nav>

<!-- Hero -->
<div style="background: linear-gradient(135deg, var(--gold-dark, #b8860b), #8a6d3b); color: #fff; padding: 64px 24px; text-align: center;">
    <h1 style="font-size: 2.4rem; font-weight: 700; margin-bottom: 12px;">Des repas faits maison, livrés chez vous</h1>
    <p style="font-size: 1.1rem; opacity: 0.9; max-width: 600px; margin: 0 auto 24px;">
        Découvrez notre menu de la semaine et commandez en quelques clics.
    </p>
    <a href="<?php echo BASE_URL; ?>/index.php?route=inscription" class="btn btn-gold" style="padding: 10px 28px;">
        Commencer à commander
    </a>
</div>

<div style="max-width: 1000px; margin: 0 auto; padding: 32px 16px;">

    <div class="panel">
        <h2>Menu de la semaine</h2>

        <?php if (!$menu): ?>
            <div class="empty-state">Aucun menu n'est publié pour le moment. Revenez bientôt !</div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-top: 12px;">
                <?php foreach ($jourLabels as $jourKey => $jourLabel): ?>
                    <div style="border: 1px solid var(--border); border-radius: 12px; padding: 14px;">
                        <div style="font-weight: 700; color: var(--gold-dark); margin-bottom: 8px;"><?php echo $jourLabel; ?></div>
                        <?php if (empty($itemsParJour[$jourKey])): ?>
                            <div style="font-size: 0.85rem; color: #aaa;">—</div>
                        <?php else: ?>
                            <?php foreach ($itemsParJour[$jourKey] as $item): ?>
                                <div style="font-size: 0.85rem; margin-bottom: 4px;"><?php echo htmlspecialchars($item['plat_nom']); ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <p style="margin-top: 20px; text-align: center;">
            <a href="<?php echo BASE_URL; ?>/index.php?route=inscription" class="btn btn-gold">Créer un compte pour commander</a>
        </p>
    </div>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
