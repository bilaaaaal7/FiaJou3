<?php
$pageTitle = "Gestion des livreurs - " . APP_NAME;
$i18nPage = 'admin_livreurs';
$pageHeading = "Livreurs";
$pageHeadingI18n = 'admin_livreurs.pageHeading';
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
    <div class="alert-box alert-success" data-i18n="admin_livreurs.succesAjout">Livreur ajouté avec succès.</div>
<?php endif; ?>

<?php if (isset($_GET['supprime'])): ?>
    <div class="alert-box alert-success" data-i18n="admin_livreurs.succesSupprime">Livreur supprimé avec succès.</div>
<?php endif; ?>

<div class="panel panel-list<?php echo $formOuvert ? ' is-hidden' : ''; ?>" id="panelListeLivreurs">
    <div class="panel-head-actions">
        <h2 data-i18n="admin_livreurs.listeLivreurs">Liste des livreurs</h2>
        <button type="button" class="btn btn-gold" data-modal-open="modalFormLivreur" data-mode="add"><span data-i18n="admin_livreurs.ajouterLivreur">Ajouter un livreur</span></button>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th data-i18n="common.prenom">Prénom</th>
                    <th data-i18n="common.nom">Nom</th>
                    <th data-i18n="common.email">Email</th>
                    <th data-i18n="common.statut">Statut</th>
                    <th data-i18n="common.actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($livreurs as $l): ?>
                <tr>
                    <td><?php echo $l['id']; ?></td>
                    <td><?php echo htmlspecialchars($l['prenom']); ?></td>
                    <td><?php echo htmlspecialchars($l['nom']); ?></td>
                    <td><?php echo htmlspecialchars($l['email']); ?></td>
                    <td>
                        <?php if ($l['actif']): ?>
                            <span class="badge-yes" data-i18n="common.actif">Actif</span>
                        <?php else: ?>
                            <span class="badge-no" data-i18n="common.inactif">Inactif</span>
                        <?php endif; ?>
                    </td>
                    <td class="actions-cell">
                        <button type="button" class="btn btn-outline btn-sm" data-modal-open="modalFormLivreur" data-mode="edit"
                            data-fields='<?php echo htmlspecialchars(json_encode([
                                'id' => (int) $l['id'],
                                'prenom' => $l['prenom'],
                                'nom' => $l['nom'],
                                'email' => $l['email'],
                                'telephone' => $l['telephone'] ?? '',
                            ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES); ?>'><span data-i18n="common.modifier">Modifier</span></button>
                        <?php if ($l['actif']): ?>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/livreurs&desactiver=<?php echo $l['id']; ?>" class="btn btn-danger btn-sm js-fj-ajax" data-confirm="Désactiver ce livreur ?" data-confirm-i18n="admin_livreurs.confirmDesactiver"><span data-i18n="common.desactiver">Désactiver</span></a>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/livreurs&activer=<?php echo $l['id']; ?>" class="btn btn-gold btn-sm js-fj-ajax"><span data-i18n="common.activer">Activer</span></a>
                        <?php endif; ?>
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/livreurs&supprimer=<?php echo $l['id']; ?>" class="btn btn-danger btn-sm js-fj-ajax" data-confirm="Voulez-vous vraiment supprimer ce livreur ?" data-confirm-i18n="admin_livreurs.confirmSupprimer"><span data-i18n="common.supprimer">Supprimer</span></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($livreurs)): ?>
                <tr><td colspan="6" class="empty-state" data-i18n="admin_livreurs.aucunLivreur">Aucun livreur enregistré.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="modalFormLivreur" hidden>
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalFormLivreurTitle">
        <div class="modal-head">
            <h3 id="modalFormLivreurTitle" data-title-add="admin_livreurs.titreAjouter" data-title-edit="admin_livreurs.titreModifier"><?php echo $formMode === 'edit' ? 'Modifier le livreur' : 'Ajouter un livreur'; ?></h3>
            <button type="button" class="modal-close" data-modal-close aria-label="Fermer" data-i18n-aria="common.fermer">&times;</button>
        </div>
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/livreurs">
            <div class="form-grid">
                <div class="form-group">
                    <label data-i18n="common.prenom">Prénom</label>
                    <input type="text" name="prenom" value="<?php echo htmlspecialchars($prenom); ?>" required>
                </div>
                <div class="form-group">
                    <label data-i18n="common.nom">Nom</label>
                    <input type="text" name="nom" value="<?php echo htmlspecialchars($nom); ?>" required>
                </div>
                <div class="form-group">
                    <label data-i18n="common.email">Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="form-group">
                    <label data-i18n="common.telephone">Téléphone</label>
                    <input type="text" name="telephone" value="<?php echo htmlspecialchars($telephone); ?>">
                </div>
                <div class="form-group" data-only-add>
                    <label data-i18n="common.motDePasse">Mot de passe</label>
                    <input type="password" name="password" minlength="6" required>
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
            window.ouvrirModalForm('modalFormLivreur', '<?php echo $formMode; ?>', null, <?php echo json_encode(cle_i18n($erreur ?? ''), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP); ?>);
        }
    });
</script>
<?php endif; ?>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
