<?php
require_once "../includes/admin_auth.php";
require_once "../config/db.php";

if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->prepare("UPDATE users SET actif = NOT actif WHERE id = ?")->execute([$id]);
    header("Location: clients.php");
    exit;
}

$stmt = $pdo->query("
    SELECT users.id, users.email, users.actif, profiles.prenom, profiles.nom, profiles.telephone,
           profiles.ville, profiles.adresse,
           (SELECT COUNT(*) FROM orders WHERE orders.user_id = users.id) AS nb_commandes,
           (SELECT COALESCE(SUM(total),0) FROM orders WHERE orders.user_id = users.id AND orders.statut != 'annulee') AS total_depense
    FROM users
    INNER JOIN profiles ON users.id = profiles.user_id
    WHERE profiles.role = 'client'
    ORDER BY profiles.prenom
");
$clients = $stmt->fetchAll();

$pageTitle  = "Clients";
$activePage = "clients";
$roleLabel  = "Administrateur";
$navItems = [
    ['key'=>'dashboard',  'label'=>'Tableau de bord',   'href'=>'index.php',        'icon'=>'📊'],
    ['key'=>'commandes',  'label'=>'Commandes',         'href'=>'commandes.php',    'icon'=>'🧾'],
    ['key'=>'plats',      'label'=>'Produits',          'href'=>'plats.php',        'icon'=>'🍽️'],
    ['key'=>'categories', 'label'=>'Catégories',        'href'=>'categories.php',   'icon'=>'🗂️'],
    ['key'=>'clients',    'label'=>'Clients',           'href'=>'clients.php',      'icon'=>'👥'],
    ['key'=>'cuisiniers', 'label'=>'Cuisiniers',        'href'=>'cuisiniers.php',   'icon'=>'👨‍🍳'],
    ['key'=>'livreurs',   'label'=>'Livreurs',          'href'=>'livreurs.php',     'icon'=>'🛵'],
    ['key'=>'zones',      'label'=>'Zones de livraison','href'=>'zones.php',        'icon'=>'📍'],
    ['key'=>'utilisateurs','label'=>'Tous les comptes', 'href'=>'utilisateurs.php', 'icon'=>'⚙️'],
];
require_once "../includes/dashboard_layout_start.php";
?>

<div class="panel">
    <h2>Clients (<?php echo count($clients); ?>)</h2>
    <p style="color:#8a8a8a; font-size:0.88rem; margin-top:-10px;">
        Les clients créent eux-mêmes leur compte depuis la page d'inscription publique.
    </p>
    <?php if (empty($clients)) { ?>
        <div class="empty-state">Aucun client inscrit pour le moment.</div>
    <?php } else { ?>
    <div class="table-wrap">
        <table class="data-table">
            <tr><th>Nom</th><th>Email</th><th>Téléphone</th><th>Ville</th><th>Commandes</th><th>Dépensé</th><th>Statut</th><th>Actions</th></tr>
            <?php foreach ($clients as $c) { ?>
            <tr>
                <td><?php echo htmlspecialchars($c['prenom'].' '.$c['nom']); ?></td>
                <td><?php echo htmlspecialchars($c['email']); ?></td>
                <td><?php echo htmlspecialchars($c['telephone']); ?></td>
                <td><?php echo htmlspecialchars($c['ville']); ?></td>
                <td><?php echo $c['nb_commandes']; ?></td>
                <td><?php echo number_format($c['total_depense'],2); ?> DH</td>
                <td>
                    <?php if ($c['actif']) { ?><span class="badge-yes">● Actif</span>
                    <?php } else { ?><span class="badge-no">● Désactivé</span><?php } ?>
                </td>
                <td class="actions-cell">
                    <a class="btn btn-outline btn-sm" href="commandes.php?client=<?php echo urlencode($c['email']); ?>">Ses commandes</a>
                    <a class="btn <?php echo $c['actif'] ? 'btn-danger' : 'btn-outline'; ?> btn-sm" href="?toggle=<?php echo $c['id']; ?>"
                       data-confirm="<?php echo $c['actif'] ? 'Désactiver ce compte ?' : 'Réactiver ce compte ?'; ?>">
                        <?php echo $c['actif'] ? 'Désactiver' : 'Réactiver'; ?>
                    </a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
    <?php } ?>
</div>

<?php require_once "../includes/dashboard_layout_end.php"; ?>
