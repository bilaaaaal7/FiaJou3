<?php
$pageTitle = "Gestion des commandes - " . APP_NAME;
$i18nPage = 'admin_commandes';
$pageHeading = "Commandes";
$pageHeadingI18n = 'admin_commandes.pageHeading';
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';

$statutI18n = [
    'en_attente'     => 'common.enAttente',
    'confirmee'      => 'common.confirmee',
    'en_preparation' => 'common.enPreparation',
    'prete'          => 'common.pret',
    'en_livraison'   => 'common.enLivraison',
    'livree'         => 'common.livree',
    'annulee'        => 'mes_commandes.statutAnnulee',
];

$erreursAdmin = [
    'cook_obligatoire' => "Pour passer la commande en « En préparation », veuillez sélectionner un cuisinier.",
    'livreur_obligatoire' => "Pour passer la commande en « En livraison », veuillez sélectionner un livreur.",
];
$erreursAdminI18n = [
    'cook_obligatoire' => 'admin_commandes.alertCuisinierRequis',
    'livreur_obligatoire' => 'admin_commandes.alertLivreurRequis',
];
if (isset($_GET['erreur']) && isset($erreursAdmin[$_GET['erreur']])):
?>
    <div class="alert-box alert-error" data-i18n="<?php echo htmlspecialchars($erreursAdminI18n[$_GET['erreur']]); ?>"><?php echo htmlspecialchars($erreursAdmin[$_GET['erreur']]); ?></div>
<?php endif; ?>

<div class="panel">
    <div class="filter-bar">
        <div class="form-group">
            <label data-i18n="admin_commandes.filtrerParStatut">Filtrer par statut</label>
            <select id="filterStatut" onchange="filtrerCommandes()">
                <option value="" data-i18n="menu.tous">Tous</option>
                <?php foreach (STATUTS_COMMANDE as $cle => $label): ?>
                    <option value="<?php echo $cle; ?>" data-i18n="<?php echo $statutI18n[$cle] ?? ''; ?>"><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="table-wrap">
        <table class="data-table" id="tableCommandes">
            <thead>
                <tr>
                    <th>ID</th>
                    <th data-i18n="common.client">Client</th>
                    <th data-i18n="common.email">Email</th>
                    <th data-i18n="common.dateCommande">Date commande</th>
                    <th data-i18n="common.dateLivraison">Date livraison</th>
                    <th data-i18n="common.heure">Heure</th>
                    <th data-i18n="common.zone">Zone</th>
                    <th data-i18n="common.total">Total</th>
                    <th data-i18n="common.statut">Statut</th>
                    <th data-i18n="common.commentaire">Commentaire</th>
                    <th data-i18n="admin_commandes.affectation">Affectation</th>
                    <th data-i18n="common.actions">Actions</th>
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
                    <td><span class="badge-status st-<?php echo $commande['statut']; ?>" data-i18n="<?php echo $statutI18n[$commande['statut']] ?? ''; ?>"><?php echo STATUTS_COMMANDE[$commande['statut']] ?? $commande['statut']; ?></span></td>
                    <td><?php echo htmlspecialchars(mb_strimwidth($commande['commentaire'] ?? '-', 0, 30, '...')); ?></td>
                    <td>
                        <?php if ($commande['assigned_cook_id'] || $commande['assigned_driver_id']): ?>
                            <?php
                            $affectation = [];
                            foreach ($cuisiniers as $c) {
                                if ($c['id'] == $commande['assigned_cook_id']) {
                                    $affectation[] = '<span data-i18n="common.cuisinier">Cuisinier</span> : ' . htmlspecialchars($c['prenom'] . ' ' . $c['nom']);
                                    break;
                                }
                            }
                            foreach ($livreurs as $l) {
                                if ($l['id'] == $commande['assigned_driver_id']) {
                                    $affectation[] = '<span data-i18n="common.livreur">Livreur</span> : ' . htmlspecialchars($l['prenom'] . ' ' . $l['nom']);
                                    break;
                                }
                            }
                            echo implode(' · ', $affectation);
                            ?>
                        <?php else: ?>
                            <em class="text-muted" data-i18n="common.nonAffecte">Non affecté</em>
                        <?php endif; ?>
                    </td>
                    <td class="actions-cell">
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/commandes&modifier=<?php echo $commande['id']; ?>" class="btn btn-outline btn-sm" data-i18n="common.statut">Statut</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($commandes)): ?>
                <tr><td colspan="12" class="empty-state" data-i18n="common.aucuneCommande">Aucune commande.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($idModifier != ""): ?>
<div class="panel">
    <h2><span data-i18n="admin_commandes.modifierStatutTitre">Modifier le statut de la commande</span> #<?php echo $idModifier; ?></h2>
    <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/commandes">
        <div class="form-grid">
            <div class="form-group">
                <label data-i18n="admin_commandes.nouveauStatut">Nouveau statut</label>
                <select name="statut" id="selectStatut" onchange="toggleGroupesAffectation()">
                    <?php foreach (STATUTS_COMMANDE as $cle => $label): ?>
                        <option value="<?php echo $cle; ?>" <?php if ($statut == $cle) echo "selected"; ?> data-i18n="<?php echo $statutI18n[$cle] ?? ''; ?>"><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" id="groupeCuisinier">
                <label><span data-i18n="common.cuisinier">Cuisinier</span> *</label>
                <select name="cook_id" id="selectCuisinier">
                    <option value="" data-i18n="admin_commandes.selectionnerCuisinier">-- Sélectionner un cuisinier --</option>
                    <?php foreach ($cuisiniers as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php if ($commandeModifier && $commandeModifier['assigned_cook_id'] == $c['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($c['prenom'] . ' ' . $c['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="form-hint" data-i18n="admin_commandes.cuisinierRequis">Requis pour « En préparation »</span>
            </div>
            <div class="form-group" id="groupeLivreur">
                <label><span data-i18n="common.livreur">Livreur</span> *</label>
                <select name="driver_id" id="selectLivreur">
                    <option value="" data-i18n="admin_commandes.selectionnerLivreur">-- Sélectionner un livreur --</option>
                    <?php foreach ($livreurs as $l): ?>
                        <option value="<?php echo $l['id']; ?>" <?php if ($commandeModifier && $commandeModifier['assigned_driver_id'] == $l['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($l['prenom'] . ' ' . $l['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="form-hint" data-i18n="admin_commandes.livreurRequis">Requis pour « En livraison »</span>
            </div>
        </div>
        <?php if ($commandeModifier): ?>
            <p class="panel-note">
                <span data-i18n="admin_commandes.affectationActuelle">Affectation actuelle</span> :
                <span data-i18n="common.cuisinier">Cuisinier</span> —
                <?php if ($commandeModifier['assigned_cook_id']): ?>
                    <?php
                    $nomCook = '';
                    foreach ($cuisiniers as $c) {
                        if ($c['id'] == $commandeModifier['assigned_cook_id']) { $nomCook = $c['prenom'] . ' ' . $c['nom']; break; }
                    }
                    echo htmlspecialchars($nomCook);
                    ?>
                <?php else: ?>
                    <em class="text-muted" data-i18n="common.nonAffecte">Non affecté</em>
                <?php endif; ?>
                · <span data-i18n="common.livreur">Livreur</span> —
                <?php if ($commandeModifier['assigned_driver_id']): ?>
                    <?php
                    $nomDriver = '';
                    foreach ($livreurs as $l) {
                        if ($l['id'] == $commandeModifier['assigned_driver_id']) { $nomDriver = $l['prenom'] . ' ' . $l['nom']; break; }
                    }
                    echo htmlspecialchars($nomDriver);
                    ?>
                <?php else: ?>
                    <em class="text-muted" data-i18n="common.nonAffecte">Non affecté</em>
                <?php endif; ?>
            </p>
        <?php endif; ?>
        <input type="hidden" name="id" value="<?php echo $idModifier; ?>">
        <div class="form-actions">
            <button type="submit" name="modifierStatut" class="btn btn-gold" data-i18n="common.enregistrerCourt">Enregistrer</button>
            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/commandes" class="btn btn-outline" data-i18n="common.annuler">Annuler</a>
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
            alert(window.fjI18n('admin_commandes.alertCuisinierRequis'));
        }
    }
    if (statut === 'en_livraison') {
        var driver = document.getElementById('selectLivreur');
        if (!driver.value) {
            e.preventDefault();
            driver.focus();
            alert(window.fjI18n('admin_commandes.alertLivreurRequis'));
        }
    }
});

toggleGroupesAffectation();
</script>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
