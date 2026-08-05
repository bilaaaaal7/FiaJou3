<?php
$pageTitle = "Menu de la semaine - " . APP_NAME;
$pageHeading = "Menu de la semaine";
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<?php
$erreursAdmin = [
    'nom'            => 'Le nom du menu est obligatoire.',
    'dates'          => 'La date de début doit être antérieure à la date de fin.',
    'chevauchement'  => 'Un menu non archivé couvre déjà cette période. Choisissez une autre semaine.',
    'duplicat'       => 'Ce produit figure déjà dans ce menu. Un produit ne peut apparaître qu\'un seul jour.',
];
if (isset($_GET['erreur']) && isset($erreursAdmin[$_GET['erreur']])): ?>
    <div class="alert-box alert-error"><?php echo htmlspecialchars($erreursAdmin[$_GET['erreur']]); ?></div>
<?php endif; ?>

<div class="panel">
    <h2>Créer un menu</h2>
    <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine" class="form-inline">
        <div class="form-group">
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
    <p class="panel-note">
        La période sert à savoir quel menu est actif pour chaque date de livraison (7j/7).
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
<?php
$platsAuMenu = [];
foreach ($itemsParJour as $items) {
    foreach ($items as $it) {
        $platsAuMenu[(int) $it['product_id']] = true;
    }
}
?>
<div class="panel">
    <h2>Modifier : <?php echo htmlspecialchars($menuActuel['nom']); ?>
        <?php if ($menuActuel['week_start']): ?>
            <span class="panel-sub">
                — <?php echo htmlspecialchars($menuActuel['week_start']); ?> → <?php echo htmlspecialchars($menuActuel['week_end']); ?>
            </span>
        <?php endif; ?>
    </h2>

    <p class="panel-note panel-note-block">
        Chaque jour peut comporter plusieurs plats : ajoutez des plats, retirez-les
        (×) et réordonnez-les (↑ / ↓). Un produit ne peut apparaître qu'un seul jour
        dans le menu. Le samedi est un jour de menu libre : aucun plat spécifique,
        tous les plats de la semaine y sont commandables.
    </p>

    <?php foreach (JOURS_MENU as $jour): ?>
        <?php $itemsJour = $itemsParJour[$jour] ?? []; ?>
        <div class="day-block">
            <div class="day-block-head">
                <h3><?php echo ucfirst($jour); ?></h3>
                <span class="day-block-count">
                    <?php echo count($itemsJour); ?> plat<?php echo count($itemsJour) > 1 ? 's' : ''; ?>
                </span>
            </div>

            <?php if (!empty($itemsJour)): ?>
                <div class="dish-chips">
                    <?php foreach ($itemsJour as $index => $item): ?>
                        <div class="dish-chip">
                            <div class="dish-chip-info">
                                <span class="dish-chip-name"><?php echo htmlspecialchars($item['plat_nom']); ?></span>
                                <span class="dish-chip-meta">
                                    <?php if (!empty($item['categorie'])): ?><?php echo htmlspecialchars($item['categorie']); ?><?php endif; ?>
                                    <?php if (isset($item['prix'])): ?> · <?php echo number_format((float) $item['prix'], 2, ',', ' '); ?> DH<?php endif; ?>
                                </span>
                            </div>
                            <div class="dish-chip-actions">
                                <?php if ($index > 0): ?>
                                    <a class="chip-btn" title="Monter" aria-label="Monter <?php echo htmlspecialchars($item['plat_nom']); ?>"
                                       href="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine&deplacer_item=<?php echo (int) $item['id']; ?>&direction=monter&menu_id=<?php echo $menuActuel['id']; ?>">↑</a>
                                <?php endif; ?>
                                <?php if ($index < count($itemsJour) - 1): ?>
                                    <a class="chip-btn" title="Descendre" aria-label="Descendre <?php echo htmlspecialchars($item['plat_nom']); ?>"
                                       href="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine&deplacer_item=<?php echo (int) $item['id']; ?>&direction=descendre&menu_id=<?php echo $menuActuel['id']; ?>">↓</a>
                                <?php endif; ?>
                                <a class="chip-btn chip-btn-danger" title="Retirer" aria-label="Retirer <?php echo htmlspecialchars($item['plat_nom']); ?>"
                                   href="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine&supprimer_item=<?php echo (int) $item['id']; ?>&menu_id=<?php echo $menuActuel['id']; ?>"
                                   data-confirm="Retirer <?php echo htmlspecialchars($item['plat_nom']); ?> du menu ?">×</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="panel-note panel-note-block">Aucun plat pour ce jour.</p>
            <?php endif; ?>

            <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine&voir=<?php echo $menuActuel['id']; ?>" class="day-add-form">
                <input type="hidden" name="menu_id" value="<?php echo $menuActuel['id']; ?>">
                <input type="hidden" name="jour" value="<?php echo $jour; ?>">
                <select name="product_id" required aria-label="Ajouter un plat à <?php echo ucfirst($jour); ?>">
                    <option value="">— Ajouter un plat à <?php echo ucfirst($jour); ?> —</option>
                    <?php foreach ($plats as $p): ?>
                        <?php
                        $dejaDansLeMenu = isset($platsAuMenu[(int) $p['id']]);
                        $optionLabel = htmlspecialchars($p['nom']) . ' (' . number_format((float) $p['prix'], 2, ',', ' ') . ' DH)';
                        ?>
                        <option value="<?php echo (int) $p['id']; ?>" <?php echo $dejaDansLeMenu ? 'disabled' : ''; ?>>
                            <?php echo $optionLabel . ($dejaDansLeMenu ? ' — déjà dans le menu' : ''); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="ajouter_item" class="btn btn-gold btn-sm">Ajouter</button>
            </form>
        </div>
    <?php endforeach; ?>

    <h3><?php echo ucfirst(JOUR_MENU_LIBRE); ?> — Menu libre</h3>
    <p class="panel-note panel-note-block">
        Le samedi ne comporte aucun plat spécifique : il n'est pas configurable.
        Tous les plats du menu de la semaine (<?php echo implode(', ', array_map('ucfirst', JOURS_MENU)); ?>)
        y sont proposés à la commande.
    </p>
</div>
<?php endif; ?>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
