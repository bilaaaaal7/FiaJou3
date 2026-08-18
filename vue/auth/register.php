<?php
$pageTitle = "Inscription - " . APP_NAME;
$extraCss = ['auth.css'];
$extraJs = ['i18n.js', 'password-toggle.js'];
$i18nActive = true;
$i18nPage = 'register';
require ROOT_PATH . '/assets/inc/header.php';
?>

    <?php require ROOT_PATH . '/assets/inc/lang_switcher.php'; ?>

    <div class="theme-toggle-fixed"><?php require ROOT_PATH . '/assets/inc/theme_toggle.php'; ?></div>

    <div class="page-wrap">
        <div class="auth-card auth-card--wide">
            <div class="logo-wrap">
                <span class="logo-mark" style="width:64px;height:64px;color:var(--text);margin:0 auto;"><?php include ROOT_PATH . '/assets/inc/logo.php'; ?></span>
            </div>

            <div class="card-body-custom">
                <h2 class="login-title" data-i18n="register.title">Inscription</h2>
                <p class="login-subtitle" data-i18n="register.subtitle">Créez votre compte pour commencer à commander</p>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success py-2" role="alert">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=inscription" autocomplete="off" spellcheck="false">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="prenom" class="form-label" data-i18n="register.prenomLabel">Prénom</label>
                            <input type="text" class="form-control" id="prenom" name="prenom" placeholder=""
                                   autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                   readonly onfocus="this.removeAttribute('readonly')" required>
                        </div>

                        <div class="col-md-6">
                            <label for="nom" class="form-label" data-i18n="register.nomLabel">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" placeholder=""
                                   autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                   readonly onfocus="this.removeAttribute('readonly')" required>
                        </div>

                        <div class="col-md-6">
                            <label for="telephone" class="form-label" data-i18n="register.telephoneLabel">Téléphone</label>
                            <input type="tel" class="form-control" id="telephone" name="telephone" placeholder=""
                                   autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                   readonly onfocus="this.removeAttribute('readonly')" required>
                        </div>

                        <div class="col-md-6">
                            <label for="ville" class="form-label" data-i18n="register.villeLabel">Ville</label>
                            <input type="text" class="form-control" id="ville" name="ville" placeholder=""
                                   autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                   readonly onfocus="this.removeAttribute('readonly')" required>
                        </div>

                        <div class="col-12">
                            <label for="adresse" class="form-label" data-i18n="register.adresseLabel">Adresse</label>
                            <input type="text" class="form-control" id="adresse" name="adresse" placeholder=""
                                   autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                   readonly onfocus="this.removeAttribute('readonly')" required>
                        </div>

                        <div class="col-12">
                            <label for="email" class="form-label" data-i18n="register.emailLabel">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder=""
                                   autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                   readonly onfocus="this.removeAttribute('readonly')" required>
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label" data-i18n="register.passwordLabel">Mot de passe</label>
                            <div class="password-input-wrap" data-password-toggle>
                                <input type="password" class="form-control" id="password" name="password" placeholder=""
                                       autocomplete="new-password"
                                       readonly onfocus="this.removeAttribute('readonly')" required>
                                <button type="button" class="password-toggle-btn" aria-label="Afficher le mot de passe">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="confirmation" class="form-label" data-i18n="register.confirmationLabel">Confirmer le mot de passe</label>
                            <div class="password-input-wrap" data-password-toggle>
                                <input type="password" class="form-control" id="confirmation" name="confirmation" placeholder=""
                                       autocomplete="new-password"
                                       readonly onfocus="this.removeAttribute('readonly')" required>
                                <button type="button" class="password-toggle-btn" aria-label="Afficher le mot de passe">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" name="register" class="btn btn-gold" data-i18n="register.submitBtn">S'inscrire</button>
                    </div>
                </form>

                <div class="divider-diamond">
                    <hr><span></span><span></span><hr>
                </div>

                <p class="register-link">
                    <span data-i18n="register.hasAccount">Vous avez déjà un compte ?</span>
                    <a href="<?php echo BASE_URL; ?>/index.php?route=connexion" data-i18n="register.loginLink">Connectez-vous</a>
                </p>
            </div>
        </div>
    </div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
