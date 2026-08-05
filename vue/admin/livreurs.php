<?php
$pageTitle = "Gestion des livreurs - " . APP_NAME;
$pageHeading = "Gestion des livreurs";
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<?php if (!empty($erreur)): ?>
    <div class="alert-box alert-error"><?php echo htmlspecialchars($erreur); ?></div>
<?php endif; ?>

<div class="panel">
    <h2>Liste des livreurs</h2>
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
            <?php foreach ($livreurs as $l): ?>
                <tr>
                    <td><?php echo $l['id']; ?></td>
                    <td><?php echo htmlspecialchars($l['prenom']); ?></td>
                    <td><?php echo htmlspecialchars($l['nom']); ?></td>
                    <td><?php echo htmlspecialchars($l['email']); ?></td>
                    <td>
                        <?php if ($l['actif']): ?>
                            <span class="badge-yes">Actif</span>
                        <?php else: ?>
                            <span class="badge-no">Inactif</span>
                        <?php endif; ?>
                    </td>
                    <td class="actions-cell">
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/livreurs&modifier=<?php echo $l['id']; ?>" class="btn btn-outline btn-sm">Modifier</a>
                        <?php if ($l['actif']): ?>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/livreurs&desactiver=<?php echo $l['id']; ?>" class="btn btn-danger btn-sm" data-confirm="Désactiver ce livreur ?">Désactiver</a>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/livreurs&activer=<?php echo $l['id']; ?>" class="btn btn-gold btn-sm">Activer</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($livreurs)): ?>
                <tr><td colspan="6" class="empty-state">Aucun livreur enregistré.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="panel">
    <h2><?php echo $idModifier ? 'Modifier le livreur' : 'Ajouter un livreur'; ?></h2>
    <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/livreurs">
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
                <a href="<?php echo BASE_URL; ?>/index.php?route=admin/livreurs" class="btn btn-outline">Annuler</a>
            <?php else: ?>
                <button type="submit" name="ajouter" class="btn btn-gold">Ajouter</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
