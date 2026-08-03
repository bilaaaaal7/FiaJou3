<?php
$pageTitle = "Inscription - " . APP_NAME;
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
        <div class="auth-card auth-card--wide">
            <div class="logo-wrap">
                <span class="logo-mark" style="width:64px;height:64px;color:#171717;margin:0 auto;"><?php include ROOT_PATH . '/assets/inc/logo.php'; ?></span>
            </div>

            <div class="card-body-custom">
                <h2 class="login-title" data-i18n="title">Inscription</h2>
                <p class="login-subtitle" data-i18n="subtitle">Créez votre compte pour commencer à commander</p>

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
                            <label for="prenom" class="form-label" data-i18n="prenomLabel">Prénom</label>
                            <input type="text" class="form-control" id="prenom" name="prenom" placeholder=""
                                   autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                   readonly onfocus="this.removeAttribute('readonly')" required>
                        </div>

                        <div class="col-md-6">
                            <label for="nom" class="form-label" data-i18n="nomLabel">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" placeholder=""
                                   autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                   readonly onfocus="this.removeAttribute('readonly')" required>
                        </div>

                        <div class="col-md-6">
                            <label for="telephone" class="form-label" data-i18n="telephoneLabel">Téléphone</label>
                            <input type="tel" class="form-control" id="telephone" name="telephone" placeholder=""
                                   autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                   readonly onfocus="this.removeAttribute('readonly')" required>
                        </div>

                        <div class="col-md-6">
                            <label for="ville" class="form-label" data-i18n="villeLabel">Ville</label>
                            <input type="text" class="form-control" id="ville" name="ville" placeholder=""
                                   autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                   readonly onfocus="this.removeAttribute('readonly')" required>
                        </div>

                        <div class="col-12">
                            <label for="adresse" class="form-label" data-i18n="adresseLabel">Adresse</label>
                            <input type="text" class="form-control" id="adresse" name="adresse" placeholder=""
                                   autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                   readonly onfocus="this.removeAttribute('readonly')" required>
                        </div>

                        <div class="col-12">
                            <label for="email" class="form-label" data-i18n="emailLabel">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder=""
                                   autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                   readonly onfocus="this.removeAttribute('readonly')" required>
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label" data-i18n="passwordLabel">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder=""
                                   autocomplete="new-password"
                                   readonly onfocus="this.removeAttribute('readonly')" required>
                        </div>

                        <div class="col-md-6">
                            <label for="confirmation" class="form-label" data-i18n="confirmationLabel">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="confirmation" name="confirmation" placeholder=""
                                   autocomplete="new-password"
                                   readonly onfocus="this.removeAttribute('readonly')" required>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" name="register" class="btn btn-gold" data-i18n="submitBtn">S'inscrire</button>
                    </div>
                </form>

                <div class="divider-diamond">
                    <hr><span></span><span></span><hr>
                </div>

                <p class="register-link">
                    <span data-i18n="hasAccount">Vous avez déjà un compte ?</span>
                    <a href="<?php echo BASE_URL; ?>/index.php?route=connexion" data-i18n="loginLink">Connectez-vous</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        window.i18n = {
            fr: {
                title: "Inscription",
                subtitle: "Créez votre compte pour commencer à commander",
                prenomLabel: "Prénom",
                nomLabel: "Nom",
                telephoneLabel: "Téléphone",
                villeLabel: "Ville",
                adresseLabel: "Adresse",
                emailLabel: "Email",
                passwordLabel: "Mot de passe",
                confirmationLabel: "Confirmer le mot de passe",
                submitBtn: "S'inscrire",
                hasAccount: "Vous avez déjà un compte ?",
                loginLink: "Connectez-vous"
            },
            en: {
                title: "Sign up",
                subtitle: "Create your account to start ordering",
                prenomLabel: "First name",
                nomLabel: "Last name",
                telephoneLabel: "Phone",
                villeLabel: "City",
                adresseLabel: "Address",
                emailLabel: "Email",
                passwordLabel: "Password",
                confirmationLabel: "Confirm password",
                submitBtn: "Sign up",
                hasAccount: "Already have an account?",
                loginLink: "Sign in",
                pageTitle: "Sign up - FiaJou3"
            },
            ar: {
                title: "إنشاء حساب",
                subtitle: "أنشئ حسابك لتبدأ بالطلب",
                prenomLabel: "الاسم الأول",
                nomLabel: "اسم العائلة",
                telephoneLabel: "الهاتف",
                villeLabel: "المدينة",
                adresseLabel: "العنوان",
                emailLabel: "البريد الإلكتروني",
                passwordLabel: "كلمة المرور",
                confirmationLabel: "تأكيد كلمة المرور",
                submitBtn: "إنشاء حساب",
                hasAccount: "لديك حساب بالفعل؟",
                loginLink: "سجّل الدخول",
                pageTitle: "إنشاء حساب - فياجوع"
            }
        };
        window.i18n.fr.pageTitle = "Inscription - FiaJou3";
    </script>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
