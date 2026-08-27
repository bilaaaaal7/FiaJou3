<?php
$pageTitle = "Abonnement - " . APP_NAME;
$extraCss = ['admin.css', 'profile-menu.css', 'client-public.css'];
$bodyClass = 'client-public-layout';
$i18nPage = 'abonnement';
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/client_navbar.php';
?>

<div style="max-width: 800px; margin: 0 auto;">

    <?php require ROOT_PATH . '/assets/inc/back_home.php'; ?>

    <div class="topbar">
        <h1 data-i18n="abonnement.titre">Abonnement mensuel</h1>
        <p class="profil-subtitle" data-i18n="abonnement.sousTitre">Souscrivez à un abonnement pour bénéficier d'avantages exclusifs.</p>
    </div>

    <?php if ($succes): ?>
        <div class="alert-box alert-success"><?php echo htmlspecialchars($succes); ?></div>
    <?php endif; ?>

    <?php if ($erreur): ?>
        <div class="alert-box alert-error"><?php echo htmlspecialchars($erreur); ?></div>
    <?php endif; ?>

    <?php if ($abonnement): ?>
    <div class="panel" style="border: 2px solid var(--gold);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div>
                <h2 style="color: var(--gold-dark); margin: 0;" data-i18n="abonnement.actif">Votre abonnement actif</h2>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin: 4px 0 0;">
                    <span data-i18n="abonnement.valide">Valide du</span>
                    <strong><?php echo date('d/m/Y', strtotime($abonnement['date_debut'])); ?></strong>
                    <span data-i18n="abonnement.au">au</span>
                    <strong><?php echo date('d/m/Y', strtotime($abonnement['date_fin'])); ?></strong>
                </p>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1.3rem; font-weight: 700; color: var(--gold-dark);">
                    <?php echo number_format((float) $abonnement['prix'], 2, ',', ' '); ?> DH
                    <span style="font-size: 0.8rem; font-weight: 400; color: var(--text-muted);">/mois</span>
                </div>
                <span class="badge-status st-confirmee" data-i18n="abonnement.statutActif">Actif</span>
            </div>
        </div>

        <div style="margin-top: 20px; padding: 16px; background: var(--gold-light); border-radius: 10px;">
            <h3 style="font-size: 0.95rem; color: var(--gold-dark); margin: 0 0 8px;" data-i18n="abonnement.avantagesTitre">Avantages de l'abonnement</h3>
            <ul style="margin: 0; padding-inline-start: 20px; font-size: 0.88rem; color: var(--text-muted); line-height: 1.8;">
                <li data-i18n="abonnement.avantage1">Remise de <?php echo REMISE_SEMAINE_POURCENT; ?>% sur les commandes de la semaine complète</li>
                <li data-i18n="abonnement.avantage2">Livraison prioritaire</li>
                <li data-i18n="abonnement.avantage3">Accès anticipé aux nouveaux plats</li>
            </ul>
        </div>

        <form method="POST" style="margin-top: 16px;">
            <input type="hidden" name="subscription_id" value="<?php echo (int) $abonnement['id']; ?>">
            <button type="submit" name="annuler" class="btn btn-outline" onclick="return confirm('Êtes-vous sûr de vouloir annuler votre abonnement ?')" data-i18n="abonnement.annuler">Annuler l'abonnement</button>
        </form>
    </div>

    <?php else: ?>
    <div class="panel" style="border: 2px solid var(--gold);">
        <div style="text-align: center; padding: 20px 0;">
            <div style="font-size: 2.5rem; margin-bottom: 12px;">
                <i data-lucide="crown" aria-hidden="true" style="width: 48px; height: 48px; color: var(--gold-dark);"></i>
            </div>
            <h2 style="color: var(--gold-dark); margin: 0 0 8px;" data-i18n="abonnement.offreTitre">Abonnement Premium</h2>
            <div style="font-size: 2rem; font-weight: 700; color: var(--gold-dark); margin: 16px 0;">
                <?php echo number_format($prix, 2, ',', ' '); ?> DH
                <span style="font-size: 0.9rem; font-weight: 400; color: var(--text-muted);">/mois</span>
            </div>
        </div>

        <div style="padding: 16px; background: var(--gold-light); border-radius: 10px; margin-bottom: 20px;">
            <h3 style="font-size: 0.95rem; color: var(--gold-dark); margin: 0 0 8px;" data-i18n="abonnement.avantagesTitre">Avantages inclus</h3>
            <ul style="margin: 0; padding-inline-start: 20px; font-size: 0.88rem; color: var(--text-muted); line-height: 1.8;">
                <li data-i18n="abonnement.avantage1">Remise de <?php echo REMISE_SEMAINE_POURCENT; ?>% sur les commandes de la semaine complète</li>
                <li data-i18n="abonnement.avantage2">Livraison prioritaire</li>
                <li data-i18n="abonnement.avantage3">Accès anticipé aux nouveaux plats</li>
            </ul>
        </div>

        <div style="text-align: center;">
            <a href="<?php echo BASE_URL; ?>/index.php?route=client/abonnement/paiement" class="btn btn-gold" style="padding: 12px 40px; font-size: 1.05rem; text-decoration: none;" data-i18n="abonnement.souscrire">Souscrire maintenant</a>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($abonnements)): ?>
    <div class="panel">
        <h2 style="font-size: 1rem; color: var(--gold-dark);" data-i18n="abonnement.historique">Historique des abonnements</h2>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th data-i18n="abonnement.dateDebut">Date début</th>
                        <th data-i18n="abonnement.dateFin">Date fin</th>
                        <th data-i18n="abonnement.prix">Prix</th>
                        <th data-i18n="abonnement.statut">Statut</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($abonnements as $sub): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($sub['date_debut'])); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($sub['date_fin'])); ?></td>
                        <td><?php echo number_format((float) $sub['prix'], 2, ',', ' '); ?> DH</td>
                        <td>
                            <span class="badge-status st-<?php echo $sub['statut'] === 'active' ? 'confirmee' : ($sub['statut'] === 'annule' ? 'annulee' : 'en_attente'); ?>">
                                <?php echo ucfirst($sub['statut']); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php require ROOT_PATH . '/assets/inc/client_footer.php'; ?>
