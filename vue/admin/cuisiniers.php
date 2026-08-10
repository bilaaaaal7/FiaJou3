<?php
$pageTitle = "Gestion des cuisiniers - " . APP_NAME;
$pageHeading = "Gestion des cuisiniers";
$extraCss = ['admin.css'];
$extraJs = ['modal-form.js'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
$formOuvert = !empty($idModifier) || !empty($erreur);
$formMode = !empty($idModifier) ? 'edit' : 'add';
?>

<div id="adminPageContent">

<?php if (!empty($erreur)): ?>
    <div class="alert-box alert-error"><?php echo htmlspecialchars($erreur); ?></div>
<?php endif; ?>

<?php if (isset($_GET['succes'])): ?>
    <div class="alert-box alert-success">Cuisinier ajouté avec succès.</div>
<?php endif; ?>

<?php if (isset($_GET['supprime'])): ?>
    <div class="alert-box alert-success">Cuisinier supprimé avec succès.</div>
<?php endif; ?>

<div class="panel panel-list<?php echo $formOuvert ? ' is-hidden' : ''; ?>" id="panelListeCuisiniers">
    <div class="panel-head-actions">
        <h2>Liste des cuisiniers</h2>
        <button type="button" class="btn btn-gold" data-modal-open="modalFormCuisinier" data-mode="add">Ajouter un cuisinier</button>
    </div>
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
            <?php foreach ($cuisiniers as $c): ?>
                <tr>
                    <td><?php echo $c['id']; ?></td>
                    <td><?php echo htmlspecialchars($c['prenom']); ?></td>
                    <td><?php echo htmlspecialchars($c['nom']); ?></td>
                    <td><?php echo htmlspecialchars($c['email']); ?></td>
                    <td>
                        <?php if ($c['actif']): ?>
                            <span class="badge-yes">Actif</span>
                        <?php else: ?>
                            <span class="badge-no">Inactif</span>
                        <?php endif; ?>
                    </td>
                    <td class="actions-cell">
                        <button type="button" class="btn btn-outline btn-sm" data-modal-open="modalFormCuisinier" data-mode="edit"
                            data-fields='<?php echo htmlspecialchars(json_encode([
                                'id' => (int) $c['id'],
                                'prenom' => $c['prenom'],
                                'nom' => $c['nom'],
                                'email' => $c['email'],
                                'telephone' => $c['telephone'] ?? '',
                            ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES); ?>'>Modifier</button>
                        <?php if ($c['actif']): ?>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/cuisiniers&desactiver=<?php echo $c['id']; ?>" class="btn btn-danger btn-sm js-fj-ajax" data-confirm="Désactiver ce cuisinier ?">Désactiver</a>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/cuisiniers&activer=<?php echo $c['id']; ?>" class="btn btn-gold btn-sm js-fj-ajax">Activer</a>
                        <?php endif; ?>
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/cuisiniers&supprimer=<?php echo $c['id']; ?>" class="btn btn-danger btn-sm js-fj-ajax" data-confirm="Voulez-vous vraiment supprimer ce cuisinier ?">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($cuisiniers)): ?>
                <tr><td colspan="6" class="empty-state">Aucun cuisinier enregistré.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="modalFormCuisinier" hidden>
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalFormCuisinierTitle">
        <div class="modal-head">
            <h3 id="modalFormCuisinierTitle" data-title-add="Ajouter un cuisinier" data-title-edit="Modifier le cuisinier"><?php echo $formMode === 'edit' ? 'Modifier le cuisinier' : 'Ajouter un cuisinier'; ?></h3>
            <button type="button" class="modal-close" data-modal-close aria-label="Fermer">&times;</button>
        </div>
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/cuisiniers">
            <div class="form-stack">
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
                <div class="form-group" data-only-add>
                    <label>Mot de passe</label>
                    <input type="password" name="password" minlength="6" required>
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
            window.ouvrirModalForm('modalFormCuisinier', '<?php echo $formMode; ?>', null, <?php echo json_encode($erreur ?? '', JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP); ?>);
        }
    });
</script>
<?php endif; ?>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
