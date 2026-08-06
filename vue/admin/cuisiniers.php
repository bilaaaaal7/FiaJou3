<?php
$pageTitle = "Gestion des cuisiniers - " . APP_NAME;
$pageHeading = "Gestion des cuisiniers";
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
$formOuvert = !empty($idModifier) || !empty($erreur);
?>

<?php if (!empty($erreur)): ?>
    <div class="alert-box alert-error"><?php echo htmlspecialchars($erreur); ?></div>
<?php endif; ?>

<?php if (isset($_GET['succes'])): ?>
    <div class="alert-box alert-success">Cuisinier ajouté avec succès.</div>
<?php endif; ?>

<div class="panel">
    <div class="panel-head-actions">
        <h2>Liste des cuisiniers</h2>
        <button type="button" id="btnToggleFormCuisinier"
                class="btn <?php echo $formOuvert ? 'btn-outline' : 'btn-gold'; ?>"
                aria-expanded="<?php echo $formOuvert ? 'true' : 'false'; ?>">
            <?php echo $formOuvert ? 'Annuler' : 'Ajouter un cuisinier'; ?>
        </button>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($cuisiniers as $c): ?>
                <tr>
                    <td><?php echo $c['id']; ?></td>
                    <td><?php echo htmlspecialchars($c['prenom']); ?></td>
                    <td><?php echo htmlspecialchars($c['nom']); ?></td>
                    <td><?php echo htmlspecialchars($c['email']); ?></td>
                    <td>
                        <?php if ($c['actif']): ?>
                            <span class="badge-yes">Actif</span>
                        <?php else: ?>
                            <span class="badge-no">Inactif</span>
                        <?php endif; ?>
                    </td>
                    <td class="actions-cell">
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/cuisiniers&modifier=<?php echo $c['id']; ?>" class="btn btn-outline btn-sm">Modifier</a>
                        <?php if ($c['actif']): ?>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/cuisiniers&desactiver=<?php echo $c['id']; ?>" class="btn btn-danger btn-sm" data-confirm="Désactiver ce cuisinier ?">Désactiver</a>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/cuisiniers&activer=<?php echo $c['id']; ?>" class="btn btn-gold btn-sm">Activer</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($cuisiniers)): ?>
                <tr><td colspan="6" class="empty-state">Aucun cuisinier enregistré.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="form-collapse <?php echo $formOuvert ? 'open' : ''; ?>" id="collapseFormCuisinier">
    <div class="form-collapse__inner">
        <div class="panel">
            <h2><?php echo $idModifier ? 'Modifier le cuisinier' : 'Ajouter un cuisinier'; ?></h2>
            <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/cuisiniers">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Prénom</label>
                        <input type="text" name="prenom" value="<?php echo htmlspecialchars($prenom); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Nom</label>
                        <input type="text" name="nom" value="<?php echo htmlspecialchars($nom); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="text" name="telephone" value="<?php echo htmlspecialchars($telephone); ?>">
                    </div>
                    <?php if (!$idModifier): ?>
                    <div class="form-group">
                        <label>Mot de passe</label>
                        <input type="password" name="password" minlength="6" required>
                    </div>
                    <?php endif; ?>
                </div>
                <input type="hidden" name="id" value="<?php echo $idModifier; ?>">
                <div class="form-actions">
                    <?php if ($idModifier): ?>
                        <button type="submit" name="modifier" class="btn btn-gold">Modifier</button>
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/cuisiniers" class="btn btn-outline">Annuler</a>
                    <?php else: ?>
                        <button type="submit" name="ajouter" class="btn btn-gold">Ajouter</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var btn = document.getElementById('btnToggleFormCuisinier');
    var collapse = document.getElementById('collapseFormCuisinier');
    if (!btn || !collapse) { return; }
    var labelOuvert = 'Annuler';
    var labelFerme = 'Ajouter un cuisinier';

    function majBouton(open) {
        btn.textContent = open ? labelOuvert : labelFerme;
        btn.classList.toggle('btn-outline', open);
        btn.classList.toggle('btn-gold', !open);
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    btn.addEventListener('click', function () {
        majBouton(collapse.classList.toggle('open'));
    });
})();
</script>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
