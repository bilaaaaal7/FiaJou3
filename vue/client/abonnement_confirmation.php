<?php
$pageTitle = "Confirmation - " . APP_NAME;
$extraCss = ['admin.css', 'profile-menu.css', 'client-public.css'];
$bodyClass = 'client-public-layout';
$i18nPage = 'abonnement';
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/client_navbar.php';
?>

<div style="max-width: 620px; margin: 0 auto;">

    <?php require ROOT_PATH . '/assets/inc/back_home.php'; ?>

    <div class="panel" style="border: 2px solid var(--gold); text-align: center; padding: 40px 28px;">

        <div style="width: 86px; height: 86px; margin: 0 auto 20px; border-radius: 50%; background: linear-gradient(135deg, #c9952a, var(--gold) 50%, #8a5f10); display:flex; align-items:center; justify-content:center; box-shadow: 0 10px 28px rgba(184,134,24,0.4);">
            <i data-lucide="check" aria-hidden="true" style="width: 44px; height: 44px; color: #fff;"></i>
        </div>

        <h2 style="color: var(--gold-dark); margin: 0 0 10px; font-size: 1.5rem;" data-i18n="abonnement.confirmationTitre">Paiement effectué avec succès !</h2>
        <p style="color: var(--text); font-size: 1rem; margin: 0 0 6px;" data-i18n="abonnement.confirmationTexte">Votre abonnement Premium est maintenant actif.</p>

        <?php if ($abonnement): ?>
        <p style="color: var(--text-muted); font-size: 0.88rem; margin: 4px 0 20px;">
            <span data-i18n="abonnement.du">Valide du</span>
            <strong><?php echo date('d/m/Y', strtotime($abonnement['date_debut'])); ?></strong>
            <span data-i18n="abonnement.au">au</span>
            <strong><?php echo date('d/m/Y', strtotime($abonnement['date_fin'])); ?></strong>
        </p>
        <p style="color: var(--text-muted); font-size: 0.82rem; margin: -10px 0 20px; font-style: italic;">
            (<span data-i18n="abonnement.confirmationSandbox">Mode test : aucun débit bancaire réel n'a eu lieu.</span>)
        </p>
        <?php endif; ?>

        <div style="display:flex; justify-content:center; gap:10px; flex-wrap:wrap; margin-top: 12px;">
            <a href="<?php echo BASE_URL; ?>/index.php?route=accueil" class="btn btn-gold" style="padding: 12px 34px; font-size: 1rem; text-decoration: none;" data-i18n="common.retourAccueil">Retour à l'accueil</a>
            <a href="<?php echo BASE_URL; ?>/index.php?route=client/abonnement" class="btn btn-outline" style="padding: 12px 24px; text-decoration: none;" data-i18n="nav.abonnement">Mes abonnements</a>
        </div>

    </div>

</div>

<?php require ROOT_PATH . '/assets/inc/client_footer.php'; ?>
