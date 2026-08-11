<?php
$pageTitle = "Connexion - " . APP_NAME;
$extraCss = ['auth.css'];
$extraJs = ['i18n.js'];
$i18nActive = true;
$i18nPage = 'login';
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
                <h2 class="login-title" data-i18n="login.title">Connexion</h2>
                <p class="login-subtitle" data-i18n="login.subtitle">Ravis de vous revoir, connectez-vous à votre compte</p>

                <?php if (!empty($flashSucces)): ?>
                    <div class="alert alert-success py-2" role="alert">
                        <?php echo htmlspecialchars($flashSucces); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=connexion">
                    <div class="mb-3">
                        <label for="email" class="form-label" data-i18n="login.emailLabel">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label" data-i18n="login.passwordLabel">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="" required>
                    </div>

                    <div class="text-end mb-3">
                        <a href="<?php echo BASE_URL; ?>/index.php?route=mot-de-passe-oublie" class="small" style="color:var(--gold-dark);" data-i18n="login.forgotPassword">Mot de passe oublié ?</a>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" name="login" class="btn btn-gold" data-i18n="login.submitBtn">Se connecter</button>
                    </div>
                </form>

                <?php if (!empty($googleActif)): ?>
                    <div class="divider-diamond divider-diamond--or">
                        <hr><span data-i18n="login.orDivider">ou</span><hr>
                    </div>

                    <?php
                        // Le paramètre "retour" éventuel (bouton "Commander" ->
                        // connexion -> retour) est propagé au flux Google pour
                        // renvoyer l'utilisateur au bon endroit après connexion.
                        $googleUrl = BASE_URL . '/index.php?route=auth/google';
                        if (!empty($_GET['retour'])) {
                            $googleUrl .= '&retour=' . urlencode($_GET['retour']);
                        }
                    ?>
                    <a href="<?php echo htmlspecialchars($googleUrl); ?>" class="btn-google">
                        <svg class="btn-google-logo" viewBox="0 0 48 48" aria-hidden="true" focusable="false">
                            <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
                            <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/>
                            <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238A11.91 11.91 0 0 1 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/>
                            <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 0 1-4.087 5.571l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>
                        </svg>
                        <span data-i18n="login.googleBtn">Continuer avec Google</span>
                    </a>
                <?php endif; ?>

                <div class="divider-diamond">
                    <hr><span></span><span></span><hr>
                </div>

                <p class="register-link">
                    <span data-i18n="login.noAccount">Pas encore de compte ?</span>
                    <a href="<?php echo BASE_URL; ?>/index.php?route=inscription" data-i18n="login.registerLink">Inscrivez-vous</a>
                </p>
            </div>
        </div>
    </div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
