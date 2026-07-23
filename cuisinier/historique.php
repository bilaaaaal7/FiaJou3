<?php
require_once "../includes/cook_auth.php";
require_once "../config/db.php";

$cookId = $_SESSION['user_id'];

$statutLabels = [
    'en_attente'     => 'En attente',
    'confirmee'      => 'Confirmée',
    'en_preparation' => 'En préparation',
    'prete'          => 'Prête',
    'en_livraison'   => 'En livraison',
    'livree'         => 'Livrée',
    'annulee'        => 'Annulée',
];

$stmt = $pdo->prepare("
    SELECT osh.*, orders.date_livraison, profiles.prenom AS client_prenom, profiles.nom AS client_nom
    FROM order_status_history osh
    INNER JOIN orders ON osh.order_id = orders.id
    INNER JOIN users ON orders.user_id = users.id
    INNER JOIN profiles ON users.id = profiles.user_id
    WHERE osh.user_id = ?
    ORDER BY osh.date_modification DESC
    LIMIT 100
");
$stmt->execute([$cookId]);
$historique = $stmt->fetchAll();

$pageTitle  = "Historique";
$activePage = "historique";
$roleLabel  = "Cuisinier";
$navItems = [
    ['key'=>'dashboard',   'label'=>'Tableau de bord', 'href'=>'index.php',      'icon'=>'📊'],
    ['key'=>'historique',  'label'=>'Historique',      'href'=>'historique.php', 'icon'=>'🕓'],
];
require_once "../includes/dashboard_layout_start.php";
?>

<div class="panel">
    <h2>Mon activité récente</h2>
    <?php if (empty($historique)) { ?>
        <div class="empty-state">Aucune activité enregistrée pour le moment.</div>
    <?php } else { ?>
    <div class="table-wrap">
        <table class="data-table">
            <tr><th>Date</th><th>Commande</th><th>Client</th><th>Action</th><th>Remarque</th></tr>
            <?php foreach ($historique as $h) { ?>
            <tr>
                <td><?php echo date('d/m/Y H:i', strtotime($h['date_modification'])); ?></td>
                <td>#<?php echo $h['order_id']; ?></td>
                <td><?php echo htmlspecialchars($h['client_prenom'].' '.$h['client_nom']); ?></td>
                <td>
                    <?php if ($h['ancien_statut'] === $h['nouveau_statut']) { ?>
                        <span style="color:#8a8a8a;">Remarque ajoutée</span>
                    <?php } else { ?>
                        <span class="badge-status st-<?php echo $h['nouveau_statut']; ?>">
                            <?php echo $statutLabels[$h['nouveau_statut']] ?? $h['nouveau_statut']; ?>
                        </span>
                    <?php } ?>
                </td>
                <td><?php echo $h['commentaire'] ? htmlspecialchars($h['commentaire']) : '—'; ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
    <?php } ?>
</div>

<?php require_once "../includes/dashboard_layout_end.php"; ?>
