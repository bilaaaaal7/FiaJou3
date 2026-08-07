<?php
$pageTitle = "Zones de livraison - " . APP_NAME;
$pageHeading = "Zones de livraison";
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
$formOuvert = !empty($idModifier) || !empty($erreur);

$fjItems = [];
foreach ($zones as $fjItem) {
    $fjItems[(string) $fjItem['id']] = [
        'nom' => $fjItem['nom'],
        'prix_livraison' => $fjItem['prix_livraison'],
    ];
}
$fjItemsJson = json_encode($fjItems, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
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
        <button type="button" id="btnToggleFormZone"
                class="btn btn-gold"
                aria-expanded="<?php echo $formOuvert ? 'true' : 'false'; ?>">
            Ajouter une zone
        </button>
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
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/zones&modifier=<?php echo $zone['id']; ?>" class="btn btn-outline btn-sm js-modifier-zone" data-id="<?php echo $zone['id']; ?>">Modifier</a>
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/zones&supprimer=<?php echo $zone['id']; ?>" class="btn btn-danger btn-sm js-fj-ajax" data-confirm="Supprimer cette zone ?">Supprimer</a>
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
        </div>
    </div>
</div>

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
</script>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
