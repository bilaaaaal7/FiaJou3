<?php
$pageTitle = "Gestion des clients - " . APP_NAME;
$pageHeading = "Gestion des clients";
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<?php if (!empty($error)): ?>
    <div class="alert-box alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="panel">
    <h2>Liste des clients</h2>
    <div class="table-wrap">
        <table class="data-table" id="tableUsers">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['prenom']); ?></td>
                    <td><?php echo htmlspecialchars($user['nom']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><span class="badge-role"><?php echo $user['role']; ?></span></td>
                    <td>
                        <?php if ($user['actif']): ?>
                            <span class="badge-yes">Actif</span>
                        <?php else: ?>
                            <span class="badge-no">Inactif</span>
                        <?php endif; ?>
                    </td>
                    <td class="actions-cell">
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/utilisateurs&modifier=<?php echo $user['id']; ?>" class="btn btn-outline btn-sm">Modifier</a>
                        <?php if ($user['actif']): ?>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/utilisateurs&desactiver=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" data-confirm="Désactiver ce client ?">Désactiver</a>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/utilisateurs&activer=<?php echo $user['id']; ?>" class="btn btn-gold btn-sm">Activer</a>
                        <?php endif; ?>
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/utilisateurs&supprimer=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" data-confirm="Voulez-vous vraiment supprimer cet utilisateur ?">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($users)): ?>
                <tr><td colspan="7" class="empty-state">Aucun client enregistré.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($idModifier != ""): ?>
<div class="panel">
    <h2>Modifier l'utilisateur</h2>
    <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/utilisateurs">
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
            <div class="form-group">
                <label>Adresse</label>
                <input type="text" name="adresse" value="<?php echo htmlspecialchars($adresse); ?>">
            </div>
            <div class="form-group">
                <label>Ville</label>
                <input type="text" name="ville" value="<?php echo htmlspecialchars($ville); ?>">
            </div>
            <div class="form-group">
                <label>Rôle</label>
                <select name="role">
                    <option value="admin" <?php if ($role == "admin") echo "selected"; ?>>Admin</option>
                    <option value="client" <?php if ($role == "client") echo "selected"; ?>>Client</option>
                    <option value="cook" <?php if ($role == "cook") echo "selected"; ?>>Cuisinier</option>
                    <option value="driver" <?php if ($role == "driver") echo "selected"; ?>>Livreur</option>
                </select>
            </div>
        </div>
        <input type="hidden" name="id" value="<?php echo $idModifier; ?>">
        <div class="form-actions">
            <button type="submit" name="modifier" class="btn btn-gold">Modifier</button>
            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/utilisateurs" class="btn btn-outline">Annuler</a>
        </div>
    </form>
</div>
<?php endif; ?>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
