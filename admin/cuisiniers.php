<?php
require_once "../includes/admin_auth.php";
require_once "../config/db.php";

$ROLE = 'cuisinier';
$prenom = ""; $nom = ""; $email = ""; $telephone = "";
$idModifier = "";
$message = ""; $messageType = "info";

// --- Créer ---
if (isset($_POST['ajouter'])) {
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        $message = "Cet email est déjà utilisé."; $messageType = "error";
    } elseif (strlen($password) < 6) {
        $message = "Le mot de passe doit contenir au moins 6 caractères."; $messageType = "error";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password, actif) VALUES (?, ?, 1)");
        $stmt->execute([$email, $hash]);
        $userId = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO profiles (user_id, prenom, nom, telephone, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $prenom, $nom, $telephone, $ROLE]);

        header("Location: cuisiniers.php");
        exit;
    }
}

// --- Modifier ---
if (isset($_POST['modifier'])) {
    $id = (int)$_POST['id'];
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);

    $pdo->prepare("UPDATE users SET email = ? WHERE id = ?")->execute([$email, $id]);
    $pdo->prepare("UPDATE profiles SET prenom=?, nom=?, telephone=? WHERE user_id = ?")
        ->execute([$prenom, $nom, $telephone, $id]);

    if (!empty($_POST['password'])) {
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$hash, $id]);
    }

    header("Location: cuisiniers.php");
    exit;
}

// --- Activer / désactiver ---
if (isset($_GET['toggle'])) {
    $pdo->prepare("UPDATE users SET actif = NOT actif WHERE id = ?")->execute([(int)$_GET['toggle']]);
    header("Location: cuisiniers.php");
    exit;
}

// --- Supprimer ---
if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    $nbAssign = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE assigned_cook_id = ?");
    $nbAssign->execute([$id]);
    if ($nbAssign->fetchColumn() > 0) {
        $message = "Impossible de supprimer : ce cuisinier est encore affecté à des commandes."; $messageType = "error";
    } else {
        $pdo->prepare("DELETE FROM profiles WHERE user_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        header("Location: cuisiniers.php");
        exit;
    }
}

// --- Pré-remplir pour modification ---
if (isset($_GET['modifier'])) {
    $id = (int)$_GET['modifier'];
    $stmt = $pdo->prepare("
        SELECT users.id, users.email, profiles.prenom, profiles.nom, profiles.telephone
        FROM users INNER JOIN profiles ON users.id = profiles.user_id
        WHERE users.id = ? AND profiles.role = ?
    ");
    $stmt->execute([$id, $ROLE]);
    $u = $stmt->fetch();
    if ($u) {
        $idModifier = $u['id']; $prenom = $u['prenom']; $nom = $u['nom'];
        $email = $u['email']; $telephone = $u['telephone'];
    }
}

$stmt = $pdo->prepare("
    SELECT users.id, users.email, users.actif, profiles.prenom, profiles.nom, profiles.telephone,
           (SELECT COUNT(*) FROM orders WHERE orders.assigned_cook_id = users.id AND orders.statut NOT IN ('livree','annulee')) AS commandes_actives
    FROM users INNER JOIN profiles ON users.id = profiles.user_id
    WHERE profiles.role = ?
    ORDER BY profiles.prenom
");
$stmt->execute([$ROLE]);
$cuisiniers = $stmt->fetchAll();

$pageTitle  = "Cuisiniers";
$activePage = "cuisiniers";
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
    <div class="alert-box alert-<?php echo $messageType === 'error' ? 'error' : 'info'; ?>"><?php echo htmlspecialchars($message); ?></div>
<?php } ?>

<div class="two-col">

    <div class="panel">
        <h2>Cuisiniers (<?php echo count($cuisiniers); ?>)</h2>
        <?php if (empty($cuisiniers)) { ?>
            <div class="empty-state">Aucun cuisinier pour le moment.</div>
        <?php } else { ?>
        <div class="table-wrap">
            <table class="data-table">
                <tr><th>Nom</th><th>Email</th><th>Téléphone</th><th>Commandes en cours</th><th>Statut</th><th>Actions</th></tr>
                <?php foreach ($cuisiniers as $c) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($c['prenom'].' '.$c['nom']); ?></td>
                    <td><?php echo htmlspecialchars($c['email']); ?></td>
                    <td><?php echo htmlspecialchars($c['telephone']); ?></td>
                    <td><?php echo $c['commandes_actives']; ?></td>
                    <td><?php echo $c['actif'] ? '<span class="badge-yes">● Actif</span>' : '<span class="badge-no">● Désactivé</span>'; ?></td>
                    <td class="actions-cell">
                        <a class="btn btn-outline btn-sm" href="?modifier=<?php echo $c['id']; ?>">Modifier</a>
                        <a class="btn <?php echo $c['actif'] ? 'btn-danger' : 'btn-outline'; ?> btn-sm" href="?toggle=<?php echo $c['id']; ?>">
                            <?php echo $c['actif'] ? 'Désactiver' : 'Réactiver'; ?>
                        </a>
                        <a class="btn btn-danger btn-sm" href="?supprimer=<?php echo $c['id']; ?>" data-confirm="Supprimer ce cuisinier ?">Supprimer</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
        <?php } ?>
    </div>

    <div class="panel">
        <h2><?php echo $idModifier ? "Modifier le cuisinier" : "Ajouter un cuisinier"; ?></h2>
        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" name="prenom" value="<?php echo htmlspecialchars($prenom); ?>" required>
                </div>
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" value="<?php echo htmlspecialchars($nom); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="text" name="telephone" value="<?php echo htmlspecialchars($telephone); ?>">
                </div>
                <div class="form-group" style="grid-column:1/-1;">
                    <label><?php echo $idModifier ? "Nouveau mot de passe (laisser vide pour ne pas changer)" : "Mot de passe"; ?></label>
                    <input type="password" name="password" <?php echo $idModifier ? '' : 'required'; ?> minlength="6">
                </div>
            </div>

            <input type="hidden" name="id" value="<?php echo htmlspecialchars($idModifier); ?>">

            <div class="form-actions">
                <?php if ($idModifier) { ?>
                    <button type="submit" name="modifier" class="btn btn-gold">Enregistrer</button>
                    <a href="cuisiniers.php" class="btn btn-outline">Annuler</a>
                <?php } else { ?>
                    <button type="submit" name="ajouter" class="btn btn-gold">Créer le compte</button>
                <?php } ?>
            </div>
        </form>
    </div>

</div>

<?php require_once "../includes/dashboard_layout_end.php"; ?>
