<?php
$pageTitle = "Gestion des commandes - " . APP_NAME;
$pageHeading = "Gestion des commandes";
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<div class="panel">
    <div class="filter-bar">
        <div class="form-group">
            <label>Filtrer par statut</label>
            <select id="filterStatut" onchange="filtrerCommandes()">
                <option value="">Tous</option>
                <?php foreach (STATUTS_COMMANDE as $cle => $label): ?>
                    <option value="<?php echo $cle; ?>"><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="table-wrap">
        <table class="data-table" id="tableCommandes">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Email</th>
                    <th>Date commande</th>
                    <th>Date livraison</th>
                    <th>Heure</th>
                    <th>Zone</th>
                    <th>Total</th>
                    <th>Statut</th>
                    <th>Commentaire</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($commandes as $commande): ?>
                <tr data-statut="<?php echo $commande['statut']; ?>">
                    <td><?php echo $commande['id']; ?></td>
                    <td><?php echo htmlspecialchars($commande['prenom'] . ' ' . $commande['nom']); ?></td>
                    <td><?php echo htmlspecialchars($commande['email']); ?></td>
                    <td><?php echo $commande['date_commande']; ?></td>
                    <td><?php echo $commande['date_livraison']; ?></td>
                    <td><?php echo $commande['heure_livraison']; ?></td>
                    <td><?php echo htmlspecialchars($commande['zone_nom'] ?? '-'); ?></td>
                    <td><?php echo number_format($commande['total'], 2); ?> DH</td>
                    <td><span class="badge-status st-<?php echo $commande['statut']; ?>"><?php echo STATUTS_COMMANDE[$commande['statut']] ?? $commande['statut']; ?></span></td>
                    <td><?php echo htmlspecialchars(mb_strimwidth($commande['commentaire'] ?? '-', 0, 30, '...')); ?></td>
                    <td class="actions-cell">
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/commandes&modifier=<?php echo $commande['id']; ?>" class="btn btn-outline btn-sm">Statut</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($commandes)): ?>
                <tr><td colspan="11" class="empty-state">Aucune commande.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($idModifier != ""): ?>
<div class="panel">
    <h2>Modifier le statut de la commande #<?php echo $idModifier; ?></h2>
    <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/commandes">
        <div class="form-grid">
            <div class="form-group">
                <label>Nouveau statut</label>
                <select name="statut">
                    <?php foreach (STATUTS_COMMANDE as $cle => $label): ?>
                        <option value="<?php echo $cle; ?>" <?php if ($statut == $cle) echo "selected"; ?>><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <input type="hidden" name="id" value="<?php echo $idModifier; ?>">
        <div class="form-actions">
            <button type="submit" name="modifierStatut" class="btn btn-gold">Enregistrer</button>
            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/commandes" class="btn btn-outline">Annuler</a>
        </div>
    </form>
</div>
<?php endif; ?>

<script>
function filtrerCommandes() {
    var filter = document.getElementById('filterStatut').value;
    var rows = document.querySelectorAll('#tableCommandes tbody tr');
    rows.forEach(function(row) {
        if (!filter || row.getAttribute('data-statut') === filter) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
