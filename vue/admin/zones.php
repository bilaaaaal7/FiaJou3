<?php
$pageTitle = "Zones de livraison - " . APP_NAME;
$pageHeading = "Zones de livraison";
$extraCss = ['admin.css'];
$extraJs = ['modal-form.js'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
$formOuvert = !empty($idModifier) || !empty($erreur);
<<<<<<< HEAD

$fjItems = [];
foreach ($zones as $fjItem) {
    $fjItems[(string) $fjItem['id']] = [
        'nom' => $fjItem['nom'],
        'prix_livraison' => $fjItem['prix_livraison'],
    ];
}
$fjItemsJson = json_encode($fjItems, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
=======
$formMode = !empty($idModifier) ? 'edit' : 'add';
>>>>>>> 82f4cdb1fe515253f5c0e8e2af9345e976778957
?>

<div id="adminPageContent">

<?php if (!empty($erreur)): ?>
    <div class="alert-box alert-error"><?php echo htmlspecialchars($erreur); ?></div>
<?php endif; ?>

<?php if (isset($_GET['succes'])): ?>
    <div class="alert-box alert-success">Zone ajoutée avec succès.</div>
<?php endif; ?>

<div class="panel panel-list<?php echo $formOuvert ? ' is-hidden' : ''; ?>" id="panelListeZones">
    <div class="panel-head-actions">
        <h2>Liste des zones</h2>
<<<<<<< HEAD
        <button type="button" id="btnToggleFormZone"
                class="btn btn-gold"
                aria-expanded="<?php echo $formOuvert ? 'true' : 'false'; ?>">
            Ajouter une zone
        </button>
=======
        <button type="button" class="btn btn-gold" data-modal-open="modalFormZone" data-mode="add">Ajouter une zone</button>
>>>>>>> 82f4cdb1fe515253f5c0e8e2af9345e976778957
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prix livraison</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($zones as $zone): ?>
                <tr>
                    <td><?php echo $zone['id']; ?></td>
                    <td><?php echo htmlspecialchars($zone['nom']); ?></td>
                    <td><?php echo number_format($zone['prix_livraison'], 2); ?> DH</td>
                    <td class="actions-cell">
<<<<<<< HEAD
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/zones&modifier=<?php echo $zone['id']; ?>" class="btn btn-outline btn-sm js-modifier-zone" data-id="<?php echo $zone['id']; ?>">Modifier</a>
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/zones&supprimer=<?php echo $zone['id']; ?>" class="btn btn-danger btn-sm js-fj-ajax" data-confirm="Supprimer cette zone ?">Supprimer</a>
=======
                        <button type="button" class="btn btn-outline btn-sm" data-modal-open="modalFormZone" data-mode="edit"
                            data-fields='<?php echo htmlspecialchars(json_encode([
                                'id' => (int) $zone['id'],
                                'nom' => $zone['nom'],
                                'prix_livraison' => $zone['prix_livraison'],
                            ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES); ?>'>Modifier</button>
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/zones&supprimer=<?php echo $zone['id']; ?>" class="btn btn-danger btn-sm" data-confirm="Supprimer cette zone ?">Supprimer</a>
>>>>>>> 82f4cdb1fe515253f5c0e8e2af9345e976778957
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($zones)): ?>
                <tr><td colspan="4" class="empty-state">Aucune zone configurée.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<<<<<<< HEAD
<div class="form-collapse <?php echo $formOuvert ? 'open' : ''; ?>" id="collapseFormZone">
    <div class="form-collapse__inner">
        <div class="panel">
            <h2>
                <span data-title-add <?php echo $idModifier ? 'hidden' : ''; ?>>Ajouter une zone</span>
                <span data-title-edit <?php echo $idModifier ? '' : 'hidden'; ?>>Modifier la zone</span>
            </h2>
            <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/zones" id="formZone">
                <div class="form-stack">
                    <div class="form-group">
                        <label>Nom</label>
                        <input type="text" name="nom" value="<?php echo htmlspecialchars($nom); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Prix de livraison (DH)</label>
                        <input type="number" step="0.01" min="0" name="prix_livraison" value="<?php echo htmlspecialchars($prix); ?>" required>
                    </div>
                </div>
                <input type="hidden" name="id" value="<?php echo $idModifier; ?>">
                <div class="form-actions form-actions-end">
                    <button type="submit" name="<?php echo $idModifier ? 'modifier' : 'ajouter'; ?>" data-btn-submit class="btn btn-gold"><?php echo $idModifier ? 'Modifier' : 'Ajouter'; ?></button>
                    <a href="<?php echo BASE_URL; ?>/index.php?route=admin/zones" class="btn btn-outline" data-btn-annuler>Annuler</a>
                </div>
            </form>
=======
<div class="modal-overlay" id="modalFormZone" hidden>
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalFormZoneTitle">
        <div class="modal-head">
            <h3 id="modalFormZoneTitle" data-title-add="Ajouter une zone" data-title-edit="Modifier la zone"><?php echo $formMode === 'edit' ? 'Modifier la zone' : 'Ajouter une zone'; ?></h3>
            <button type="button" class="modal-close" data-modal-close aria-label="Fermer">&times;</button>
>>>>>>> 82f4cdb1fe515253f5c0e8e2af9345e976778957
        </div>
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/zones">
            <div class="form-stack">
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" value="<?php echo htmlspecialchars($nom); ?>" required>
                </div>
                <div class="form-group">
                    <label>Prix de livraison (DH)</label>
                    <input type="number" step="0.01" min="0" name="prix_livraison" value="<?php echo htmlspecialchars($prix); ?>" required>
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
    listId: 'panelListeZones',
    toggleId: 'btnToggleFormZone',
    collapseId: 'collapseFormZone',
    formId: 'formZone',
    editSelector: '.js-modifier-zone',
    ajaxSelector: '.js-fj-ajax',
    labels: {
        addSubmit: 'Ajouter',
        editSubmit: 'Modifier'
    },
    initialMode: <?php echo $idModifier ? "'edit'" : "'add'"; ?>,
    items: <?php echo $fjItemsJson; ?>,
    populate: function (form, item) {
        form.querySelector('[name="nom"]').value = item.nom;
        form.querySelector('[name="prix_livraison"]').value = item.prix_livraison;
    }
});
=======
<?php if ($formOuvert): ?>
<script>
    window.addEventListener('DOMContentLoaded', function () {
        if (window.ouvrirModalForm) {
            window.ouvrirModalForm('modalFormZone', '<?php echo $formMode; ?>', null, <?php echo json_encode($erreur ?? '', JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP); ?>);
        }
    });
>>>>>>> 82f4cdb1fe515253f5c0e8e2af9345e976778957
</script>
<?php endif; ?>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
