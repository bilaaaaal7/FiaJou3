<?php
$pageTitle = "Gestion des plats - " . APP_NAME;
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<h1>Gestion des plats</h1>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger py-2" role="alert"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="panel">
    <h2>Liste des plats</h2>
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
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/plats&modifier=<?php echo $plat['id']; ?>" class="btn btn-outline btn-sm">Modifier</a>
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

<div class="panel">
    <h2><?php echo $idModifier != "" ? 'Modifier le plat' : 'Ajouter un plat'; ?></h2>
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
        <div class="form-actions">
            <?php if ($idModifier != ""): ?>
                <button type="submit" name="modifier" class="btn btn-gold">Modifier</button>
                <a href="<?php echo BASE_URL; ?>/index.php?route=admin/plats" class="btn btn-outline">Annuler</a>
            <?php else: ?>
                <button type="submit" name="ajouter" class="btn btn-gold">Ajouter</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
