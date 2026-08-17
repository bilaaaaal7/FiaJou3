<?php
$pageTitle = "Catégories - " . APP_NAME;
$i18nPage = 'admin_categories';
$pageHeading = "Catégories";
$pageHeadingI18n = 'admin_categories.pageHeading';
$extraCss = ['admin.css'];
$extraJs = ['modal-form.js'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
$formOuvert = !empty($idModifier) || !empty($error);
$formMode = !empty($idModifier) ? 'edit' : 'add';
?>

<?php if (!empty($error)): ?>
    <div class="alert-box alert-error"><?php echo render_i18n($error); ?></div>
<?php endif; ?>

<div class="panel">
    <div class="panel-head-actions">
        <h2 data-i18n="admin_categories.listeCategories">Liste des catégories</h2>
        <button type="button" id="btnAjouterCategorie" class="btn btn-gold" data-modal-open="modalFormCategorie" data-mode="add"><span data-i18n="admin_categories.ajouterCategorie">Ajouter une catégorie</span></button>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th data-i18n="common.nom">Nom</th>
                    <th data-i18n="common.description">Description</th>
                    <th data-i18n="common.image">Image</th>
                    <th data-i18n="common.actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($categories as $categorie): ?>
                <tr>
                    <td><?php echo $categorie['id']; ?></td>
                    <td><?php echo htmlspecialchars(localiser($categorie, 'nom')); ?></td>
                    <td><?php echo htmlspecialchars(localiser($categorie, 'description')); ?></td>
                    <td><?php echo htmlspecialchars($categorie['image'] ?? '-'); ?></td>
                    <td class="actions-cell">
                        <button type="button" class="btn btn-outline btn-sm" data-modal-open="modalFormCategorie" data-mode="edit"
                            data-fields='<?php echo htmlspecialchars(json_encode([
                                'id' => (int) $categorie['id'],
                                'nom' => $categorie['nom'],
                                'nom_en' => $categorie['nom_en'] ?? '',
                                'nom_ar' => $categorie['nom_ar'] ?? '',
                                'description' => $categorie['description'] ?? '',
                                'description_en' => $categorie['description_en'] ?? '',
                                'description_ar' => $categorie['description_ar'] ?? '',
                            ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES); ?>'><span data-i18n="common.modifier">Modifier</span></button>
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/categories&supprimer=<?php echo $categorie['id']; ?>" class="btn btn-danger btn-sm" data-confirm="Supprimer cette catégorie ?" data-confirm-i18n="admin_categories.confirmSupprimer"><span data-i18n="common.supprimer">Supprimer</span></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($categories)): ?>
                <tr><td colspan="5" class="empty-state" data-i18n="admin_categories.aucuneCategorie">Aucune catégorie.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="modalFormCategorie" hidden>
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalFormCategorieTitle">
        <div class="modal-head">
            <h3 id="modalFormCategorieTitle" data-title-add="admin_categories.titreAjouter" data-title-edit="admin_categories.titreModifier"><?php echo $formMode === 'edit' ? 'Modifier la catégorie' : 'Ajouter une catégorie'; ?></h3>
            <button type="button" class="modal-close" data-modal-close aria-label="Fermer" data-i18n-aria="common.fermer">&times;</button>
        </div>
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/categories" enctype="multipart/form-data">
            <div class="form-stack">
                <div class="form-group">
                    <label data-i18n="common.nom">Nom</label>
                    <input type="text" name="nom" value="<?php echo htmlspecialchars($nom); ?>" required>
                </div>
                <div class="form-group">
                    <label data-i18n="admin_categories.nomEn">Nom (EN)</label>
                    <input type="text" name="nom_en" value="<?php echo htmlspecialchars($nomEn); ?>" placeholder="English name">
                </div>
                <div class="form-group">
                    <label data-i18n="admin_categories.nomAr">Nom (AR)</label>
                    <input type="text" name="nom_ar" value="<?php echo htmlspecialchars($nomAr); ?>" placeholder="الاسم بالعربية" dir="rtl">
                </div>
                <div class="form-group">
                    <label data-i18n="common.description">Description</label>
                    <input type="text" name="description" value="<?php echo htmlspecialchars($description); ?>" required>
                </div>
                <div class="form-group">
                    <label data-i18n="admin_categories.descriptionEn">Description (EN)</label>
                    <input type="text" name="description_en" value="<?php echo htmlspecialchars($descriptionEn); ?>" placeholder="English description">
                </div>
                <div class="form-group">
                    <label data-i18n="admin_categories.descriptionAr">Description (AR)</label>
                    <input type="text" name="description_ar" value="<?php echo htmlspecialchars($descriptionAr); ?>" placeholder="الوصف بالعربية" dir="rtl">
                </div>
                <div class="form-group">
                    <label data-i18n="admin_categories.imageLabel">Image (fichier)</label>
                    <input type="file" name="image" accept="image/*">
                    <small class="form-hint" data-i18n="admin_categories.imageHint">Laisser vide pour conserver l'image actuelle.</small>
                </div>
            </div>
            <input type="hidden" name="id" value="<?php echo $idModifier; ?>">
            <p class="modal-error" hidden><?php echo render_i18n($error ?? ''); ?></p>
            <div class="form-actions">
                <button type="submit" name="<?php echo $formMode === 'edit' ? 'modifier' : 'ajouter'; ?>" class="btn btn-gold"
                    data-name-add="ajouter" data-name-edit="modifier"
                    data-label-add="common.ajouter" data-label-edit="common.modifier"><?php echo $formMode === 'edit' ? 'Modifier' : 'Ajouter'; ?></button>
                <button type="button" class="btn btn-outline" data-modal-close><span data-i18n="common.annuler">Annuler</span></button>
            </div>
        </form>
    </div>
</div>

<?php if ($formOuvert): ?>
<script>
    window.addEventListener('DOMContentLoaded', function () {
        if (window.ouvrirModalForm) {
            window.ouvrirModalForm('modalFormCategorie', '<?php echo $formMode; ?>', null, <?php echo json_encode(cle_i18n($error ?? ''), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP); ?>);
        }
    });
</script>
<?php endif; ?>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
