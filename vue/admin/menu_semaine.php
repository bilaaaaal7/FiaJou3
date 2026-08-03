<?php
$pageTitle = "Menu de la semaine - " . APP_NAME;
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<h1>Menu de la semaine</h1>

<?php
$erreursAdmin = [
    'nom'            => 'Le nom du menu est obligatoire.',
    'dates'          => 'La date de début doit être antérieure à la date de fin.',
    'chevauchement'  => 'Un menu non archivé couvre déjà cette période. Choisissez une autre semaine.',
    'jour'           => 'Ce jour comporte déjà un plat. Un seul plat par jour est autorisé.',
    'duplicat'       => 'Ce produit figure déjà dans ce menu. Un produit ne peut apparaître qu\'un seul jour.',
];
if (isset($_GET['erreur']) && isset($erreursAdmin[$_GET['erreur']])): ?>
    <div class="alert alert-danger py-2" role="alert"><?php echo htmlspecialchars($erreursAdmin[$_GET['erreur']]); ?></div>
<?php endif; ?>

<div class="panel">
    <h2>Créer un menu</h2>
    <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine" style="display:flex; gap:10px; align-items:end; flex-wrap:wrap;">
        <div class="form-group" style="flex:1; min-width:200px;">
            <label>Nom du menu</label>
            <input type="text" name="nom" placeholder="Ex: Menu semaine 30" required>
        </div>
        <div class="form-group">
            <label>Début de semaine</label>
            <input type="date" name="week_start">
        </div>
        <div class="form-group">
            <label>Fin de semaine</label>
            <input type="date" name="week_end">
        </div>
        <button type="submit" name="creer" class="btn btn-gold">Créer</button>
    </form>
    <p style="color: var(--text-muted); font-size: 0.8rem; margin-top: 8px;">
        La période sert à savoir quel menu est actif pour chaque date de livraison (lundi à vendredi).
    </p>
</div>

<div class="panel">
    <h2>Menus existants</h2>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Semaine</th>
                    <th>Date création</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($menus as $m): ?>
                <tr>
                    <td><?php echo $m['id']; ?></td>
                    <td><?php echo htmlspecialchars($m['nom']); ?></td>
                    <td>
                        <?php echo $m['week_start'] ? htmlspecialchars($m['week_start']) . ' → ' . htmlspecialchars($m['week_end']) : '<em>Non définie</em>'; ?>
                    </td>
                    <td><?php echo $m['date_creation']; ?></td>
                    <td>
                        <?php
                        $statutLabel = ['brouillon' => 'Brouillon', 'publie' => 'Publié', 'archive' => 'Archivé'];
                        $statutClass = ['brouillon' => 'st-en_attente', 'publie' => 'st-prete', 'archive' => 'st-annulee'];
                        ?>
                        <span class="badge-status <?php echo $statutClass[$m['statut']] ?? ''; ?>">
                            <?php echo $statutLabel[$m['statut']] ?? $m['statut']; ?>
                        </span>
                    </td>
                    <td class="actions-cell">
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine&voir=<?php echo $m['id']; ?>" class="btn btn-outline btn-sm">Voir / Modifier</a>
                        <?php if ($m['statut'] === 'brouillon'): ?>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine&publier=<?php echo $m['id']; ?>" class="btn btn-gold btn-sm" data-confirm="Publier ce menu ?">Publier</a>
                        <?php endif; ?>
                        <?php if ($m['statut'] === 'publie'): ?>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine&archiver=<?php echo $m['id']; ?>" class="btn btn-outline btn-sm" data-confirm="Archiver ce menu ?">Archiver</a>
                        <?php endif; ?>
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine&supprimer=<?php echo $m['id']; ?>" class="btn btn-danger btn-sm" data-confirm="Supprimer ce menu ?">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($menus)): ?>
                <tr><td colspan="6" class="empty-state">Aucun menu créé.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($menuActuel): ?>
<div class="panel">
    <h2>Modifier : <?php echo htmlspecialchars($menuActuel['nom']); ?>
        <?php if ($menuActuel['week_start']): ?>
            <span style="font-weight:400; color:var(--text-muted); font-size:0.9rem;">
                — <?php echo htmlspecialchars($menuActuel['week_start']); ?> → <?php echo htmlspecialchars($menuActuel['week_end']); ?>
            </span>
        <?php endif; ?>
    </h2>

    <div style="margin-bottom:20px;">
        <h3>Ajouter un plat</h3>
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine&voir=<?php echo $menuActuel['id']; ?>" style="display:flex; gap:10px; align-items:end; flex-wrap:wrap;">
            <input type="hidden" name="menu_id" value="<?php echo $menuActuel['id']; ?>">
            <div class="form-group">
                <label>Jour</label>
                <select name="jour" required>
                    <?php foreach (JOURS_LIVRAISON as $j): ?>
                        <option value="<?php echo $j; ?>"><?php echo ucfirst($j); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="flex:1;">
                <label>Produit</label>
                <select name="product_id" required>
                    <?php foreach ($plats as $p): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['nom']); ?> (<?php echo number_format($p['prix'], 2); ?> DH)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="ajouter_item" class="btn btn-gold">Ajouter</button>
        </form>
        <p style="color: var(--text-muted); font-size: 0.8rem; margin-top: 6px;">
            Un seul plat par jour (lundi à vendredi) et chaque produit ne peut apparaître qu'un seul jour.
        </p>
    </div>

    <?php $jours = ['lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche']; ?>
    <?php foreach ($jours as $jour): ?>
        <h3><?php echo ucfirst($jour); ?></h3>
        <?php if (!empty($itemsParJour[$jour])): ?>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr><th>Produit</th><th>Catégorie</th><th>Prix</th><th>Action</th></tr>
                </thead>
                <tbody>
                <?php foreach ($itemsParJour[$jour] as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['plat_nom']); ?></td>
                        <td><?php echo htmlspecialchars($item['categorie']); ?></td>
                        <td><?php echo number_format($item['prix'], 2); ?> DH</td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine&supprimer_item=<?php echo $item['id']; ?>&menu_id=<?php echo $menuActuel['id']; ?>"
                               class="btn btn-danger btn-sm" data-confirm="Retirer ce plat ?">Retirer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p style="color:var(--text-muted); padding:10px 0;">Aucun plat pour ce jour.</p>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
