<?php
$pageTitle = "Assignation des commandes - " . APP_NAME;
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<h1>Assignation des commandes</h1>

<?php if (!empty($message)): ?>
    <div class="alert-box alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<div class="panel">
    <h2>Commandes en attente d'assignation</h2>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Date livraison</th>
                    <th>Heure</th>
                    <th>Total</th>
                    <th>Statut</th>
                    <th>Cuisinier</th>
                    <th>Livreur</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($commandes as $cmd): ?>
                <?php if (in_array($cmd['statut'], ['en_attente', 'confirmee', 'en_preparation'])): ?>
                <tr>
                    <td><?php echo $cmd['id']; ?></td>
                    <td><?php echo htmlspecialchars($cmd['prenom'] . ' ' . $cmd['nom']); ?></td>
                    <td><?php echo $cmd['date_livraison']; ?></td>
                    <td><?php echo $cmd['heure_livraison']; ?></td>
                    <td><?php echo number_format($cmd['total'], 2); ?> DH</td>
                    <td><span class="badge-status st-<?php echo $cmd['statut']; ?>"><?php echo STATUTS_COMMANDE[$cmd['statut']] ?? $cmd['statut']; ?></span></td>
                    <td>
                        <?php if ($cmd['assigned_cook_id']): ?>
                            <?php
                            $cookFound = false;
                            foreach ($cuisiniers as $c) {
                                if ($c['id'] == $cmd['assigned_cook_id']) {
                                    echo htmlspecialchars($c['prenom'] . ' ' . $c['nom']);
                                    $cookFound = true;
                                    break;
                                }
                            }
                            if (!$cookFound) echo 'ID: ' . $cmd['assigned_cook_id'];
                            ?>
                        <?php else: ?>
                            <em style="color:var(--text-muted)">Non assigné</em>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($cmd['assigned_driver_id']): ?>
                            <?php
                            $driverFound = false;
                            foreach ($livreurs as $l) {
                                if ($l['id'] == $cmd['assigned_driver_id']) {
                                    echo htmlspecialchars($l['prenom'] . ' ' . $l['nom']);
                                    $driverFound = true;
                                    break;
                                }
                            }
                            if (!$driverFound) echo 'ID: ' . $cmd['assigned_driver_id'];
                            ?>
                        <?php else: ?>
                            <em style="color:var(--text-muted)">Non assigné</em>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/assignation" style="display:inline-flex; gap:4px; align-items:center; flex-wrap:wrap;">
                            <input type="hidden" name="order_id" value="<?php echo $cmd['id']; ?>">
                            <select name="cook_id" class="form-group">
                                <option value="">-- Cuisinier --</option>
                                <?php foreach ($cuisiniers as $c): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php if ($cmd['assigned_cook_id'] == $c['id']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($c['prenom'] . ' ' . $c['nom']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <select name="driver_id" class="form-group">
                                <option value="">-- Livreur --</option>
                                <?php foreach ($livreurs as $l): ?>
                                    <option value="<?php echo $l['id']; ?>" <?php if ($cmd['assigned_driver_id'] == $l['id']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($l['prenom'] . ' ' . $l['nom']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="assigner" class="btn btn-gold btn-sm">Assigner</button>
                        </form>
                    </td>
                </tr>
                <?php endif; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
