<?php
$pageTitle = "Zones de livraison - " . APP_NAME;
$pageHeading = "Zones de livraison";
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
$formOuvert = !empty($idModifier) || !empty($erreur);
?>

<?php if (!empty($erreur)): ?>
    <div class="alert-box alert-error"><?php echo htmlspecialchars($erreur); ?></div>
<?php endif; ?>

<?php if (isset($_GET['succes'])): ?>
    <div class="alert-box alert-success">Zone ajoutée avec succès.</div>
<?php endif; ?>

<div class="panel">
    <div class="panel-head-actions">
        <h2>Liste des zones</h2>
        <button type="button" id="btnToggleFormZone"
                class="btn <?php echo $formOuvert ? 'btn-outline' : 'btn-gold'; ?>"
                aria-expanded="<?php echo $formOuvert ? 'true' : 'false'; ?>">
            <?php echo $formOuvert ? 'Annuler' : 'Ajouter une zone'; ?>
        </button>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prix livraison</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($zones as $zone): ?>
                <tr>
                    <td><?php echo $zone['id']; ?></td>
                    <td><?php echo htmlspecialchars($zone['nom']); ?></td>
                    <td><?php echo number_format($zone['prix_livraison'], 2); ?> DH</td>
                    <td class="actions-cell">
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/zones&modifier=<?php echo $zone['id']; ?>" class="btn btn-outline btn-sm">Modifier</a>
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/zones&supprimer=<?php echo $zone['id']; ?>" class="btn btn-danger btn-sm" data-confirm="Supprimer cette zone ?">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($zones)): ?>
                <tr><td colspan="4" class="empty-state">Aucune zone configurée.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="form-collapse <?php echo $formOuvert ? 'open' : ''; ?>" id="collapseFormZone">
    <div class="form-collapse__inner">
        <div class="panel">
            <h2><?php echo $idModifier ? 'Modifier la zone' : 'Ajouter une zone'; ?></h2>
            <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/zones">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nom</label>
                        <input type="text" name="nom" value="<?php echo htmlspecialchars($nom); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Prix de livraison (DH)</label>
                        <input type="number" step="0.01" min="0" name="prix_livraison" value="<?php echo htmlspecialchars($prix); ?>" required>
                    </div>
                </div>
                <input type="hidden" name="id" value="<?php echo $idModifier; ?>">
                <div class="form-actions">
                    <?php if ($idModifier): ?>
                        <button type="submit" name="modifier" class="btn btn-gold">Modifier</button>
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/zones" class="btn btn-outline">Annuler</a>
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
    var btn = document.getElementById('btnToggleFormZone');
    var collapse = document.getElementById('collapseFormZone');
    if (!btn || !collapse) { return; }
    var labelOuvert = 'Annuler';
    var labelFerme = 'Ajouter une zone';

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
