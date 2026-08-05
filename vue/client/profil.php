<?php
$pageTitle = "Mon profil - " . APP_NAME;
$extraCss = ['admin.css'];
$bodyClass = 'profil-sans-sidebar';
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';

$prenomProfil = trim((string) ($profil['prenom'] ?? ''));
$nomProfil    = trim((string) ($profil['nom'] ?? ''));
$emailProfil  = (string) ($profil['email'] ?? '');
$initialesProfil = mb_strtoupper(mb_substr($prenomProfil, 0, 1) . mb_substr($nomProfil, 0, 1));
if ($initialesProfil === '') {
    $initialesProfil = '?';
}
?>

<div class="page-profil">

    <?php require ROOT_PATH . '/assets/inc/back_home.php'; ?>

    <div class="topbar">
        <h1>Mon profil</h1>
        <p class="profil-subtitle">Gérez vos informations personnelles et votre mot de passe.</p>
    </div>

    <?php if ($succes): ?>
        <div class="alert-box alert-success"><?php echo htmlspecialchars($succes); ?></div>
    <?php endif; ?>

    <?php if ($erreur): ?>
        <div class="alert-box alert-error"><?php echo htmlspecialchars($erreur); ?></div>
    <?php endif; ?>

    <div class="profil-hero">
        <span class="profil-hero-avatar"><?php echo htmlspecialchars($initialesProfil); ?></span>
        <div class="profil-hero-info">
            <strong><?php echo htmlspecialchars(trim($prenomProfil . ' ' . $nomProfil)); ?></strong>
            <span><?php echo htmlspecialchars($emailProfil); ?></span>
        </div>
    </div>

    <div class="panel profil-card">
        <div class="profil-card-head">
            <i data-lucide="user" aria-hidden="true"></i>
            <h2>Informations personnelles</h2>
        </div>

        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=client/profil">
            <div class="form-grid">
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom"
                           value="<?php echo htmlspecialchars($profil['prenom'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom"
                           value="<?php echo htmlspecialchars($profil['nom'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone"
                           value="<?php echo htmlspecialchars($profil['telephone'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email"
                           value="<?php echo htmlspecialchars($profil['email'] ?? ''); ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <input type="text" id="adresse" name="adresse"
                           value="<?php echo htmlspecialchars($profil['adresse'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="ville">Ville</label>
                    <input type="text" id="ville" name="ville"
                           value="<?php echo htmlspecialchars($profil['ville'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="modifier" class="btn btn-gold">Enregistrer les modifications</button>
                <a href="<?php echo BASE_URL; ?>/index.php?route=client/profil" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>

    <div class="panel profil-card">
        <div class="profil-card-head">
            <i data-lucide="lock" aria-hidden="true"></i>
            <h2>Changer le mot de passe</h2>
        </div>

        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=client/profil">
            <div class="form-grid">
                <div class="form-group">
                    <label for="ancien_mdp">Mot de passe actuel</label>
                    <input type="password" id="ancien_mdp" name="ancien_mdp" required>
                </div>

                <div class="form-group">
                    <label for="nouveau_mdp">Nouveau mot de passe</label>
                    <input type="password" id="nouveau_mdp" name="nouveau_mdp" minlength="6" required>
                </div>

                <div class="form-group">
                    <label for="confirmation_mdp">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="confirmation_mdp" name="confirmation_mdp" minlength="6" required>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="changer_mdp" class="btn btn-gold">Changer le mot de passe</button>
            </div>
        </form>
    </div>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
