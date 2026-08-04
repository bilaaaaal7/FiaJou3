<?php
require_once "../config/db.php";
session_start();

$error = "";

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = "Cet email n'existe pas.";
    } else {
        if (password_verify($password, $user['password'])) {

            $stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            $profile = $stmt->fetch();

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['prenom'] = $profile['prenom'];
            $_SESSION['role'] = $profile['role'];

            if ($profile['role'] == "admin") {
                header("Location: ../admin/index.php");
                exit;
            } elseif ($profile['role'] == "client") {
                header("Location: ../client/index.php");
                exit;
            } elseif ($profile['role'] == "cuisinier") {
                header("Location: ../cuisinier/index.php");
                exit;
            } elseif ($profile['role'] == "livreur") {
                header("Location: ../livreur/index.php");
                exit;
            }
        } else {
            $error = "Mot de passe incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <script>
        (function () {
            var t = null;
            try { t = localStorage.getItem('fiajou3-theme'); } catch (e) { /* ignore */ }
            if (t !== 'dark') { t = 'light'; }
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title id="pageTitle">Connexion - FiaJou3</title>

    <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon-16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/images/favicon-180.png">
    <link rel="shortcut icon" href="../assets/images/favicon.ico">

    <link id="bootstrapCss" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Open+Sans:wght@400;600;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/app.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>

    <button type="button" class="theme-toggle theme-toggle-fixed" data-theme-toggle aria-pressed="false" title="Basculer le thème (clair / sombre)">
        <svg data-theme-icon="sun" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2"></path><path d="M12 20v2"></path><path d="m4.93 4.93 1.41 1.41"></path><path d="m17.66 17.66 1.41 1.41"></path><path d="M2 12h2"></path><path d="M20 12h2"></path><path d="m6.34 17.66-1.41 1.41"></path><path d="m19.07 4.93-1.41 1.41"></path></svg>
        <svg data-theme-icon="moon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path></svg>
    </button>

    <div class="lang-switcher" role="group" aria-label="Sélecteur de langue">
        <button type="button" class="active" data-lang="fr" onclick="setLang('fr')">French</button>
        <button type="button" data-lang="en" onclick="setLang('en')">English</button>
        <button type="button" data-lang="ar" onclick="setLang('ar')">العربية</button>
    </div>

    <div class="page-wrap">
        <div class="auth-card">
            <div class="logo-wrap">
                <img class="logo-theme-light" src="../assets/images/logo.png" alt="FiaJou3 Logo">
                <img class="logo-theme-dark" src="../assets/images/logo-light.png" alt="FiaJou3 Logo">
            </div>

            <div class="card-body-custom">
                <h2 class="login-title" data-i18n="title">Connexion</h2>
                <p class="login-subtitle" data-i18n="subtitle">Ravis de vous revoir, connectez-vous à votre compte</p>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label" data-i18n="emailLabel">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label" data-i18n="passwordLabel">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="" required>
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
                    <a href="register.php" data-i18n="registerLink">Inscrivez-vous</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        const i18n = {
            fr: {
                title: "Connexion",
                subtitle: "Ravis de vous revoir, connectez-vous à votre compte",
                emailLabel: "Email",
                passwordLabel: "Mot de passe",
                submitBtn: "Se connecter",
                noAccount: "Pas encore de compte ?",
                registerLink: "Inscrivez-vous"
            },
            en: {
                title: "Login",
                subtitle: "Welcome back, please sign in to your account",
                emailLabel: "Email",
                passwordLabel: "Password",
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
                submitBtn: "تسجيل الدخول",
                noAccount: "ليس لديك حساب؟",
                registerLink: "إنشاء حساب",
                pageTitle: "تسجيل الدخول - فياجوع"
            }
        };

        i18n.fr.pageTitle = "Connexion - FiaJou3";

        const BOOTSTRAP_LTR = "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css";
        const BOOTSTRAP_RTL = "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css";

        function setLang(lang) {
            if (!i18n[lang]) return;

            document.querySelectorAll('.lang-switcher button').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.lang === lang);
            });

            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (i18n[lang][key]) {
                    el.textContent = i18n[lang][key];
                }
            });

            const dir = lang === 'ar' ? 'rtl' : 'ltr';
            document.documentElement.lang = lang;
            document.documentElement.dir = dir;

            const bootstrapCss = document.getElementById('bootstrapCss');
            const targetHref = dir === 'rtl' ? BOOTSTRAP_RTL : BOOTSTRAP_LTR;
            if (bootstrapCss && bootstrapCss.getAttribute('href') !== targetHref) {
                bootstrapCss.setAttribute('href', targetHref);
            }

            if (i18n[lang].pageTitle) {
                document.getElementById('pageTitle').textContent = i18n[lang].pageTitle;
            }

            localStorage.setItem('fiajou3_lang', lang);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const savedLang = localStorage.getItem('fiajou3_lang');
            if (savedLang && i18n[savedLang]) {
                setLang(savedLang);
            }
        });
    </script>

    <script src="../assets/js/theme.js"></script>

</body>
</html>
