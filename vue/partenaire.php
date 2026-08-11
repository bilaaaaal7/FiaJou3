<?php
/**
 * Vue : Dossier partenaire (cuisinier / livreur)
 * Ouverte depuis le lien sécurisé reçu par email.
 * Même habillage que la page d'inscription client (auth-card), seul le
 * contenu change : l'email est pré-rempli à partir de l'invitation.
 */

$pageTitle = (isset($roleLabel) && $roleLabel !== '' ? ucfirst($roleLabel) : 'Devenir partenaire') . ' - ' . APP_NAME;
$extraCss = ['auth.css'];
$extraJs = ['i18n.js'];
$i18nActive = true;
$i18nPage = 'partenaire';
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
                <?php if ($error !== ''): ?>
                    <h2 class="login-title" data-i18n="partenaire.errorTitle">Lien invalide</h2>
                    <p class="login-subtitle" data-i18n="partenaire.errorSubtitle">Impossible d'ouvrir le formulaire.</p>
                    <div class="alert alert-danger py-2" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                    <div class="d-grid mt-4">
                        <a href="<?php echo BASE_URL; ?>/index.php?route=accueil" class="btn btn-gold" data-i18n="partenaire.backHome">Retour à l'accueil</a>
                    </div>
                <?php else: ?>
                    <h2 class="login-title" data-i18n="partenaire.title"><?php echo htmlspecialchars(ucfirst($roleLabel)); ?></h2>
                    <p class="login-subtitle" data-i18n="partenaire.subtitle">Complétez votre dossier de candidature</p>

                    <div class="alert alert-info py-2" role="alert">
                        <span data-i18n="partenaire.emailNote">Le formulaire est lié à l'adresse utilisée pour demander le lien :</span>
                        <strong><?php echo htmlspecialchars($invitation['email']); ?></strong>
                    </div>

                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger py-2" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=partenaire&token=<?php echo urlencode($invitation['token']); ?>" autocomplete="off" spellcheck="false">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($invitation['token']); ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="prenom" class="form-label" data-i18n="partenaire.prenomLabel">Prénom</label>
                                <input type="text" class="form-control" id="prenom" name="prenom"
                                       value="<?php echo htmlspecialchars($prenom ?? ''); ?>"
                                       autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                       readonly onfocus="this.removeAttribute('readonly')" required>
                            </div>

                            <div class="col-md-6">
                                <label for="nom" class="form-label" data-i18n="partenaire.nomLabel">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom"
                                       value="<?php echo htmlspecialchars($nom ?? ''); ?>"
                                       autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                       readonly onfocus="this.removeAttribute('readonly')" required>
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label" data-i18n="partenaire.emailLabel">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?php echo htmlspecialchars($invitation['email']); ?>"
                                       readonly autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                                <small class="form-text text-muted" data-i18n="partenaire.emailLocked">Email pré-rempli à partir de votre lien — non modifiable.</small>
                            </div>

                            <div class="col-md-6">
                                <label for="telephone" class="form-label" data-i18n="partenaire.telephoneLabel">Téléphone</label>
                                <input type="tel" class="form-control" id="telephone" name="telephone"
                                       value="<?php echo htmlspecialchars($telephone ?? ''); ?>"
                                       autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                       readonly onfocus="this.removeAttribute('readonly')" required>
                            </div>

                            <div class="col-md-6">
                                <label for="ville" class="form-label" data-i18n="partenaire.villeLabel">Ville</label>
                                <input type="text" class="form-control" id="ville" name="ville"
                                       value="<?php echo htmlspecialchars($ville ?? ''); ?>"
                                       autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                       readonly onfocus="this.removeAttribute('readonly')" required>
                            </div>

                            <div class="col-12">
                                <label for="adresse" class="form-label" data-i18n="partenaire.adresseLabel">Adresse</label>
                                <input type="text" class="form-control" id="adresse" name="adresse"
                                       value="<?php echo htmlspecialchars($adresse ?? ''); ?>"
                                       autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                       readonly onfocus="this.removeAttribute('readonly')" required>
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label" data-i18n="partenaire.passwordLabel">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password"
                                       autocomplete="new-password"
                                       readonly onfocus="this.removeAttribute('readonly')" required>
                            </div>

                            <div class="col-md-6">
                                <label for="confirmation" class="form-label" data-i18n="partenaire.confirmationLabel">Confirmer le mot de passe</label>
                                <input type="password" class="form-control" id="confirmation" name="confirmation"
                                       autocomplete="new-password"
                                       readonly onfocus="this.removeAttribute('readonly')" required>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" name="partenaire" class="btn btn-gold" data-i18n="partenaire.submitBtn">Valider mon dossier</button>
                        </div>
                    </form>

                    <div class="divider-diamond">
                        <hr><span></span><hr>
                    </div>

                    <p class="register-link">
                        <a href="<?php echo BASE_URL; ?>/index.php?route=connexion" data-i18n="partenaire.loginLink">Déjà un compte ? Connectez-vous</a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        <?php
        // Titre du dossier dynamique selon le rôle, traduit pour chaque langue.
        // Le moteur i18n (assets/js/i18n.js) fusionne ces clés dans le
        // dictionnaire central via window.FJ_I18N_EXTRA.
        $estCuisinier = ($invitation['role'] ?? '') === 'cuisinier';
        $titresPartenaire = [
            'fr' => ucfirst($roleLabel),
            'en' => $estCuisinier ? 'Partner Chef' : 'Partner Courier',
            'ar' => $estCuisinier ? 'طباخ شريك' : 'موصل شريك',
        ];
        ?>
        window.FJ_I18N_EXTRA = {
            fr: {
                'partenaire.title': <?php echo json_encode($titresPartenaire['fr'], JSON_UNESCAPED_UNICODE); ?>,
                'partenaire.pageTitle': <?php echo json_encode($pageTitle, JSON_UNESCAPED_UNICODE); ?>
            },
            en: {
                'partenaire.title': <?php echo json_encode($titresPartenaire['en'], JSON_UNESCAPED_UNICODE); ?>,
                'partenaire.pageTitle': <?php echo json_encode($pageTitle, JSON_UNESCAPED_UNICODE); ?>
            },
            ar: {
                'partenaire.title': <?php echo json_encode($titresPartenaire['ar'], JSON_UNESCAPED_UNICODE); ?>,
                'partenaire.pageTitle': <?php echo json_encode($pageTitle, JSON_UNESCAPED_UNICODE); ?>
            }
        };
    </script>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
