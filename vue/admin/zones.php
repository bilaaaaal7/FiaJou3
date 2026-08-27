<?php
$pageTitle = "Zones de livraison - " . APP_NAME;
$i18nPage = 'admin_zones';
$pageHeading = "Zones de livraison";
$pageHeadingI18n = 'admin_zones.pageHeading';
$extraCss = ['admin.css'];
$extraJs = ['modal-form.js'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
$formOuvert = !empty($idModifier) || !empty($erreur);
$formMode = !empty($idModifier) ? 'edit' : 'add';
?>

<div id="adminPageContent">

<?php if (!empty($erreur)): ?>
    <div class="alert-box alert-error"><?php echo render_i18n($erreur); ?></div>
<?php endif; ?>

<?php if (isset($_GET['succes'])): ?>
    <div class="alert-box alert-success" data-i18n="admin_zones.succesAjout">Zone ajoutée avec succès.</div>
<?php endif; ?>

<div class="panel panel-list<?php echo $formOuvert ? ' is-hidden' : ''; ?>" id="panelListeZones">
    <div class="panel-head-actions">
        <h2 data-i18n="admin_zones.listeZones">Liste des zones</h2>
        <button type="button" class="btn btn-gold" data-modal-open="modalFormZone" data-mode="add"><span data-i18n="admin_zones.ajouterZone">Ajouter une zone</span></button>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th data-i18n="common.nom">Nom</th>
                    <th data-i18n="common.prixLivraison">Prix livraison</th>
                    <th data-i18n="common.actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($zones as $zone): ?>
                <tr>
                    <td><?php echo $zone['id']; ?></td>
                    <td><?php echo htmlspecialchars(localiser($zone, 'nom')); ?></td>
                    <td><?php echo number_format($zone['prix_livraison'], 2); ?> DH</td>
                    <td class="actions-cell">
                        <button type="button" class="btn btn-outline btn-sm" data-modal-open="modalFormZone" data-mode="edit"
                            data-fields='<?php echo htmlspecialchars(json_encode([
                                'id' => (int) $zone['id'],
                                'nom' => $zone['nom'],
                                'nom_en' => $zone['nom_en'] ?? '',
                                'nom_ar' => $zone['nom_ar'] ?? '',
                                'prix_livraison' => $zone['prix_livraison'],
                                'lat' => $zone['lat'] ?? '',
                                'lng' => $zone['lng'] ?? '',
                                'rayon_km' => $zone['rayon_km'] ?? '',
                            ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES); ?>'><span data-i18n="common.modifier">Modifier</span></button>
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/zones&supprimer=<?php echo $zone['id']; ?>" class="btn btn-danger btn-sm" data-confirm="Supprimer cette zone ?" data-confirm-i18n="admin_zones.confirmSupprimer"><span data-i18n="common.supprimer">Supprimer</span></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($zones)): ?>
                <tr><td colspan="4" class="empty-state" data-i18n="admin_zones.aucuneZone">Aucune zone configurée.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="modalFormZone" hidden>
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalFormZoneTitle">
        <div class="modal-head">
            <h3 id="modalFormZoneTitle" data-title-add="admin_zones.titreAjouter" data-title-edit="admin_zones.titreModifier"><?php echo $formMode === 'edit' ? 'Modifier la zone' : 'Ajouter une zone'; ?></h3>
            <button type="button" class="modal-close" data-modal-close aria-label="Fermer" data-i18n-aria="common.fermer">&times;</button>
        </div>
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/zones">
            <div class="form-stack">
                <div class="form-group">
                    <label data-i18n="common.nom">Nom</label>
                    <input type="text" name="nom" value="<?php echo htmlspecialchars($nom); ?>" required>
                </div>
                <div class="form-group">
                    <label data-i18n="admin_zones.nomEn">Nom (EN)</label>
                    <input type="text" name="nom_en" value="<?php echo htmlspecialchars($nomEn); ?>" placeholder="English name">
                </div>
                <div class="form-group">
                    <label data-i18n="admin_zones.nomAr">Nom (AR)</label>
                    <input type="text" name="nom_ar" value="<?php echo htmlspecialchars($nomAr); ?>" placeholder="الاسم بالعربية" dir="rtl">
                </div>
                <div class="form-group">
                    <label data-i18n="admin_zones.prixLivraison">Prix de livraison (DH)</label>
                    <input type="number" step="0.01" min="0" name="prix_livraison" value="<?php echo htmlspecialchars($prix); ?>" required>
                </div>
                <div class="form-group">
                    <label data-i18n="admin_zones.latitude">Latitude (centre de la zone)</label>
                    <input type="number" step="any" name="lat" value="<?php echo htmlspecialchars($lat); ?>" placeholder="Ex : 31.6295000">
                </div>
                <div class="form-group">
                    <label data-i18n="admin_zones.longitude">Longitude</label>
                    <input type="number" step="any" name="lng" value="<?php echo htmlspecialchars($lng); ?>" placeholder="Ex : -7.9811000">
                </div>
                <div class="form-group">
                    <label data-i18n="admin_zones.rayon">Rayon (km)</label>
                    <input type="number" step="0.1" min="0" name="rayon_km" value="<?php echo htmlspecialchars($rayon); ?>" placeholder="Ex : 2.5">
                </div>
            </div>
            <input type="hidden" name="id" value="<?php echo $idModifier; ?>">
            <p class="modal-error" hidden><?php echo render_i18n($erreur ?? ''); ?></p>
            <div class="form-actions">
                <button type="submit" name="<?php echo $formMode === 'edit' ? 'modifier' : 'ajouter'; ?>" class="btn btn-gold"
                    data-name-add="ajouter" data-name-edit="modifier"
                    data-label-add="common.ajouter" data-label-edit="common.modifier"><?php echo $formMode === 'edit' ? 'Modifier' : 'Ajouter'; ?></button>
                <button type="button" class="btn btn-outline" data-modal-close><span data-i18n="common.annuler">Annuler</span></button>
            </div>
        </form>
    </div>
</div>

<?php if ($formOuvert): ?>
<script>
    window.addEventListener('DOMContentLoaded', function () {
        if (window.ouvrirModalForm) {
            window.ouvrirModalForm('modalFormZone', '<?php echo $formMode; ?>', null, <?php echo json_encode(cle_i18n($erreur ?? ''), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP); ?>);
        }
    });
</script>
<?php endif; ?>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
