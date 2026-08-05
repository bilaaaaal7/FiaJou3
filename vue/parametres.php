<?php
$pageTitle = "Paramètres - " . APP_NAME;
$pageHeading = "Paramètres";
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<div style="max-width: 800px; margin: 0 auto;">

    <?php if ($succes): ?>
        <div class="alert-box alert-success"><?php echo htmlspecialchars($succes); ?></div>
    <?php endif; ?>

    <?php if ($erreur): ?>
        <div class="alert-box alert-error"><?php echo htmlspecialchars($erreur); ?></div>
    <?php endif; ?>

    <div class="panel">
        <h2>Informations personnelles</h2>
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=parametres">
            <div class="form-grid">
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($profil['prenom'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($profil['nom'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" value="<?php echo htmlspecialchars($profil['telephone'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <input type="text" id="adresse" name="adresse" value="<?php echo htmlspecialchars($profil['adresse'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="ville">Ville</label>
                    <input type="text" id="ville" name="ville" value="<?php echo htmlspecialchars($profil['ville'] ?? ''); ?>">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" name="modifier_infos" class="btn btn-gold">Enregistrer les modifications</button>
            </div>
        </form>
    </div>

    <div class="panel" style="margin-top:24px;">
        <h2>Adresse email</h2>
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=parametres">
            <div class="form-grid">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($profil['email'] ?? ''); ?>" required>
                    <small class="form-hint">Votre email sert à vous connecter. Il doit être unique.</small>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" name="modifier_email" class="btn btn-gold">Changer l'adresse email</button>
            </div>
        </form>
    </div>

    <div class="panel" style="margin-top:24px;">
        <h2>Changer le mot de passe</h2>
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=parametres">
            <div class="form-grid">
                <div class="form-group">
                    <label for="ancien_mdp">Mot de passe actuel</label>
                    <input type="password" id="ancien_mdp" name="ancien_mdp" required>
                </div>
                <div class="form-group">
                    <label for="nouveau_mdp">Nouveau mot de passe</label>
                    <input type="password" id="nouveau_mdp" name="nouveau_mdp" minlength="6" required>
                </div>
                <div class="form-group">
                    <label for="confirmation_mdp">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="confirmation_mdp" name="confirmation_mdp" minlength="6" required>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" name="changer_mdp" class="btn btn-gold">Changer le mot de passe</button>
            </div>
        </form>
    </div>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
