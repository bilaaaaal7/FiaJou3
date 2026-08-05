<?php
$pageTitle = "Notifications - " . APP_NAME;
$extraCss = ['admin.css'];

// Page partagée par tous les rôles : la mise en page « profil » (sans sidebar)
// n'est appliquée que dans l'espace client. Les autres rôles conservent leur sidebar.
$estClientNotif = utilisateur_role() === ROLE_CLIENT;
if ($estClientNotif) {
    $bodyClass = 'profil-sans-sidebar';
}
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';

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
        <h1>Notifications</h1>
        <div class="topbar-actions">
            <?php if ($nbNonLues > 0): ?>
                <p class="profil-subtitle"><?php echo (int) $nbNonLues; ?> notification(s) non lue(s).</p>
                <a href="<?php echo BASE_URL; ?>/index.php?route=client/notifications&marquer_tout_lu=1" class="btn btn-outline btn-sm">Tout marquer comme lu</a>
            <?php else: ?>
                <p class="profil-subtitle">Vous n'avez aucune notification non lue.</p>
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
                        <?php echo htmlspecialchars($n['titre']); ?>
                    </div>
                    <div class="notif-msg"><?php echo htmlspecialchars($n['message']); ?></div>
                    <small><?php echo htmlspecialchars($n['date_notification']); ?></small>
                </div>
                <?php if (!$n['est_lu']): ?>
                    <a href="<?php echo BASE_URL; ?>/index.php?route=client/notifications&marquer_lu=<?php echo (int) $n['id']; ?>"
                       class="btn btn-outline btn-sm" style="white-space:nowrap;">Lu</a>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">Aucune notification.</div>
        <?php endif; ?>
    </div>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
