<?php
/**
 * Vue : Mot de passe oublié / Réinitialisation du mot de passe
 * Même habillage que la page de connexion (auth-card, thème noir/or).
 *
 * Deux formulaires possibles selon le contexte fourni par
 * MotDePasseOublieControleur :
 *   - $modeReset = false : formulaire "Email" (demande de lien) ;
 *   - $modeReset = true  : formulaire "Nouveau mot de passe" (lien reçu par
 *     email valide, jeton dans $tokenActuel).
 */
$pageTitle = "Mot de passe oublié - " . APP_NAME;
$extraCss = ['auth.css'];
$extraJs = ['i18n.js'];
$i18nActive = true;
$i18nPage = 'mdp';
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

                <?php if ($modeReset): ?>

                    <h2 class="login-title" data-i18n="mdp.resetTitle">Nouveau mot de passe</h2>
                    <p class="login-subtitle" data-i18n="mdp.resetSubtitle">Choisissez un nouveau mot de passe pour votre compte</p>

                    <?php if (!empty($erreur)): ?>
                        <div class="alert alert-danger py-2" role="alert">
                            <?php echo htmlspecialchars($erreur); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=mot-de-passe-oublie">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($tokenActuel); ?>">

                        <div class="mb-3">
                            <label for="nouveau_mdp" class="form-label" data-i18n="mdp.newPasswordLabel">Nouveau mot de passe</label>
                            <input type="password" class="form-control" id="nouveau_mdp" name="nouveau_mdp"
                                   autocomplete="new-password" required minlength="6">
                        </div>

                        <div class="mb-3">
                            <label for="confirmation" class="form-label" data-i18n="mdp.confirmPasswordLabel">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="confirmation" name="confirmation"
                                   autocomplete="new-password" required minlength="6">
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" name="reset" class="btn btn-gold" data-i18n="mdp.resetBtn">Réinitialiser</button>
                        </div>
                    </form>

                <?php elseif ($message !== ''): ?>

                    <h2 class="login-title" data-i18n="mdp.title">Mot de passe oublié</h2>

                    <div class="alert alert-success py-2" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                    </div>

                <?php else: ?>

                    <h2 class="login-title" data-i18n="mdp.title">Mot de passe oublié</h2>
                    <p class="login-subtitle" data-i18n="mdp.subtitle">Entrez votre email pour réinitialiser votre mot de passe</p>

                    <?php if (!empty($erreur)): ?>
                        <div class="alert alert-danger py-2" role="alert">
                            <?php echo htmlspecialchars($erreur); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=mot-de-passe-oublie">
                        <div class="mb-3">
                            <label for="email" class="form-label" data-i18n="mdp.emailLabel">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="" required>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" name="envoyer" class="btn btn-gold" data-i18n="mdp.submitBtn">Envoyer le lien</button>
                        </div>
                    </form>

                <?php endif; ?>

                <div class="divider-diamond">
                    <hr><span></span><span></span><hr>
                </div>

                <p class="register-link">
                    <a href="<?php echo BASE_URL; ?>/index.php?route=connexion" data-i18n="mdp.backLogin">Retour à la connexion</a>
                </p>
            </div>
        </div>
    </div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
