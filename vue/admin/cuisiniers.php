<?php
$pageTitle = "Gestion des cuisiniers - " . APP_NAME;
$pageHeading = "Gestion des cuisiniers";
$extraCss = ['admin.css'];
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
        <button type="button" id="btnToggleFormCuisinier"
                class="btn btn-gold"
                aria-expanded="<?php echo $formOuvert ? 'true' : 'false'; ?>">
            Ajouter un cuisinier
        </button>
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
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/cuisiniers&modifier=<?php echo $c['id']; ?>" class="btn btn-outline btn-sm js-modifier-cuisinier" data-id="<?php echo $c['id']; ?>">Modifier</a>
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
        </div>
    </div>
</div>

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
</script>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
