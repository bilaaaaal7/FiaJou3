<?php
require_once "../config/db.php";

$error = "";
$success = "";

if (isset($_POST['register'])) {

    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $telephone = trim($_POST['telephone']);
    $adresse = trim($_POST['adresse']);
    $ville = trim($_POST['ville']);
    $email = trim($_POST['email']);
    $password  = $_POST['password'];
    $confirmation = $_POST['confirmation'];

    if ($password != $confirmation) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $error = "Cet email est déjà utilisé.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt->execute([$email, $hashedPassword]);

            $userId = $pdo->lastInsertId();

            $role = "client";
            $stmt = $pdo->prepare("INSERT INTO profiles (user_id, prenom, nom, telephone, adresse, ville, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $prenom, $nom, $telephone, $adresse, $ville, $role]);

            $success = "Compte créé avec succès. Vous pouvez vous connecter.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title id="pageTitle">Inscription - FiaJou3</title>

    <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon-16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/images/favicon-180.png">
    <link rel="shortcut icon" href="../assets/images/favicon.ico">

    <link id="bootstrapCss" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --gold: #b8863b;
            --gold-dark: #96702f;
            --dark: #1a1a1a;
            --dark-2: #262626;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #ffffff;
            min-height: 100vh;
            width: 100vw;
            overflow-x: hidden;
            position: relative;
        }

        html[dir="rtl"] body {
            font-family: 'Tajawal', 'Poppins', sans-serif;
        }

        /* subtle decorative glow, purely cosmetic */
        body::before {
            content: "";
            position: fixed;
            top: -10%;
            right: -10%;
            width: 45vw;
            height: 45vw;
            background: radial-gradient(circle, rgba(184, 134, 59, 0.06) 0%, rgba(184, 134, 59, 0) 70%);
            pointer-events: none;
            z-index: 0;
        }

        html[dir="rtl"] body::before {
            right: auto;
            left: -10%;
        }

        /* ---------- Language switcher ---------- */
        .lang-switcher {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10;
            display: flex;
            background: #f7f2e9;
            border: 1px solid #e2d9c7;
            border-radius: 999px;
            padding: 4px;
        }

        .lang-switcher button {
            border: none;
            background: transparent;
            color: #9a9a9a;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 0.8rem;
            padding: 7px 16px;
            border-radius: 999px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .lang-switcher button.active {
            background: var(--gold);
            color: #fff;
        }

        .lang-switcher button:not(.active):hover {
            color: var(--dark);
        }

        html[dir="rtl"] .lang-switcher {
            right: auto;
            left: 20px;
        }

        html[dir="rtl"] .lang-switcher button {
            font-family: 'Tajawal', 'Poppins', sans-serif;
        }

        /* ---------- Layout / centering ---------- */
        .page-wrap {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 90px 20px 30px;
        }

        /* ---------- Register card (no border - just a soft elevated white block) ---------- */
        .login-card {
            background: #fff;
            border-radius: 20px;
            border: none;
            box-shadow:
                0 4px 10px rgba(184, 134, 59, 0.06),
                0 15px 40px rgba(26, 26, 26, 0.08),
                0 2px 60px rgba(184, 134, 59, 0.06);
            overflow: hidden;
            width: 100%;
            max-width: 560px;
        }

        .logo-wrap {
            background: #fff;
            padding: 34px 20px 16px;
            text-align: center;
            border-bottom: 1px solid #f0e6d6;
        }

        .logo-wrap img {
            max-width: 180px;
            width: 100%;
            height: auto;
        }

        .card-body-custom {
            padding: 32px 38px 42px;
        }

        .login-title {
            color: var(--dark);
            font-weight: 700;
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 6px;
        }

        .login-subtitle {
            color: #8a8a8a;
            text-align: center;
            font-size: 0.9rem;
            line-height: 1.4;
            margin-bottom: 26px;
        }

        .form-label {
            font-weight: 500;
            color: var(--dark);
            font-size: 0.88rem;
            margin-bottom: 6px;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #e2d9c7;
            padding: 11px 14px;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 0.2rem rgba(184, 134, 59, 0.2);
        }

        .btn-gold {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 10px;
            padding: 12px;
            font-size: 0.98rem;
            transition: all 0.2s ease-in-out;
        }

        .btn-gold:hover {
            background: linear-gradient(135deg, var(--gold-dark) 0%, var(--gold) 100%);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(184, 134, 59, 0.35);
        }

        .divider-diamond {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 24px 0 18px;
        }

        .divider-diamond hr {
            flex: 1;
            border-top: 1px solid #e2d9c7;
            margin: 0;
        }

        .divider-diamond span {
            width: 8px;
            height: 8px;
            background: var(--gold);
            transform: rotate(45deg);
            display: inline-block;
        }

        .register-link {
            text-align: center;
            font-size: 0.9rem;
            margin: 0;
        }

        .register-link a {
            color: var(--gold-dark);
            font-weight: 600;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 991px) {
            .card-body-custom {
                padding: 28px 32px 36px;
            }
        }

        @media (max-width: 575px) {
            .page-wrap {
                padding: 80px 14px 24px;
                align-items: flex-start;
            }

            .login-card {
                max-width: 100%;
                border-radius: 16px;
            }

            .logo-wrap {
                padding: 26px 16px 12px;
            }

            .logo-wrap img {
                max-width: 140px;
            }

            .card-body-custom {
                padding: 22px 20px 28px;
            }

            .login-title {
                font-size: 1.3rem;
            }

            .login-subtitle {
                font-size: 0.85rem;
                margin-bottom: 20px;
            }

            .lang-switcher {
                top: 14px;
                right: 14px;
            }

            html[dir="rtl"] .lang-switcher {
                right: auto;
                left: 14px;
            }

            .lang-switcher button {
                padding: 6px 12px;
                font-size: 0.75rem;
            }
        }

        @media (max-height: 700px) {
            .page-wrap {
                align-items: flex-start;
                padding-top: 80px;
            }
        }
    </style>
</head>
<body>

    <div class="lang-switcher" role="group" aria-label="Sélecteur de langue">
        <button type="button" class="active" data-lang="fr" onclick="setLang('fr')">French</button>
        <button type="button" data-lang="en" onclick="setLang('en')">English</button>
        <button type="button" data-lang="ar" onclick="setLang('ar')">العربية</button>
    </div>

    <div class="page-wrap">
        <div class="login-card">
            <div class="logo-wrap">
                <img src="../assets/images/logo.png" alt="FiaJou3 Logo">
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

                <form method="POST" action="" autocomplete="off" spellcheck="false">
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
                    <a href="login.php" data-i18n="loginLink">Connectez-vous</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        const i18n = {
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

        i18n.fr.pageTitle = "Inscription - FiaJou3";

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

</body>
</html>
