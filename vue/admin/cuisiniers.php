<?php
$pageTitle = "Gestion des cuisiniers - " . APP_NAME;
$pageHeading = "Gestion des cuisiniers";
$extraCss = ['admin.css'];
<<<<<<< HEAD
$fjFormPrenom = $prenom ?? '';
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
$formOuvert = !empty($idModifier) || !empty($erreur);

$fjItems = [];
foreach ($cuisiniers as $fjItem) {
    $fjItems[(string) $fjItem['id']] = [
        'prenom' => $fjItem['prenom'],
        'nom' => $fjItem['nom'],
        'email' => $fjItem['email'],
        'telephone' => $fjItem['telephone'],
    ];
}
$fjItemsJson = json_encode($fjItems, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
=======
$extraJs = ['modal-form.js'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
$formOuvert = !empty($idModifier) || !empty($erreur);
$formMode = !empty($idModifier) ? 'edit' : 'add';
>>>>>>> 82f4cdb1fe515253f5c0e8e2af9345e976778957
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
<<<<<<< HEAD
        <button type="button" id="btnToggleFormCuisinier"
                class="btn btn-gold"
                aria-expanded="<?php echo $formOuvert ? 'true' : 'false'; ?>">
            Ajouter un cuisinier
        </button>
=======
        <button type="button" class="btn btn-gold" data-modal-open="modalFormCuisinier" data-mode="add">Ajouter un cuisinier</button>
>>>>>>> 82f4cdb1fe515253f5c0e8e2af9345e976778957
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
<<<<<<< HEAD
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/cuisiniers&modifier=<?php echo $c['id']; ?>" class="btn btn-outline btn-sm js-modifier-cuisinier" data-id="<?php echo $c['id']; ?>">Modifier</a>
=======
                        <button type="button" class="btn btn-outline btn-sm" data-modal-open="modalFormCuisinier" data-mode="edit"
                            data-fields='<?php echo htmlspecialchars(json_encode([
                                'id' => (int) $c['id'],
                                'prenom' => $c['prenom'],
                                'nom' => $c['nom'],
                                'email' => $c['email'],
                                'telephone' => $c['telephone'] ?? '',
                            ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES); ?>'>Modifier</button>
>>>>>>> 82f4cdb1fe515253f5c0e8e2af9345e976778957
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

<<<<<<< HEAD
<div class="form-collapse <?php echo $formOuvert ? 'open' : ''; ?>" id="collapseFormCuisinier">
    <div class="form-collapse__inner">
        <div class="panel">
            <h2>
                <span data-title-add <?php echo $idModifier ? 'hidden' : ''; ?>>Ajouter un cuisinier</span>
                <span data-title-edit <?php echo $idModifier ? '' : 'hidden'; ?>>Modifier le cuisinier</span>
            </h2>
            <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/cuisiniers" id="formCuisinier">
                <div class="form-stack">
                    <div class="form-group">
                        <label>Prénom</label>
                        <input type="text" name="prenom" value="<?php echo htmlspecialchars($fjFormPrenom); ?>" required>
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
                    <div class="form-group" data-field-password <?php echo $idModifier ? 'hidden' : ''; ?>>
                        <label>Mot de passe</label>
                        <input type="password" name="password" minlength="6" <?php echo $idModifier ? '' : 'required'; ?>>
                    </div>
                </div>
                <input type="hidden" name="id" value="<?php echo $idModifier; ?>">
                <div class="form-actions form-actions-end">
                    <button type="submit" name="<?php echo $idModifier ? 'modifier' : 'ajouter'; ?>" data-btn-submit class="btn btn-gold"><?php echo $idModifier ? 'Modifier' : 'Ajouter'; ?></button>
                    <a href="<?php echo BASE_URL; ?>/index.php?route=admin/cuisiniers" class="btn btn-outline" data-btn-annuler>Annuler</a>
                </div>
            </form>
=======
<div class="modal-overlay" id="modalFormCuisinier" hidden>
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalFormCuisinierTitle">
        <div class="modal-head">
            <h3 id="modalFormCuisinierTitle" data-title-add="Ajouter un cuisinier" data-title-edit="Modifier le cuisinier"><?php echo $formMode === 'edit' ? 'Modifier le cuisinier' : 'Ajouter un cuisinier'; ?></h3>
            <button type="button" class="modal-close" data-modal-close aria-label="Fermer">&times;</button>
>>>>>>> 82f4cdb1fe515253f5c0e8e2af9345e976778957
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

<<<<<<< HEAD
<script src="<?php echo BASE_URL; ?>/assets/js/admin_form.js"></script>
<script>
fjInitFormPanel({
    contentId: 'adminPageContent',
    listId: 'panelListeCuisiniers',
    toggleId: 'btnToggleFormCuisinier',
    collapseId: 'collapseFormCuisinier',
    formId: 'formCuisinier',
    editSelector: '.js-modifier-cuisinier',
    ajaxSelector: '.js-fj-ajax',
    labels: {
        addSubmit: 'Ajouter',
        editSubmit: 'Modifier'
    },
    initialMode: <?php echo $idModifier ? "'edit'" : "'add'"; ?>,
    items: <?php echo $fjItemsJson; ?>,
    populate: function (form, item) {
        form.querySelector('[name="prenom"]').value = item.prenom;
        form.querySelector('[name="nom"]').value = item.nom;
        form.querySelector('[name="email"]').value = item.email;
        form.querySelector('[name="telephone"]').value = item.telephone;
    }
});
=======
<?php if ($formOuvert): ?>
<script>
    window.addEventListener('DOMContentLoaded', function () {
        if (window.ouvrirModalForm) {
            window.ouvrirModalForm('modalFormCuisinier', '<?php echo $formMode; ?>', null, <?php echo json_encode($erreur ?? '', JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP); ?>);
        }
    });
>>>>>>> 82f4cdb1fe515253f5c0e8e2af9345e976778957
</script>
<?php endif; ?>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
