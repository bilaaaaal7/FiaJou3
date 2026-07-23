<?php
require_once "../includes/admin_auth.php";
require_once "../config/db.php";

$nom = "";
$prix_livraison = "";
$idModifier = "";
$message = "";

if (isset($_POST['ajouter']) || isset($_POST['modifier'])) {
    $nom = trim($_POST['nom']);
    $prix_livraison = $_POST['prix_livraison'];

    if ($prix_livraison === "" || (float)$prix_livraison < 0) {
        $message = "Le tarif doit être un nombre positif.";
    } elseif (isset($_POST['ajouter'])) {
        $stmt = $pdo->prepare("INSERT INTO delivery_zones (nom, prix_livraison) VALUES (?, ?)");
        $stmt->execute([$nom, $prix_livraison]);
        header("Location: zones.php");
        exit;
    } else {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE delivery_zones SET nom = ?, prix_livraison = ? WHERE id = ?");
        $stmt->execute([$nom, $prix_livraison, $id]);
        header("Location: zones.php");
        exit;
    }
}

if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $nbCommandes = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE zone_id = ?");
    $nbCommandes->execute([$id]);
    if ($nbCommandes->fetchColumn() > 0) {
        $message = "Impossible de supprimer : des commandes utilisent déjà cette zone.";
    } else {
        $pdo->prepare("DELETE FROM delivery_zones WHERE id = ?")->execute([$id]);
        header("Location: zones.php");
        exit;
    }
}

if (isset($_GET['modifier'])) {
    $id = $_GET['modifier'];
    $stmt = $pdo->prepare("SELECT * FROM delivery_zones WHERE id = ?");
    $stmt->execute([$id]);
    $zone = $stmt->fetch();
    if ($zone) {
        $idModifier = $zone['id'];
        $nom = $zone['nom'];
        $prix_livraison = $zone['prix_livraison'];
    }
}

$zones = $pdo->query("
    SELECT delivery_zones.*, (SELECT COUNT(*) FROM orders WHERE orders.zone_id = delivery_zones.id) AS nb_commandes
    FROM delivery_zones ORDER BY nom
")->fetchAll();

$pageTitle  = "Zones de livraison";
$activePage = "zones";
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

<?php if ($message) { ?>
    <div class="alert-box alert-error"><?php echo htmlspecialchars($message); ?></div>
<?php } ?>

<div class="two-col">

    <div class="panel">
        <h2>Zones de livraison (<?php echo count($zones); ?>)</h2>
        <?php if (empty($zones)) { ?>
            <div class="empty-state">Aucune zone pour le moment.</div>
        <?php } else { ?>
        <div class="table-wrap">
            <table class="data-table">
                <tr><th>Zone</th><th>Frais de livraison</th><th>Commandes</th><th>Actions</th></tr>
                <?php foreach ($zones as $z) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($z['nom']); ?></td>
                    <td><?php echo number_format($z['prix_livraison'],2); ?> DH</td>
                    <td><?php echo $z['nb_commandes']; ?></td>
                    <td class="actions-cell">
                        <a class="btn btn-outline btn-sm" href="?modifier=<?php echo $z['id']; ?>">Modifier</a>
                        <a class="btn btn-danger btn-sm" href="?supprimer=<?php echo $z['id']; ?>" data-confirm="Supprimer cette zone ?">Supprimer</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
        <?php } ?>
    </div>

    <div class="panel">
        <h2><?php echo $idModifier ? "Modifier la zone" : "Ajouter une zone"; ?></h2>
        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Nom de la zone</label>
                    <input type="text" name="nom" value="<?php echo htmlspecialchars($nom); ?>" required>
                </div>
                <div class="form-group">
                    <label>Frais de livraison (DH)</label>
                    <input type="number" step="0.01" min="0" name="prix_livraison" value="<?php echo htmlspecialchars($prix_livraison); ?>" required>
                </div>
            </div>

            <input type="hidden" name="id" value="<?php echo htmlspecialchars($idModifier); ?>">

            <div class="form-actions">
                <?php if ($idModifier) { ?>
                    <button type="submit" name="modifier" class="btn btn-gold">Enregistrer</button>
                    <a href="zones.php" class="btn btn-outline">Annuler</a>
                <?php } else { ?>
                    <button type="submit" name="ajouter" class="btn btn-gold">Ajouter la zone</button>
                <?php } ?>
            </div>
        </form>
    </div>

</div>

<?php require_once "../includes/dashboard_layout_end.php"; ?>
