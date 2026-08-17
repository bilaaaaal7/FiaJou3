<?php
$pageTitle = "Gestion des plats - " . APP_NAME;
$i18nPage = 'admin_plats';
$pageHeading = "Produits";
$pageHeadingI18n = 'admin_plats.pageHeading';
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
        <h2 data-i18n="admin_plats.listePlats">Liste des plats</h2>
        <button type="button" id="btnAjouterPlat" class="btn btn-gold" data-modal-open="modalFormPlat" data-mode="add"><span data-i18n="admin_plats.ajouterPlat">Ajouter un plat</span></button>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th data-i18n="common.image">Image</th>
                    <th data-i18n="common.nom">Nom</th>
                    <th data-i18n="common.categorie">Catégorie</th>
                    <th data-i18n="common.description">Description</th>
                    <th data-i18n="common.prix">Prix</th>
                    <th data-i18n="common.disponible">Disponible</th>
                    <th data-i18n="common.actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($plats as $plat): ?>
                <tr>
                    <td><?php echo $plat['id']; ?></td>
                    <td>
                        <?php if ($plat['image']): ?>
                            <img class="thumb" src="<?php echo UPLOADS_URL; ?>/<?php echo htmlspecialchars($plat['image']); ?>" alt="<?php echo htmlspecialchars(localiser($plat, 'nom')); ?>">
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars(localiser($plat, 'nom')); ?></td>
                    <td>
                        <?php
                        foreach ($categories as $cat) {
                            if ($cat['id'] == $plat['category_id']) {
                                echo htmlspecialchars(localiser($cat, 'nom'));
                                break;
                            }
                        }
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars(mb_strimwidth(localiser($plat, 'description'), 0, 40, '...')); ?></td>
                    <td><?php echo number_format($plat['prix'], 2); ?> DH</td>
                    <td>
                        <?php if ($plat['disponible']): ?>
                            <span class="badge-yes" data-i18n="common.oui">Oui</span>
                        <?php else: ?>
                            <span class="badge-no" data-i18n="common.non">Non</span>
                        <?php endif; ?>
                    </td>
                    <td class="actions-cell">
                        <button type="button" class="btn btn-outline btn-sm" data-modal-open="modalFormPlat" data-mode="edit"
                            data-fields='<?php echo htmlspecialchars(json_encode([
                                'id' => (int) $plat['id'],
                                'nom' => $plat['nom'],
                                'nom_en' => $plat['nom_en'] ?? '',
                                'nom_ar' => $plat['nom_ar'] ?? '',
                                'description' => $plat['description'] ?? '',
                                'description_en' => $plat['description_en'] ?? '',
                                'description_ar' => $plat['description_ar'] ?? '',
                                'prix' => $plat['prix'],
                                'category_id' => (int) $plat['category_id'],
                                'disponible' => (int) $plat['disponible'],
                                'ancienne_image' => $plat['image'] ?? '',
                            ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES); ?>'><span data-i18n="common.modifier">Modifier</span></button>
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/plats&supprimer=<?php echo $plat['id']; ?>" class="btn btn-danger btn-sm" data-confirm="Supprimer ce plat ?" data-confirm-i18n="admin_plats.confirmSupprimer"><span data-i18n="common.supprimer">Supprimer</span></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($plats)): ?>
                <tr><td colspan="8" class="empty-state" data-i18n="admin_plats.aucunPlat">Aucun plat.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="modalFormPlat" hidden>
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalFormPlatTitle">
        <div class="modal-head">
            <h3 id="modalFormPlatTitle" data-title-add="admin_plats.titreAjouter" data-title-edit="admin_plats.titreModifier"><?php echo $formMode === 'edit' ? 'Modifier le plat' : 'Ajouter un plat'; ?></h3>
            <button type="button" class="modal-close" data-modal-close aria-label="Fermer" data-i18n-aria="common.fermer">&times;</button>
        </div>
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/plats" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label data-i18n="common.categorie">Catégorie</label>
                    <select name="category_id" required>
                        <?php foreach ($categories as $categorie): ?>
                            <option value="<?php echo $categorie['id']; ?>" <?php if ($categorie['id'] == $category_id) echo "selected"; ?>>
                                <?php echo htmlspecialchars(localiser($categorie, 'nom')); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label data-i18n="common.nom">Nom</label>
                    <input type="text" name="nom" value="<?php echo htmlspecialchars($nom); ?>" required>
                </div>
                <div class="form-group">
                    <label data-i18n="admin_plats.nomEn">Nom (EN)</label>
                    <input type="text" name="nom_en" value="<?php echo htmlspecialchars($nomEn); ?>" placeholder="English name">
                </div>
                <div class="form-group">
                    <label data-i18n="admin_plats.nomAr">Nom (AR)</label>
                    <input type="text" name="nom_ar" value="<?php echo htmlspecialchars($nomAr); ?>" placeholder="الاسم بالعربية" dir="rtl">
                </div>
                <div class="form-group">
                    <label data-i18n="common.description">Description</label>
                    <input type="text" name="description" value="<?php echo htmlspecialchars($description); ?>" required>
                </div>
                <div class="form-group">
                    <label data-i18n="admin_plats.descriptionEn">Description (EN)</label>
                    <input type="text" name="description_en" value="<?php echo htmlspecialchars($descriptionEn); ?>" placeholder="English description">
                </div>
                <div class="form-group">
                    <label data-i18n="admin_plats.descriptionAr">Description (AR)</label>
                    <input type="text" name="description_ar" value="<?php echo htmlspecialchars($descriptionAr); ?>" placeholder="الوصف بالعربية" dir="rtl">
                </div>
                <div class="form-group">
                    <label data-i18n="common.prix">Prix (DH)</label>
                    <input type="number" step="0.01" min="0" name="prix" value="<?php echo htmlspecialchars($prix); ?>" required>
                </div>
                <div class="form-group">
                    <label data-i18n="admin_plats.imageLabel">Image</label>
                    <input type="file" name="image" accept="image/*">
                    <small class="form-hint" data-i18n="admin_plats.imageHint">Laisser vide pour conserver l'image actuelle.</small>
                </div>
                <div class="form-group">
                    <label data-i18n="common.disponible">Disponible</label>
                    <select name="disponible">
                        <option value="1" <?php if ($disponible) echo 'selected'; ?> data-i18n="common.oui">Oui</option>
                        <option value="0" <?php if (!$disponible) echo 'selected'; ?> data-i18n="common.non">Non</option>
                    </select>
                </div>
            </div>
            <input type="hidden" name="id" value="<?php echo $idModifier; ?>">
            <input type="hidden" name="ancienne_image" value="<?php echo htmlspecialchars($image); ?>">
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
            window.ouvrirModalForm('modalFormPlat', '<?php echo $formMode; ?>', null, <?php echo json_encode(cle_i18n($error ?? ''), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP); ?>);
        }
    });
</script>
<?php endif; ?>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
