<?php
$pageTitle = "Mot de passe oublié - " . APP_NAME;
$extraCss = ['auth.css'];
$extraJs = ['lang-switch.js'];
require ROOT_PATH . '/assets/inc/header.php';
?>

    <div class="lang-switcher" role="group" aria-label="Sélecteur de langue">
        <button type="button" class="active" data-lang="fr" onclick="setLang('fr')">French</button>
        <button type="button" data-lang="en" onclick="setLang('en')">English</button>
        <button type="button" data-lang="ar" onclick="setLang('ar')">العربية</button>
    </div>

    <div class="page-wrap">
        <div class="auth-card">
            <div class="logo-wrap">
                <span class="logo-mark" style="width:64px;height:64px;color:#171717;margin:0 auto;"><?php include ROOT_PATH . '/assets/inc/logo.php'; ?></span>
            </div>

            <div class="card-body-custom">
                <h2 class="login-title" data-i18n="title">Mot de passe oublié</h2>
                <p class="login-subtitle" data-i18n="subtitle">Entrez votre email pour réinitialiser votre mot de passe</p>

                <?php if (!empty($erreur)): ?>
                    <div class="alert alert-danger py-2" role="alert">
                        <?php echo htmlspecialchars($erreur); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($message) && !$modeReset): ?>
                    <div class="alert alert-success py-2" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                    <p class="register-link">
                        <a href="<?php echo BASE_URL; ?>/index.php?route=connexion">Retour à la connexion</a>
                    </p>
                <?php elseif ($modeReset && empty($erreur)): ?>

                    <?php if ($message): ?>
                    <div class="alert alert-success py-2" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=mot-de-passe-oublie">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['reset_token'] ?? ''); ?>">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($emailReset); ?>">

                        <div class="mb-3">
                            <label for="nouveau_mdp" class="form-label" data-i18n="newPasswordLabel">Nouveau mot de passe</label>
                            <input type="password" class="form-control" id="nouveau_mdp" name="nouveau_mdp"
                                   autocomplete="new-password" required minlength="6">
                        </div>

                        <div class="mb-3">
                            <label for="confirmation" class="form-label" data-i18n="confirmPasswordLabel">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="confirmation" name="confirmation"
                                   autocomplete="new-password" required minlength="6">
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" name="reset" class="btn btn-gold" data-i18n="resetBtn">Réinitialiser</button>
                        </div>
                    </form>

                <?php else: ?>

                    <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=mot-de-passe-oublie">
                        <div class="mb-3">
                            <label for="email" class="form-label" data-i18n="emailLabel">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="" required>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" name="envoyer" class="btn btn-gold" data-i18n="submitBtn">Envoyer le lien</button>
                        </div>
                    </form>

                <?php endif; ?>

                <div class="divider-diamond">
                    <hr><span></span><span></span><hr>
                </div>

                <p class="register-link">
                    <a href="<?php echo BASE_URL; ?>/index.php?route=connexion" data-i18n="backLogin">Retour à la connexion</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        window.i18n = {
            fr: {
                title: "Mot de passe oublié",
                subtitle: "Entrez votre email pour réinitialiser votre mot de passe",
                emailLabel: "Email",
                newPasswordLabel: "Nouveau mot de passe",
                confirmPasswordLabel: "Confirmer le mot de passe",
                submitBtn: "Envoyer le lien",
                resetBtn: "Réinitialiser",
                backLogin: "Retour à la connexion"
            },
            en: {
                title: "Forgot Password",
                subtitle: "Enter your email to reset your password",
                emailLabel: "Email",
                newPasswordLabel: "New password",
                confirmPasswordLabel: "Confirm password",
                submitBtn: "Send link",
                resetBtn: "Reset",
                backLogin: "Back to login",
                pageTitle: "Forgot Password - FiaJou3"
            },
            ar: {
                title: "نسيت كلمة المرور",
                subtitle: "أدخل بريدك الإلكتروني لإعادة تعيين كلمة المرور",
                emailLabel: "البريد الإلكتروني",
                newPasswordLabel: "كلمة المرور الجديدة",
                confirmPasswordLabel: "تأكيد كلمة المرور",
                submitBtn: "إرسال الرابط",
                resetBtn: "إعادة التعيين",
                backLogin: "العودة لتسجيل الدخول",
                pageTitle: "نسيت كلمة المرور - فياجوع"
            }
        };
        window.i18n.fr.pageTitle = "Mot de passe oublié - FiaJou3";
    </script>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
