<?php
$pageTitle = "Paramètres - " . APP_NAME;
$extraCss = ['admin.css'];
$extraJs = ['i18n.js'];
$bodyClass = 'profil-sans-sidebar';
$i18nActive = true;
$i18nPage = 'parametres';
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';

$prenomParam = trim((string) ($profil['prenom'] ?? ''));
$nomParam    = trim((string) ($profil['nom'] ?? ''));
$emailParam  = (string) ($profil['email'] ?? '');
$initialesParam = mb_strtoupper(mb_substr($prenomParam, 0, 1) . mb_substr($nomParam, 0, 1));
if ($initialesParam === '') {
    $initialesParam = '?';
}

$langueActiveParam = langue_actuelle();
$languesInfosParam = [
    'fr' => ['code' => 'FR', 'nom' => 'Français'],
    'en' => ['code' => 'EN', 'nom' => 'English'],
    'ar' => ['code' => 'ع', 'nom' => 'العربية'],
];
$langueCouranteInfo = $languesInfosParam[$langueActiveParam] ?? $languesInfosParam['fr'];
?>

<div class="page-profil">

    <?php require ROOT_PATH . '/assets/inc/back_home.php'; ?>

    <div class="topbar">
        <h1 data-i18n="parametres.titre">Paramètres</h1>
        <p class="profil-subtitle" data-i18n="parametres.sousTitre">Gérez vos informations personnelles, votre email, votre langue et votre mot de passe.</p>
    </div>

    <?php if ($succes): ?>
        <div class="alert-box alert-success"><?php echo htmlspecialchars($succes); ?></div>
    <?php endif; ?>

    <?php if ($erreur): ?>
        <div class="alert-box alert-error"><?php echo htmlspecialchars($erreur); ?></div>
    <?php endif; ?>

    <div class="profil-hero">
        <span class="profil-hero-avatar"><?php echo htmlspecialchars($initialesParam); ?></span>
        <div class="profil-hero-info">
            <strong><?php echo htmlspecialchars(trim($prenomParam . ' ' . $nomParam)); ?></strong>
            <span><?php echo htmlspecialchars($emailParam); ?></span>
        </div>
    </div>

    <div class="panel profil-card">
        <div class="profil-card-head">
            <i data-lucide="globe" aria-hidden="true"></i>
            <h2 data-i18n="parametres.langueTitre">Langue / Language</h2>
            <span class="lang-current-pill" data-i18n-aria="parametres.langueActuelle" aria-label="Langue actuelle">
                <span class="lang-code" data-i18n-lang-code><?php echo $langueCouranteInfo['code']; ?></span>
                <span class="lang-current-name" data-i18n-lang-current><?php echo htmlspecialchars($langueCouranteInfo['nom']); ?></span>
            </span>
        </div>
        <p class="form-hint" data-i18n="parametres.langueSousTitre">Choisissez la langue de l'application. Elle est mémorisée sur votre compte.</p>
        <?php $langSwitcherInline = true; require ROOT_PATH . '/assets/inc/lang_switcher.php'; ?>
    </div>

    <div class="panel profil-card">
        <div class="profil-card-head">
            <i data-lucide="user" aria-hidden="true"></i>
            <h2 data-i18n="parametres.infosTitre">Informations personnelles</h2>
        </div>

        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=parametres">
            <div class="form-grid">
                <div class="form-group">
                    <label for="prenom" data-i18n="parametres.prenomLabel">Prénom</label>
                    <input type="text" id="prenom" name="prenom"
                           value="<?php echo htmlspecialchars($profil['prenom'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="nom" data-i18n="parametres.nomLabel">Nom</label>
                    <input type="text" id="nom" name="nom"
                           value="<?php echo htmlspecialchars($profil['nom'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="telephone" data-i18n="parametres.telephoneLabel">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone"
                           value="<?php echo htmlspecialchars($profil['telephone'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="adresse" data-i18n="parametres.adresseLabel">Adresse</label>
                    <input type="text" id="adresse" name="adresse"
                           value="<?php echo htmlspecialchars($profil['adresse'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="ville" data-i18n="parametres.villeLabel">Ville</label>
                    <input type="text" id="ville" name="ville"
                           value="<?php echo htmlspecialchars($profil['ville'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="modifier_infos" class="btn btn-gold" data-i18n="parametres.enregistrerInfos">Enregistrer les modifications</button>
            </div>
        </form>
    </div>

    <div class="panel profil-card">
        <div class="profil-card-head">
            <i data-lucide="mail" aria-hidden="true"></i>
            <h2 data-i18n="parametres.emailTitre">Adresse email</h2>
        </div>

        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=parametres">
            <div class="form-grid">
                <div class="form-group">
                    <label for="email" data-i18n="parametres.emailLabel">Email</label>
                    <input type="email" id="email" name="email"
                           value="<?php echo htmlspecialchars($profil['email'] ?? ''); ?>" required>
                    <small class="form-hint" data-i18n="parametres.emailHint">Votre email sert à vous connecter. Il doit être unique.</small>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="modifier_email" class="btn btn-gold" data-i18n="parametres.changerEmail">Changer l'adresse email</button>
            </div>
        </form>
    </div>

    <div class="panel profil-card">
        <div class="profil-card-head">
            <i data-lucide="lock" aria-hidden="true"></i>
            <h2 data-i18n="parametres.mdpTitre">Changer le mot de passe</h2>
        </div>

        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=parametres">
            <div class="form-grid">
                <div class="form-group">
                    <label for="ancien_mdp" data-i18n="parametres.mdpActuel">Mot de passe actuel</label>
                    <input type="password" id="ancien_mdp" name="ancien_mdp" required>
                </div>

                <div class="form-group">
                    <label for="nouveau_mdp" data-i18n="parametres.nouveauMdp">Nouveau mot de passe</label>
                    <input type="password" id="nouveau_mdp" name="nouveau_mdp" minlength="6" required>
                </div>

                <div class="form-group">
                    <label for="confirmation_mdp" data-i18n="parametres.confirmationMdp">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="confirmation_mdp" name="confirmation_mdp" minlength="6" required>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="changer_mdp" class="btn btn-gold" data-i18n="parametres.changerMdp">Changer le mot de passe</button>
            </div>
        </form>
    </div>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
