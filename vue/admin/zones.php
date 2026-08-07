<?php
$pageTitle = "Zones de livraison - " . APP_NAME;
$pageHeading = "Zones de livraison";
$extraCss = ['admin.css'];
$extraJs = ['modal-form.js'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
$formOuvert = !empty($idModifier) || !empty($erreur);
$formMode = !empty($idModifier) ? 'edit' : 'add';
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
        <button type="button" class="btn btn-gold" data-modal-open="modalFormZone" data-mode="add">Ajouter une zone</button>
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
                        <button type="button" class="btn btn-outline btn-sm" data-modal-open="modalFormZone" data-mode="edit"
                            data-fields='<?php echo htmlspecialchars(json_encode([
                                'id' => (int) $zone['id'],
                                'nom' => $zone['nom'],
                                'prix_livraison' => $zone['prix_livraison'],
                            ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES); ?>'>Modifier</button>
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

<div class="modal-overlay" id="modalFormZone" hidden>
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalFormZoneTitle">
        <div class="modal-head">
            <h3 id="modalFormZoneTitle" data-title-add="Ajouter une zone" data-title-edit="Modifier la zone"><?php echo $formMode === 'edit' ? 'Modifier la zone' : 'Ajouter une zone'; ?></h3>
            <button type="button" class="modal-close" data-modal-close aria-label="Fermer">&times;</button>
        </div>
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/zones">
            <div class="form-stack">
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
            <p class="modal-error" hidden><?php echo htmlspecialchars($erreur ?? ''); ?></p>
            <div class="form-actions">
                <button type="submit" name="<?php echo $formMode === 'edit' ? 'modifier' : 'ajouter'; ?>" class="btn btn-gold"
                    data-name-add="ajouter" data-name-edit="modifier"
                    data-label-add="Ajouter" data-label-edit="Modifier"><?php echo $formMode === 'edit' ? 'Modifier' : 'Ajouter'; ?></button>
                <button type="button" class="btn btn-outline" data-modal-close>Annuler</button>
            </div>
        </form>
    </div>
</div>

<?php if ($formOuvert): ?>
<script>
    window.addEventListener('DOMContentLoaded', function () {
        if (window.ouvrirModalForm) {
            window.ouvrirModalForm('modalFormZone', '<?php echo $formMode; ?>', null, <?php echo json_encode($erreur ?? '', JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP); ?>);
        }
    });
</script>
<?php endif; ?>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
