<?php
$pageTitle = "Notifications - " . APP_NAME;
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<div style="max-width:800px; margin:0 auto;">

    <div class="topbar">
        <h1>Notifications</h1>
        <?php if ($nbNonLues > 0): ?>
        <a href="<?php echo BASE_URL; ?>/index.php?route=client/notifications&marquer_tout_lu=1" class="btn btn-outline btn-sm">Tout marquer comme lu</a>
        <?php endif; ?>
    </div>

    <?php if ($nbNonLues > 0): ?>
    <p style="color:var(--text-muted); margin-bottom:16px;"><?php echo $nbNonLues; ?> notification(s) non lue(s).</p>
    <?php endif; ?>

    <div class="panel" style="padding:0;">
        <?php if (!empty($notifications)): ?>
            <?php foreach ($notifications as $n): ?>
            <div style="padding:16px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:start; gap:12px; <?php echo !$n['est_lu'] ? 'background:var(--gold-light);' : ''; ?>">
                <div>
                    <div style="font-weight:600; margin-bottom:4px;">
                        <?php if (!$n['est_lu']): ?>
                            <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:var(--gold-dark); margin-right:6px;"></span>
                        <?php endif; ?>
                        <?php echo htmlspecialchars($n['titre']); ?>
                    </div>
                    <div style="color:var(--text-muted); font-size:0.9rem; margin-bottom:4px;"><?php echo htmlspecialchars($n['message']); ?></div>
                    <div style="color:#aaa; font-size:0.8rem;"><?php echo $n['date_notification']; ?></div>
                </div>
                <?php if (!$n['est_lu']): ?>
                <a href="<?php echo BASE_URL; ?>/index.php?route=client/notifications&marquer_lu=<?php echo $n['id']; ?>" class="btn btn-outline btn-sm" style="white-space:nowrap;">Lu</a>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state" style="padding:40px;">Aucune notification.</div>
        <?php endif; ?>
    </div>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
