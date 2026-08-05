<?php
$pageTitle = "Mes commandes - " . APP_NAME;
$extraCss = ['admin.css'];
$bodyClass = 'profil-sans-sidebar';
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';

$prenomCmd = trim((string) ($_SESSION['prenom'] ?? ''));
$nomCmd    = trim((string) ($_SESSION['nom'] ?? ''));
$emailCmd  = (string) ($_SESSION['email'] ?? '');
$initialesCmd = mb_strtoupper(mb_substr($prenomCmd, 0, 1) . mb_substr($nomCmd, 0, 1));
if ($initialesCmd === '') {
    $initialesCmd = '?';
}
?>

<div class="page-profil">

    <div class="topbar">
        <h1>Mes commandes</h1>
        <div class="topbar-actions">
            <p class="profil-subtitle">Suivez vos commandes, du panier jusqu'à la livraison.</p>
            <a href="<?php echo BASE_URL; ?>/index.php?route=client" class="btn btn-gold btn-sm">Consulter le menu</a>
        </div>
    </div>

    <div class="profil-hero">
        <span class="profil-hero-avatar"><?php echo htmlspecialchars($initialesCmd); ?></span>
        <div class="profil-hero-info">
            <strong><?php echo htmlspecialchars(trim($prenomCmd . ' ' . $nomCmd)); ?></strong>
            <span><?php echo htmlspecialchars($emailCmd); ?></span>
        </div>
    </div>

    <?php if (!empty($commandes)): ?>
    <div class="panel profil-card">
        <div class="filter-bar">
            <div class="form-group">
                <label>Filtrer</label>
                <select id="filterMesCommandes" onchange="filtrerMesCommandes()">
                    <option value="toutes">Toutes</option>
                    <option value="en_cours">En cours</option>
                    <option value="livrees">Livrées</option>
                    <option value="annulees">Annulées</option>
                </select>
            </div>
        </div>
        <div class="table-wrap">
            <table class="data-table" id="tableMesCommandes">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date commande</th>
                        <th>Date livraison</th>
                        <th>Heure</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Commentaire</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($commandes as $commande): ?>
                    <tr data-statut="<?php echo htmlspecialchars($commande['statut']); ?>">
                        <td><?php echo (int) $commande['id']; ?></td>
                        <td><?php echo htmlspecialchars($commande['date_commande']); ?></td>
                        <td><?php echo htmlspecialchars($commande['date_livraison']); ?></td>
                        <td><?php echo htmlspecialchars($commande['heure_livraison']); ?></td>
                        <td><?php echo number_format((float) $commande['total'], 2, ',', ' '); ?> DH</td>
                        <td>
                            <span class="badge-status st-<?php echo htmlspecialchars($commande['statut']); ?>">
                                <?php echo STATUTS_COMMANDE[$commande['statut']] ?? $commande['statut']; ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($commande['commentaire'] ?? '-'); ?></td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=client/detail-commande&id=<?php echo (int) $commande['id']; ?>"
                               class="btn btn-outline btn-sm">Détail</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function filtrerMesCommandes() {
        var filtre = document.getElementById('filterMesCommandes').value;
        var enCours = ['en_attente', 'confirmee', 'en_preparation', 'prete', 'en_livraison'];
        var lignes = document.querySelectorAll('#tableMesCommandes tbody tr');
        lignes.forEach(function(ligne) {
            var statut = ligne.getAttribute('data-statut');
            var visible = filtre === 'toutes'
                || (filtre === 'en_cours' && enCours.indexOf(statut) !== -1)
                || (filtre === 'livrees' && statut === 'livree')
                || (filtre === 'annulees' && statut === 'annulee');
            ligne.style.display = visible ? '' : 'none';
        });
    }
    </script>

    <?php else: ?>
    <div class="panel profil-card">
        <div class="empty-state">
            Vous n'avez pas encore passé de commande.
            <br><br>
            <a href="<?php echo BASE_URL; ?>/index.php?route=client" class="btn btn-gold">Consulter le menu</a>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
