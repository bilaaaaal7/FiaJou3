<?php
$pageTitle = "Mon profil - " . APP_NAME;
$extraCss = ['admin.css', 'profile-menu.css', 'client-public.css'];
$bodyClass = 'client-public-layout';
$i18nPage = 'profil';
$forceLightTheme = true;
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/client_navbar.php';

$prenomProfil = trim((string) ($profil['prenom'] ?? ''));
$nomProfil    = trim((string) ($profil['nom'] ?? ''));
$emailProfil  = (string) ($profil['email'] ?? '');
$telephoneProfil = (string) ($profil['telephone'] ?? '');
$adresseProfil   = (string) ($profil['adresse'] ?? '');
$villeProfil     = (string) ($profil['ville'] ?? '');
$initialesProfil = mb_strtoupper(mb_substr($prenomProfil, 0, 1) . mb_substr($nomProfil, 0, 1));
if ($initialesProfil === '') {
    $initialesProfil = '?';
}
?>

<div class="page-profil">

    <div class="topbar">
        <div>
            <h1 data-i18n="profil.titre">Mon profil</h1>
            <p class="profil-subtitle" data-i18n="profil.sousTitre">Gérez vos informations personnelles et votre mot de passe.</p>
        </div>
    </div>

    <?php if ($succes): ?>
        <div class="alert-box alert-success" id="alert-success"><?php echo htmlspecialchars($succes); ?></div>
    <?php endif; ?>

    <?php if ($erreur): ?>
        <div class="alert-box alert-error" id="alert-error"><?php echo htmlspecialchars($erreur); ?></div>
    <?php endif; ?>

    <div class="profile-hero-card">
        <div class="profile-hero-glow"></div>
        <div class="profile-hero-avatar-wrap">
            <span class="profile-hero-avatar"><?php echo htmlspecialchars($initialesProfil); ?></span>
            <span class="profile-hero-status"></span>
        </div>
        <div class="profile-hero-details">
            <h2 class="profile-hero-name"><?php echo htmlspecialchars(trim($prenomProfil . ' ' . $nomProfil)); ?></h2>
            <p class="profile-hero-email"><?php echo htmlspecialchars($emailProfil); ?></p>
            <span class="profile-hero-badge">
                <i data-lucide="shield-check" aria-hidden="true"></i>
                Customer
            </span>
        </div>
    </div>

    <div class="profile-sections">

        <div class="panel profil-card">
            <div class="profil-card-head">
                <div class="profil-card-icon">
                    <i data-lucide="user" aria-hidden="true"></i>
                </div>
                <div>
                    <h2 data-i18n="parametres.infosTitre">Informations personnelles</h2>
                    <p class="profil-card-desc">Mettez à jour vos informations personnelles</p>
                </div>
            </div>

            <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=client/profil" id="profile-form">
                <div class="form-grid profile-form-grid">
                    <div class="form-group">
                        <label for="prenom" data-i18n="parametres.prenomLabel">Prénom</label>
                        <div class="input-icon-wrap">
                            <i data-lucide="user" aria-hidden="true"></i>
                            <input type="text" id="prenom" name="prenom"
                                   value="<?php echo htmlspecialchars($profil['prenom'] ?? ''); ?>" required placeholder="Votre prénom">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nom" data-i18n="parametres.nomLabel">Nom</label>
                        <div class="input-icon-wrap">
                            <i data-lucide="user" aria-hidden="true"></i>
                            <input type="text" id="nom" name="nom"
                                   value="<?php echo htmlspecialchars($profil['nom'] ?? ''); ?>" required placeholder="Votre nom">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="telephone" data-i18n="parametres.telephoneLabel">Téléphone</label>
                        <div class="input-icon-wrap">
                            <i data-lucide="phone" aria-hidden="true"></i>
                            <input type="tel" id="telephone" name="telephone"
                                   value="<?php echo htmlspecialchars($telephoneProfil); ?>" placeholder="+212 600 000 000">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email" data-i18n="parametres.emailLabel">Email</label>
                        <div class="input-icon-wrap input-icon-wrap--disabled">
                            <i data-lucide="mail" aria-hidden="true"></i>
                            <input type="email" id="email"
                                   value="<?php echo htmlspecialchars($emailProfil); ?>" disabled>
                            <span class="input-badge-locked">
                                <i data-lucide="lock" aria-hidden="true"></i>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="adresse" data-i18n="parametres.adresseLabel">Adresse</label>
                        <div class="input-icon-wrap">
                            <i data-lucide="map-pin" aria-hidden="true"></i>
                            <input type="text" id="adresse" name="adresse"
                                   value="<?php echo htmlspecialchars($adresseProfil); ?>" placeholder="Votre adresse">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ville" data-i18n="parametres.villeLabel">Ville</label>
                        <div class="input-icon-wrap">
                            <i data-lucide="building-2" aria-hidden="true"></i>
                            <input type="text" id="ville" name="ville"
                                   value="<?php echo htmlspecialchars($villeProfil); ?>" placeholder="Votre ville">
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="modifier" class="btn btn-gold" data-i18n="parametres.enregistrerInfos">
                        <i data-lucide="check" aria-hidden="true"></i> Enregistrer les modifications
                    </button>
                    <a href="<?php echo BASE_URL; ?>/index.php?route=client/profil" class="btn btn-outline" data-i18n="common.annuler">Annuler</a>
                </div>
            </form>
        </div>

        <div class="panel profil-card">
            <div class="profil-card-head">
                <div class="profil-card-icon profil-card-icon--lock">
                    <i data-lucide="lock" aria-hidden="true"></i>
                </div>
                <div>
                    <h2 data-i18n="parametres.mdpTitre">Changer le mot de passe</h2>
                    <p class="profil-card-desc">Assurez-vous d'utiliser un mot de passe sécurisé</p>
                </div>
            </div>

            <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=client/profil" id="password-form" novalidate>
                <div class="form-grid profile-form-grid profile-form-grid--single">
                    <div class="form-group">
                        <label for="ancien_mdp" data-i18n="parametres.mdpActuel">Mot de passe actuel</label>
                        <div class="input-icon-wrap">
                            <i data-lucide="key-round" aria-hidden="true"></i>
                            <input type="password" id="ancien_mdp" name="ancien_mdp" required placeholder="••••••••">
                        </div>
                        <span class="field-error" id="err-ancien_mdp"></span>
                    </div>

                    <div class="form-group">
                        <label for="nouveau_mdp" data-i18n="parametres.nouveauMdp">Nouveau mot de passe</label>
                        <div class="input-icon-wrap">
                            <i data-lucide="shield" aria-hidden="true"></i>
                            <input type="password" id="nouveau_mdp" name="nouveau_mdp" minlength="6" required placeholder="••••••••">
                        </div>
                        <span class="field-error" id="err-nouveau_mdp"></span>
                        <div class="password-strength" id="password-strength" hidden>
                            <div class="password-strength-bar"><div class="password-strength-fill" id="strength-fill"></div></div>
                            <span class="password-strength-label" id="strength-label"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirmation_mdp" data-i18n="parametres.confirmationMdp">Confirmer le nouveau mot de passe</label>
                        <div class="input-icon-wrap">
                            <i data-lucide="shield-check" aria-hidden="true"></i>
                            <input type="password" id="confirmation_mdp" name="confirmation_mdp" minlength="6" required placeholder="••••••••">
                        </div>
                        <span class="field-error" id="err-confirmation_mdp"></span>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="changer_mdp" class="btn btn-gold" data-i18n="parametres.changerMdp">
                        <i data-lucide="key-round" aria-hidden="true"></i> Changer le mot de passe
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>

<script>
(function () {
    var pwdForm = document.getElementById('password-form');
    if (!pwdForm) return;

    var ancien   = document.getElementById('ancien_mdp');
    var nouveau  = document.getElementById('nouveau_mdp');
    var confirm  = document.getElementById('confirmation_mdp');
    var errAncien  = document.getElementById('err-ancien_mdp');
    var errNouveau = document.getElementById('err-nouveau_mdp');
    var errConfirm = document.getElementById('err-confirmation_mdp');
    var strengthEl = document.getElementById('password-strength');
    var strengthFill = document.getElementById('strength-fill');
    var strengthLabel = document.getElementById('strength-label');

    function clearErrors() {
        [errAncien, errNouveau, errConfirm].forEach(function (e) { e.textContent = ''; });
        [ancien, nouveau, confirm].forEach(function (f) { f.classList.remove('input-error'); });
    }

    function showError(field, errEl, msg) {
        errEl.textContent = msg;
        field.classList.add('input-error');
    }

    function evaluateStrength(pwd) {
        var score = 0;
        if (pwd.length >= 8) score++;
        if (pwd.length >= 12) score++;
        if (/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) score++;
        if (/\d/.test(pwd)) score++;
        if (/[^a-zA-Z0-9]/.test(pwd)) score++;
        return score;
    }

    function updateStrength() {
        var pwd = nouveau.value;
        if (!pwd) {
            strengthEl.hidden = true;
            return;
        }
        strengthEl.hidden = false;
        var score = evaluateStrength(pwd);
        var levels = ['', 'Faible', 'Moyen', 'Bon', 'Fort', 'Très fort'];
        var colors = ['', '#e74c3c', '#e67e22', '#f1c40f', '#2ecc71', '#27ae60'];
        var pcts   = ['', '20%', '40%', '60%', '80%', '100%'];
        strengthFill.style.width = pcts[score] || '0%';
        strengthFill.style.background = colors[score] || 'transparent';
        strengthLabel.textContent = levels[score] || '';
        strengthLabel.style.color = colors[score] || 'var(--text-muted)';
    }

    nouveau.addEventListener('input', updateStrength);

    confirm.addEventListener('input', function () {
        if (confirm.value && confirm.value !== nouveau.value) {
            errConfirm.textContent = '';
        }
    });

    pwdForm.addEventListener('submit', function (e) {
        e.preventDefault();
        clearErrors();
        var valid = true;

        if (!ancien.value.trim()) {
            showError(ancien, errAncien, 'Veuillez saisir votre mot de passe actuel.');
            valid = false;
        }

        if (!nouveau.value.trim()) {
            showError(nouveau, errNouveau, 'Veuillez saisir un nouveau mot de passe.');
            valid = false;
        } else if (nouveau.value.length < 6) {
            showError(nouveau, errNouveau, 'Le mot de passe doit contenir au moins 6 caractères.');
            valid = false;
        }

        if (!confirm.value.trim()) {
            showError(confirm, errConfirm, 'Veuillez confirmer le nouveau mot de passe.');
            valid = false;
        } else if (confirm.value !== nouveau.value) {
            showError(confirm, errConfirm, 'Les mots de passe ne correspondent pas.');
            valid = false;
        }

        if (!valid) return;

        pwdForm.submit();
    });
})();
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var alerts = document.querySelectorAll('.alert-box');
    alerts.forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            el.style.opacity = '0';
            el.style.transform = 'translateY(-10px)';
            setTimeout(function () { el.remove(); }, 500);
        }, 5000);
    });
});
</script>

<?php require ROOT_PATH . '/assets/inc/client_footer.php'; ?>
