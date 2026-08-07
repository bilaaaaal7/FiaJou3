<?php
$pageTitle = "Catégories - " . APP_NAME;
$pageHeading = "Gestion des catégories";
$extraCss = ['admin.css'];
$extraJs = ['modal-form.js'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
$formOuvert = !empty($idModifier) || !empty($error);
$formMode = !empty($idModifier) ? 'edit' : 'add';
?>

<?php if (!empty($error)): ?>
    <div class="alert-box alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="panel">
    <div class="panel-head-actions">
        <h2>Liste des catégories</h2>
        <button type="button" id="btnAjouterCategorie" class="btn btn-gold" data-modal-open="modalFormCategorie" data-mode="add">Ajouter une catégorie</button>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($categories as $categorie): ?>
                <tr>
                    <td><?php echo $categorie['id']; ?></td>
                    <td><?php echo htmlspecialchars($categorie['nom']); ?></td>
                    <td><?php echo htmlspecialchars($categorie['description'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($categorie['image'] ?? '-'); ?></td>
                    <td class="actions-cell">
                        <button type="button" class="btn btn-outline btn-sm" data-modal-open="modalFormCategorie" data-mode="edit"
                            data-fields='<?php echo htmlspecialchars(json_encode([
                                'id' => (int) $categorie['id'],
                                'nom' => $categorie['nom'],
                                'description' => $categorie['description'] ?? '',
                            ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES); ?>'>Modifier</button>
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/categories&supprimer=<?php echo $categorie['id']; ?>" class="btn btn-danger btn-sm" data-confirm="Supprimer cette catégorie ?">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($categories)): ?>
                <tr><td colspan="5" class="empty-state">Aucune catégorie.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="modalFormCategorie" hidden>
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalFormCategorieTitle">
        <div class="modal-head">
            <h3 id="modalFormCategorieTitle" data-title-add="Ajouter une catégorie" data-title-edit="Modifier la catégorie"><?php echo $formMode === 'edit' ? 'Modifier la catégorie' : 'Ajouter une catégorie'; ?></h3>
            <button type="button" class="modal-close" data-modal-close aria-label="Fermer">&times;</button>
        </div>
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/categories" enctype="multipart/form-data">
            <div class="form-stack">
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" value="<?php echo htmlspecialchars($nom); ?>" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" name="description" value="<?php echo htmlspecialchars($description); ?>" required>
                </div>
                <div class="form-group">
                    <label>Image (fichier)</label>
                    <input type="file" name="image" accept="image/*">
                    <small class="form-hint">Laisser vide pour conserver l'image actuelle.</small>
                </div>
            </div>
            <input type="hidden" name="id" value="<?php echo $idModifier; ?>">
            <p class="modal-error" hidden><?php echo htmlspecialchars($error ?? ''); ?></p>
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
            window.ouvrirModalForm('modalFormCategorie', '<?php echo $formMode; ?>', null, <?php echo json_encode($error ?? '', JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP); ?>);
        }
    });
</script>
<?php endif; ?>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
