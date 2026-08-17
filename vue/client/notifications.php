<?php
$pageTitle = "Notifications - " . APP_NAME;
$extraCss = ['admin.css', 'profile-menu.css', 'client-public.css'];
$bodyClass = 'client-public-layout';
$i18nPage = 'notifications';
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/client_navbar.php';

$prenomNotif = trim((string) ($_SESSION['prenom'] ?? ''));
$nomNotif    = trim((string) ($_SESSION['nom'] ?? ''));
$emailNotif  = (string) ($_SESSION['email'] ?? '');
$initialesNotif = mb_strtoupper(mb_substr($prenomNotif, 0, 1) . mb_substr($nomNotif, 0, 1));
if ($initialesNotif === '') {
    $initialesNotif = '?';
}
?>

<div class="page-profil">

    <?php require ROOT_PATH . '/assets/inc/back_home.php'; ?>

    <div class="topbar">
        <h1 data-i18n="notifications.titre">Notifications</h1>
        <div class="topbar-actions">
            <?php if ($nbNonLues > 0): ?>
                <p class="profil-subtitle"><strong><?php echo (int) $nbNonLues; ?></strong> <span data-i18n="notifications.nonLues">notification(s) non lue(s).</span></p>
                <a href="<?php echo BASE_URL; ?>/index.php?route=client/notifications&marquer_tout_lu=1" class="btn btn-outline btn-sm" data-i18n="notifications.toutLu">Tout marquer comme lu</a>
            <?php else: ?>
                <p class="profil-subtitle" data-i18n="notifications.aucuneNonLue">Vous n'avez aucune notification non lue.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="profil-hero">
        <span class="profil-hero-avatar"><?php echo htmlspecialchars($initialesNotif); ?></span>
        <div class="profil-hero-info">
            <strong><?php echo htmlspecialchars(trim($prenomNotif . ' ' . $nomNotif)); ?></strong>
            <span><?php echo htmlspecialchars($emailNotif); ?></span>
        </div>
    </div>

    <div class="panel profil-card">
        <?php if (!empty($notifications)): ?>
            <?php foreach ($notifications as $n): ?>
            <div class="notif-item<?php echo $n['est_lu'] ? '' : ' notif-unread'; ?>"
                 style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px;">
                <div style="min-width:0;">
                    <div class="notif-title">
                        <i data-lucide="bell" aria-hidden="true"></i>
                        <?php echo render_i18n($n['titre']); ?>
                    </div>
                    <div class="notif-msg"><?php echo render_i18n($n['message']); ?></div>
                    <small><?php echo htmlspecialchars($n['date_notification']); ?></small>
                </div>
                <?php if (!$n['est_lu']): ?>
                    <a href="<?php echo BASE_URL; ?>/index.php?route=client/notifications&marquer_lu=<?php echo (int) $n['id']; ?>"
                       class="btn btn-outline btn-sm" style="white-space:nowrap;" data-i18n="notifications.lu">Lu</a>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state" data-i18n="notifications.aucune">Aucune notification.</div>
        <?php endif; ?>
    </div>

</div>

<?php require ROOT_PATH . '/assets/inc/client_footer.php'; ?>
