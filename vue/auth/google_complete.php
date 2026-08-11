<?php
/**
 * Vue : Complément d'inscription Google
 * Même habillage que les autres pages d'authentification (auth-card,
 * thème noir/or). Prénom/nom/email proviennent de Google et sont affichés
 * en lecture seule ; seul le téléphone (obligatoire) et, en option,
 * l'adresse/la ville sont demandés.
 */
$pageTitle = "Finaliser l'inscription - " . APP_NAME;
$extraCss = ['auth.css'];
require ROOT_PATH . '/assets/inc/header.php';
?>

    <?php require ROOT_PATH . '/assets/inc/lang_switcher.php'; ?>

    <div class="theme-toggle-fixed"><?php require ROOT_PATH . '/assets/inc/theme_toggle.php'; ?></div>

    <div class="page-wrap">
        <div class="auth-card">
            <div class="logo-wrap">
                <span class="logo-mark" style="width:64px;height:64px;color:var(--text);margin:0 auto;"><?php include ROOT_PATH . '/assets/inc/logo.php'; ?></span>
            </div>

            <div class="card-body-custom">
                <h2 class="login-title">Encore une étape</h2>
                <p class="login-subtitle">
                    Bienvenue <?php echo htmlspecialchars($pending['prenom']); ?> ! Complétez ces quelques informations pour finaliser votre compte <?php echo htmlspecialchars(APP_NAME); ?>.
                </p>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=auth/google/complete">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($pending['email']); ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="telephone" name="telephone"
                               value="<?php echo htmlspecialchars($_POST['telephone'] ?? ''); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="ville" class="form-label">Ville <span class="text-muted">(optionnel)</span></label>
                        <input type="text" class="form-control" id="ville" name="ville"
                               value="<?php echo htmlspecialchars($_POST['ville'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="adresse" class="form-label">Adresse <span class="text-muted">(optionnel)</span></label>
                        <input type="text" class="form-control" id="adresse" name="adresse"
                               value="<?php echo htmlspecialchars($_POST['adresse'] ?? ''); ?>">
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" name="completer" class="btn btn-gold">Créer mon compte</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
