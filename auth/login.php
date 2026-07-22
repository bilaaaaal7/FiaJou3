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
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Connexion - FiaJou3</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

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

        /* ---------- Login card (no longer a floating card - just a centered content block) ---------- */
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
            max-width: 440px;
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
    </div>

    <div class="page-wrap">
        <div class="login-card">
            <div class="logo-wrap">
                <img src="../assets/images/logo.png" alt="FiaJou3 Logo">
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
                registerLink: "Sign up"
            }
        };

        function setLang(lang) {
            document.querySelectorAll('.lang-switcher button').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.lang === lang);
            });

            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (i18n[lang] && i18n[lang][key]) {
                    el.textContent = i18n[lang][key];
                }
            });

            document.getElementById('email').placeholder = lang === 'en' ? '' : '';
            document.documentElement.lang = lang;
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
