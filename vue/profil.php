<?php
$pageTitle = "Mon profil - " . APP_NAME;
$pageHeading = "Mon profil";
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';

$prenom = trim((string) ($profil['prenom'] ?? ''));
$nom    = trim((string) ($profil['nom'] ?? ''));
$initiales = mb_strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1));
if ($initiales === '') {
    $initiales = '?';
}

$roleLabels = [
    ROLE_ADMIN     => 'Administrateur',
    ROLE_CLIENT    => 'Client',
    ROLE_CUISINIER => 'Cuisinier',
    ROLE_LIVREUR   => 'Livreur',
];
$roleLabel = $roleLabels[$profil['role'] ?? ''] ?? ($profil['role'] ?? '');
?>

<div style="max-width: 800px; margin: 0 auto;">

    <div class="panel">
        <div style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">
            <span class="avatar avatar-lg" style="width:64px; height:64px; font-size:1.5rem;"><?php echo htmlspecialchars($initiales); ?></span>

            <div style="flex:1; min-width:200px;">
                <div style="font-size:1.25rem; font-weight:700; color:var(--text);"><?php echo htmlspecialchars(trim($prenom . ' ' . $nom)); ?></div>
                <div style="color:var(--text-muted); font-size:0.9rem; margin-top:4px;"><?php echo htmlspecialchars($roleLabel); ?></div>
                <div style="color:var(--text-muted); font-size:0.9rem;"><?php echo htmlspecialchars($profil['email'] ?? ''); ?></div>
            </div>

            <a href="<?php echo BASE_URL; ?>/index.php?route=parametres" class="btn btn-gold">
                <i data-lucide="settings" aria-hidden="true"></i> Modifier mes informations
            </a>
        </div>
    </div>

    <div class="panel" style="margin-top:24px;">
        <h2>Mes informations</h2>
        <div class="form-grid">
            <div class="form-group">
                <label>Téléphone</label>
                <div style="padding:10px 12px; border:1px solid var(--border-soft); border-radius:9px; color:var(--text);">
                    <?php echo htmlspecialchars($profil['telephone'] ?? ''); ?>
                </div>
            </div>
            <div class="form-group">
                <label>Adresse</label>
                <div style="padding:10px 12px; border:1px solid var(--border-soft); border-radius:9px; color:var(--text);">
                    <?php echo htmlspecialchars($profil['adresse'] ?? ''); ?>
                </div>
            </div>
            <div class="form-group">
                <label>Ville</label>
                <div style="padding:10px 12px; border:1px solid var(--border-soft); border-radius:9px; color:var(--text);">
                    <?php echo htmlspecialchars($profil['ville'] ?? ''); ?>
                </div>
            </div>
        </div>
    </div>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
