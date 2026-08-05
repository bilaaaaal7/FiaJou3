<?php
$pageTitle = "Gestion des commandes - " . APP_NAME;
$pageHeading = "Gestion des commandes";
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';

$erreursAdmin = [
    'cook_obligatoire' => "Pour passer la commande en « En préparation », veuillez sélectionner un cuisinier.",
    'livreur_obligatoire' => "Pour passer la commande en « En livraison », veuillez sélectionner un livreur.",
];
if (isset($_GET['erreur']) && isset($erreursAdmin[$_GET['erreur']])):
?>
    <div class="alert-box alert-error"><?php echo htmlspecialchars($erreursAdmin[$_GET['erreur']]); ?></div>
<?php endif; ?>

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
                    <th>Affectation</th>
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
                    <td>
                        <?php if ($commande['assigned_cook_id'] || $commande['assigned_driver_id']): ?>
                            <?php
                            $affectation = [];
                            foreach ($cuisiniers as $c) {
                                if ($c['id'] == $commande['assigned_cook_id']) {
                                    $affectation[] = 'Cuisinier : ' . $c['prenom'] . ' ' . $c['nom'];
                                    break;
                                }
                            }
                            foreach ($livreurs as $l) {
                                if ($l['id'] == $commande['assigned_driver_id']) {
                                    $affectation[] = 'Livreur : ' . $l['prenom'] . ' ' . $l['nom'];
                                    break;
                                }
                            }
                            echo htmlspecialchars(implode(' · ', $affectation));
                            ?>
                        <?php else: ?>
                            <em class="text-muted">Non affecté</em>
                        <?php endif; ?>
                    </td>
                    <td class="actions-cell">
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/commandes&modifier=<?php echo $commande['id']; ?>" class="btn btn-outline btn-sm">Statut</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($commandes)): ?>
                <tr><td colspan="12" class="empty-state">Aucune commande.</td></tr>
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
                <select name="statut" id="selectStatut" onchange="toggleGroupesAffectation()">
                    <?php foreach (STATUTS_COMMANDE as $cle => $label): ?>
                        <option value="<?php echo $cle; ?>" <?php if ($statut == $cle) echo "selected"; ?>><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" id="groupeCuisinier">
                <label>Cuisinier *</label>
                <select name="cook_id" id="selectCuisinier">
                    <option value="">-- Sélectionner un cuisinier --</option>
                    <?php foreach ($cuisiniers as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php if ($commandeModifier && $commandeModifier['assigned_cook_id'] == $c['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($c['prenom'] . ' ' . $c['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="form-hint">Requis pour « En préparation »</span>
            </div>
            <div class="form-group" id="groupeLivreur">
                <label>Livreur *</label>
                <select name="driver_id" id="selectLivreur">
                    <option value="">-- Sélectionner un livreur --</option>
                    <?php foreach ($livreurs as $l): ?>
                        <option value="<?php echo $l['id']; ?>" <?php if ($commandeModifier && $commandeModifier['assigned_driver_id'] == $l['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($l['prenom'] . ' ' . $l['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="form-hint">Requis pour « En livraison »</span>
            </div>
        </div>
        <?php if ($commandeModifier): ?>
            <p class="panel-note">
                Affectation actuelle : Cuisinier —
                <?php
                $nomCook = 'Non affecté';
                foreach ($cuisiniers as $c) {
                    if ($c['id'] == $commandeModifier['assigned_cook_id']) { $nomCook = $c['prenom'] . ' ' . $c['nom']; break; }
                }
                echo htmlspecialchars($nomCook);
                ?>
                · Livreur —
                <?php
                $nomDriver = 'Non affecté';
                foreach ($livreurs as $l) {
                    if ($l['id'] == $commandeModifier['assigned_driver_id']) { $nomDriver = $l['prenom'] . ' ' . $l['nom']; break; }
                }
                echo htmlspecialchars($nomDriver);
                ?>
            </p>
        <?php endif; ?>
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

function toggleGroupesAffectation() {
    var statut = document.getElementById('selectStatut').value;
    document.getElementById('groupeCuisinier').style.display = (statut === 'en_preparation') ? '' : 'none';
    document.getElementById('groupeLivreur').style.display = (statut === 'en_livraison') ? '' : 'none';
}

document.addEventListener('submit', function(e) {
    if (!e.target.querySelector('[name="modifierStatut"]')) return;
    var statut = document.getElementById('selectStatut').value;
    if (statut === 'en_preparation') {
        var cook = document.getElementById('selectCuisinier');
        if (!cook.value) {
            e.preventDefault();
            cook.focus();
            alert('Veuillez sélectionner un cuisinier.');
        }
    }
    if (statut === 'en_livraison') {
        var driver = document.getElementById('selectLivreur');
        if (!driver.value) {
            e.preventDefault();
            driver.focus();
            alert('Veuillez sélectionner un livreur.');
        }
    }
});

toggleGroupesAffectation();
</script>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
