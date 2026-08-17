<?php
$pageTitle = "Gestion des clients - " . APP_NAME;
$i18nPage = 'admin_clients';
$pageHeading = "Clients";
$pageHeadingI18n = 'admin_clients.pageHeading';
$extraCss = ['admin.css'];
$extraJs = ['modal-form.js'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
$formOuvert = !empty($idModifier) || !empty($error);
$formMode = !empty($idModifier) ? 'edit' : 'add';

$roleI18n = [
    'client'    => 'nav.roleClient',
    'admin'     => 'nav.roleAdmin',
    'cuisinier' => 'nav.roleCuisinier',
    'livreur'   => 'nav.roleLivreur',
];
?>

<?php if (!empty($error)): ?>
    <div class="alert-box alert-error"><?php echo render_i18n($error); ?></div>
<?php endif; ?>

<?php if (isset($_GET['succes'])): ?>
    <div class="alert-box alert-success" data-i18n="admin_clients.succesAjout">Client ajouté avec succès.</div>
<?php endif; ?>

<?php if (isset($_GET['supprime'])): ?>
    <div class="alert-box alert-success" data-i18n="admin_clients.succesSupprime">Client supprimé avec succès.</div>
<?php endif; ?>

<div class="panel">
    <div class="panel-head-actions">
        <h2 data-i18n="admin_clients.listeClients">Liste des clients</h2>
        <button type="button" class="btn btn-gold" data-modal-open="modalFormUtilisateur" data-mode="add"><span data-i18n="admin_clients.ajouterClient">Ajouter un client</span></button>
    </div>
    <div class="table-wrap">
        <table class="data-table" id="tableUsers">
            <thead>
                <tr>
                    <th>ID</th>
                    <th data-i18n="common.prenom">Prénom</th>
                    <th data-i18n="common.nom">Nom</th>
                    <th data-i18n="common.email">Email</th>
                    <th data-i18n="common.role">Rôle</th>
                    <th data-i18n="common.statut">Statut</th>
                    <th data-i18n="common.actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['prenom']); ?></td>
                    <td><?php echo htmlspecialchars($user['nom']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><span class="badge-role" data-i18n="<?php echo $roleI18n[$user['role']] ?? ''; ?>"><?php echo $user['role']; ?></span></td>
                    <td>
                        <?php if ($user['actif']): ?>
                            <span class="badge-yes" data-i18n="common.actif">Actif</span>
                        <?php else: ?>
                            <span class="badge-no" data-i18n="common.inactif">Inactif</span>
                        <?php endif; ?>
                    </td>
                    <td class="actions-cell">
                        <button type="button" class="btn btn-outline btn-sm" data-modal-open="modalFormUtilisateur" data-mode="edit"
                            data-fields='<?php echo htmlspecialchars(json_encode([
                                'id' => (int) $user['id'],
                                'prenom' => $user['prenom'],
                                'nom' => $user['nom'],
                                'email' => $user['email'],
                                'telephone' => $user['telephone'] ?? '',
                                'adresse' => $user['adresse'] ?? '',
                                'ville' => $user['ville'] ?? '',
                                'role' => $user['role'],
                            ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES); ?>'><span data-i18n="common.modifier">Modifier</span></button>
                        <?php if ($user['actif']): ?>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/utilisateurs&desactiver=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" data-confirm="Désactiver ce client ?" data-confirm-i18n="admin_clients.confirmDesactiver"><span data-i18n="common.desactiver">Désactiver</span></a>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/utilisateurs&activer=<?php echo $user['id']; ?>" class="btn btn-gold btn-sm"><span data-i18n="common.activer">Activer</span></a>
                        <?php endif; ?>
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/utilisateurs&supprimer=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" data-confirm="Voulez-vous vraiment supprimer cet utilisateur ?" data-confirm-i18n="admin_clients.confirmSupprimer"><span data-i18n="common.supprimer">Supprimer</span></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($users)): ?>
                <tr><td colspan="7" class="empty-state" data-i18n="admin_clients.aucunClient">Aucun client enregistré.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="modalFormUtilisateur" hidden>
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalFormUtilisateurTitle">
        <div class="modal-head">
            <h3 id="modalFormUtilisateurTitle" data-title-add="admin_clients.titreAjouter" data-title-edit="admin_clients.titreModifier"><?php echo $formMode === 'edit' ? 'Modifier le client' : 'Ajouter un client'; ?></h3>
            <button type="button" class="modal-close" data-modal-close aria-label="Fermer" data-i18n-aria="common.fermer">&times;</button>
        </div>
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/utilisateurs">
            <div class="form-stack">
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
                <div class="form-group">
                    <label data-i18n="common.adresse">Adresse</label>
                    <input type="text" name="adresse" value="<?php echo htmlspecialchars($adresse); ?>">
                </div>
                <div class="form-group">
                    <label data-i18n="common.ville">Ville</label>
                    <input type="text" name="ville" value="<?php echo htmlspecialchars($ville); ?>">
                </div>
                <div class="form-group">
                    <label data-i18n="common.role">Rôle</label>
                    <select name="role">
                        <option value="client" <?php if ($roleUser == "client") echo "selected"; ?> data-i18n="nav.roleClient">Client</option>
                        <option value="admin" <?php if ($roleUser == "admin") echo "selected"; ?> data-i18n="nav.roleAdmin">Admin</option>
                        <option value="cuisinier" <?php if ($roleUser == "cuisinier") echo "selected"; ?> data-i18n="nav.roleCuisinier">Cuisinier</option>
                        <option value="livreur" <?php if ($roleUser == "livreur") echo "selected"; ?> data-i18n="nav.roleLivreur">Livreur</option>
                    </select>
                </div>
                <div class="form-group" data-only-add>
                    <label data-i18n="common.motDePasse">Mot de passe</label>
                    <input type="password" name="password" minlength="6" required>
                </div>
            </div>
            <input type="hidden" name="id" value="<?php echo $idModifier; ?>">
            <p class="modal-error" hidden><?php echo render_i18n($error ?? ''); ?></p>
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
            window.ouvrirModalForm('modalFormUtilisateur', '<?php echo $formMode; ?>', null, <?php echo json_encode(cle_i18n($error ?? ''), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP); ?>);
        }
    });
</script>
<?php endif; ?>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
