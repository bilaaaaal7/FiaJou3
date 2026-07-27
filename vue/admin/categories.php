<?php
$pageTitle = "Catégories - " . APP_NAME;
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<h1>Gestion des catégories</h1>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger py-2" role="alert"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="panel">
    <h2>Liste des catégories</h2>
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
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/categories&modifier=<?php echo $categorie['id']; ?>" class="btn btn-outline btn-sm">Modifier</a>
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

<div class="panel">
    <h2><?php echo $idModifier ? 'Modifier la catégorie' : 'Ajouter une catégorie'; ?></h2>
    <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/categories">
        <div class="form-grid">
            <div class="form-group">
                <label>Nom</label>
                <input type="text" name="nom" value="<?php echo htmlspecialchars($nom); ?>" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <input type="text" name="description" value="<?php echo htmlspecialchars($description); ?>" required>
            </div>
            <div class="form-group">
                <label>Image (nom de fichier)</label>
                <input type="file" name="image" required>
            </div>
        </div>
        <input type="hidden" name="id" value="<?php echo $idModifier; ?>">
        <div class="form-actions">
            <?php if ($idModifier): ?>
                <button type="submit" name="modifier" class="btn btn-gold">Modifier</button>
                <form method="POST"
                    action="<?php echo BASE_URL; ?>/index.php?route=admin/categories"
                    enctype="multipart/form-data">
                    <button type="submit" name="annuler" class="btn btn-outline">Annuler</button>
                </form>
            <?php else: ?>
                <button type="submit" name="ajouter" class="btn btn-gold">Ajouter</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
