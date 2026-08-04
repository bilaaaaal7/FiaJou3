<?php
$pageTitle = "Connexion - " . APP_NAME;
$extraCss = ['auth.css'];
$extraJs = ['lang-switch.js'];
require ROOT_PATH . '/assets/inc/header.php';
?>

    <div class="lang-switcher" role="group" aria-label="Sélecteur de langue">
        <button type="button" class="active" data-lang="fr" onclick="setLang('fr')">French</button>
        <button type="button" data-lang="en" onclick="setLang('en')">English</button>
        <button type="button" data-lang="ar" onclick="setLang('ar')">العربية</button>
    </div>

    <div class="theme-toggle-fixed"><?php require ROOT_PATH . '/assets/inc/theme_toggle.php'; ?></div>

    <div class="page-wrap">
        <div class="auth-card">
            <div class="logo-wrap">
                <span class="logo-mark" style="width:64px;height:64px;color:var(--text);margin:0 auto;"><?php include ROOT_PATH . '/assets/inc/logo.php'; ?></span>
            </div>

            <div class="card-body-custom">
                <h2 class="login-title" data-i18n="title">Connexion</h2>
                <p class="login-subtitle" data-i18n="subtitle">Ravis de vous revoir, connectez-vous à votre compte</p>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=connexion">
                    <div class="mb-3">
                        <label for="email" class="form-label" data-i18n="emailLabel">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label" data-i18n="passwordLabel">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="" required>
                    </div>

                    <div class="text-end mb-3">
                        <a href="<?php echo BASE_URL; ?>/index.php?route=mot-de-passe-oublie" class="small" style="color:var(--gold-dark);" data-i18n="forgotPassword">Mot de passe oublié ?</a>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" name="login" class="btn btn-gold" data-i18n="submitBtn">Se connecter</button>
                    </div>
                </form>

                <div class="divider-diamond">
                    <hr><span></span><span></span><hr>
                </div>

                <p class="register-link">
                    <span data-i18n="noAccount">Pas encore de compte ?</span>
                    <a href="<?php echo BASE_URL; ?>/index.php?route=inscription" data-i18n="registerLink">Inscrivez-vous</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        window.i18n = {
            fr: {
                title: "Connexion",
                subtitle: "Ravis de vous revoir, connectez-vous à votre compte",
                emailLabel: "Email",
                passwordLabel: "Mot de passe",
                forgotPassword: "Mot de passe oublié ?",
                submitBtn: "Se connecter",
                noAccount: "Pas encore de compte ?",
                registerLink: "Inscrivez-vous"
            },
            en: {
                title: "Login",
                subtitle: "Welcome back, please sign in to your account",
                emailLabel: "Email",
                passwordLabel: "Password",
                forgotPassword: "Forgot password?",
                submitBtn: "Sign in",
                noAccount: "Don't have an account?",
                registerLink: "Sign up",
                pageTitle: "Login - FiaJou3"
            },
            ar: {
                title: "تسجيل الدخول",
                subtitle: "سعداء بعودتك، سجّل الدخول إلى حسابك",
                emailLabel: "البريد الإلكتروني",
                passwordLabel: "كلمة المرور",
                forgotPassword: "نسيت كلمة المرور؟",
                submitBtn: "تسجيل الدخول",
                noAccount: "ليس لديك حساب؟",
                registerLink: "إنشاء حساب",
                pageTitle: "تسجيل الدخول - فياجوع"
            }
        };
        window.i18n.fr.pageTitle = "Connexion - FiaJou3";
    </script>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
