<?php
$pageTitle = "Mes commandes - " . APP_NAME;
$extraCss = ['admin.css'];
$bodyClass = 'profil-sans-sidebar';
$i18nPage = 'mes_commandes';
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';

$prenomCmd = trim((string) ($_SESSION['prenom'] ?? ''));
$nomCmd    = trim((string) ($_SESSION['nom'] ?? ''));
$emailCmd  = (string) ($_SESSION['email'] ?? '');
$initialesCmd = mb_strtoupper(mb_substr($prenomCmd, 0, 1) . mb_substr($nomCmd, 0, 1));
if ($initialesCmd === '') {
    $initialesCmd = '?';
}

$cleStatutCommande = [
    'en_attente'    => 'mes_commandes.statutEnAttente',
    'confirmee'     => 'mes_commandes.statutConfirmee',
    'en_preparation'=> 'mes_commandes.statutEnPreparation',
    'prete'         => 'mes_commandes.statutPrete',
    'en_livraison'  => 'mes_commandes.statutEnLivraison',
    'livree'        => 'mes_commandes.statutLivree',
    'annulee'       => 'mes_commandes.statutAnnulee',
];
?>

<div class="page-profil">

    <?php require ROOT_PATH . '/assets/inc/back_home.php'; ?>

    <div class="topbar">
        <h1 data-i18n="mes_commandes.titre">Mes commandes</h1>
        <div class="topbar-actions">
            <p class="profil-subtitle" data-i18n="mes_commandes.sousTitre">Suivez vos commandes, du panier jusqu'à la livraison.</p>
            <a href="<?php echo BASE_URL; ?>/index.php?route=client" class="btn btn-gold btn-sm" data-i18n="common.consulterMenu">Consulter le menu</a>
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
                <label data-i18n="mes_commandes.filtrer">Filtrer</label>
                <select id="filterMesCommandes" onchange="filtrerMesCommandes()">
                    <option value="toutes" data-i18n="mes_commandes.filtreToutes">Toutes</option>
                    <option value="en_cours" data-i18n="mes_commandes.filtreEnCours">En cours</option>
                    <option value="livrees" data-i18n="mes_commandes.filtreLivrees">Livrées</option>
                    <option value="annulees" data-i18n="mes_commandes.filtreAnnulees">Annulées</option>
                </select>
            </div>
        </div>
        <div class="table-wrap">
            <table class="data-table" id="tableMesCommandes">
                <thead>
                    <tr>
                        <th>#</th>
                        <th data-i18n="mes_commandes.dateCommande">Date commande</th>
                        <th data-i18n="mes_commandes.dateLivraison">Date livraison</th>
                        <th data-i18n="mes_commandes.heure">Heure</th>
                        <th data-i18n="mes_commandes.total">Total</th>
                        <th data-i18n="mes_commandes.statut">Statut</th>
                        <th data-i18n="mes_commandes.commentaire">Commentaire</th>
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
                            <span class="badge-status st-<?php echo htmlspecialchars($commande['statut']); ?>"
                                  data-i18n="<?php echo $cleStatutCommande[$commande['statut']] ?? ''; ?>">
                                <?php echo STATUTS_COMMANDE[$commande['statut']] ?? $commande['statut']; ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($commande['commentaire'] ?? '-'); ?></td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=client/detail-commande&id=<?php echo (int) $commande['id']; ?>"
                               class="btn btn-outline btn-sm" data-i18n="mes_commandes.detail">Détail</a>
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
            <span data-i18n="mes_commandes.vide">Vous n'avez pas encore passé de commande.</span>
            <br><br>
            <a href="<?php echo BASE_URL; ?>/index.php?route=client" class="btn btn-gold" data-i18n="common.consulterMenu">Consulter le menu</a>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
