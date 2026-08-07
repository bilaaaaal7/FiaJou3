<?php
$pageTitle = "Gestion des plats - " . APP_NAME;
$pageHeading = "Gestion des plats";
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
        <h2>Liste des plats</h2>
        <button type="button" id="btnAjouterPlat" class="btn btn-gold" data-modal-open="modalFormPlat" data-mode="add">Ajouter un plat</button>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Description</th>
                    <th>Prix</th>
                    <th>Disponible</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($plats as $plat): ?>
                <tr>
                    <td><?php echo $plat['id']; ?></td>
                    <td>
                        <?php if ($plat['image']): ?>
                            <img class="thumb" src="<?php echo UPLOADS_URL; ?>/<?php echo htmlspecialchars($plat['image']); ?>" alt="<?php echo htmlspecialchars($plat['nom']); ?>">
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($plat['nom']); ?></td>
                    <td>
                        <?php
                        foreach ($categories as $cat) {
                            if ($cat['id'] == $plat['category_id']) {
                                echo htmlspecialchars($cat['nom']);
                                break;
                            }
                        }
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars(mb_strimwidth($plat['description'] ?? '', 0, 40, '...')); ?></td>
                    <td><?php echo number_format($plat['prix'], 2); ?> DH</td>
                    <td>
                        <?php if ($plat['disponible']): ?>
                            <span class="badge-yes">Oui</span>
                        <?php else: ?>
                            <span class="badge-no">Non</span>
                        <?php endif; ?>
                    </td>
                    <td class="actions-cell">
                        <button type="button" class="btn btn-outline btn-sm" data-modal-open="modalFormPlat" data-mode="edit"
                            data-fields='<?php echo htmlspecialchars(json_encode([
                                'id' => (int) $plat['id'],
                                'nom' => $plat['nom'],
                                'description' => $plat['description'] ?? '',
                                'prix' => $plat['prix'],
                                'category_id' => (int) $plat['category_id'],
                                'disponible' => (int) $plat['disponible'],
                                'ancienne_image' => $plat['image'] ?? '',
                            ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES); ?>'>Modifier</button>
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/plats&supprimer=<?php echo $plat['id']; ?>" class="btn btn-danger btn-sm" data-confirm="Supprimer ce plat ?">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($plats)): ?>
                <tr><td colspan="8" class="empty-state">Aucun plat.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="modalFormPlat" hidden>
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalFormPlatTitle">
        <div class="modal-head">
            <h3 id="modalFormPlatTitle" data-title-add="Ajouter un plat" data-title-edit="Modifier le plat"><?php echo $formMode === 'edit' ? 'Modifier le plat' : 'Ajouter un plat'; ?></h3>
            <button type="button" class="modal-close" data-modal-close aria-label="Fermer">&times;</button>
        </div>
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/plats" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label>Catégorie</label>
                    <select name="category_id" required>
                        <?php foreach ($categories as $categorie): ?>
                            <option value="<?php echo $categorie['id']; ?>" <?php if ($categorie['id'] == $category_id) echo "selected"; ?>>
                                <?php echo htmlspecialchars($categorie['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" value="<?php echo htmlspecialchars($nom); ?>" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" name="description" value="<?php echo htmlspecialchars($description); ?>" required>
                </div>
                <div class="form-group">
                    <label>Prix (DH)</label>
                    <input type="number" step="0.01" min="0" name="prix" value="<?php echo htmlspecialchars($prix); ?>" required>
                </div>
                <div class="form-group">
                    <label>Image</label>
                    <input type="file" name="image" accept="image/*">
                </div>
                <div class="form-group">
                    <label>Disponible</label>
                    <select name="disponible">
                        <option value="1" <?php if ($disponible) echo 'selected'; ?>>Oui</option>
                        <option value="0" <?php if (!$disponible) echo 'selected'; ?>>Non</option>
                    </select>
                </div>
            </div>
            <input type="hidden" name="id" value="<?php echo $idModifier; ?>">
            <input type="hidden" name="ancienne_image" value="<?php echo htmlspecialchars($image); ?>">
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
            window.ouvrirModalForm('modalFormPlat', '<?php echo $formMode; ?>', null, <?php echo json_encode($error ?? '', JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP); ?>);
        }
    });
</script>
<?php endif; ?>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
