<?php
$pageTitle = "Menu de la semaine - " . APP_NAME;
$i18nPage = 'admin_menu_semaine';
$pageHeading = "Menu de la semaine";
$pageHeadingI18n = 'admin_menu_semaine.pageHeading';
$extraCss = ['admin.css'];
$extraJs = ['menu-semaine-admin.js'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

<?php
$erreursAdmin = [
    'nom'             => ['Le nom du menu est obligatoire.', 'admin_menu_semaine.erreurNom'],
    'dates'           => ['La date de début doit être antérieure à la date de fin.', 'admin_menu_semaine.erreurDates'],
    'chevauchement'   => ['Un menu non archivé couvre déjà cette période. Choisissez une autre semaine.', 'admin_menu_semaine.erreurChevauchement'],
    'duplicat_semaine'=> ['La semaine suivante possède déjà un menu : elle n\'a pas été dupliquée.', 'admin_menu_semaine.erreurDuplicat'],
    'modification'    => ['La modification de l\'entrée du menu a échoué.', 'admin_menu_semaine.erreurModification'],
];
if (isset($_GET['erreur']) && isset($erreursAdmin[$_GET['erreur']])): ?>
    <div class="alert-box alert-error" data-i18n="<?php echo $erreursAdmin[$_GET['erreur']][1]; ?>"><?php echo htmlspecialchars($erreursAdmin[$_GET['erreur']][0]); ?></div>
<?php endif; ?>

<div class="panel week-header">
    <div class="week-header-top">
        <h2 data-i18n="admin_menu_semaine.planificationHebdo">Planification hebdomadaire</h2>
        <?php if ($estSemaineCourante): ?>
            <span class="badge-status st-prete" data-i18n="admin_menu_semaine.semaineEnCours">Semaine en cours</span>
        <?php endif; ?>
    </div>
    <div class="week-nav">
        <div class="week-nav-info">
            <span class="week-label"><?php echo htmlspecialchars($libelleSemaine); ?></span>
            <span class="week-dates"><?php echo date('d/m/Y', strtotime($lundi)); ?> → <?php echo date('d/m/Y', strtotime($dimanche)); ?></span>
        </div>
        <div class="week-nav-actions">
            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine&semaine=<?php echo $semainePrecedente; ?>" class="btn btn-outline btn-sm"><span data-i18n="admin_menu_semaine.semainePrecedente">← Semaine précédente</span></a>
            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine&semaine=<?php echo $semaineSuivante; ?>" class="btn btn-outline btn-sm"><span data-i18n="admin_menu_semaine.semaineSuivante">Semaine suivante →</span></a>
            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine&creer_semaine=<?php echo $semaineSuivante; ?>" class="btn btn-gold btn-sm"><span data-i18n="admin_menu_semaine.nouvelleSemaine">+ Nouvelle semaine</span></a>
            <?php if ($menuActuel): ?>
                <a href="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine&dupliquer=<?php echo (int) $menuActuel['id']; ?>&semaine=<?php echo $lundi; ?>"
                   class="btn btn-gold btn-sm" data-confirm="Dupliquer ce menu vers la semaine suivante ?" data-confirm-i18n="admin_menu_semaine.confirmDupliquer"><span data-i18n="admin_menu_semaine.dupliquerSemaine">Dupliquer la semaine</span></a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($menuActuel): ?>
<div class="panel" id="menuSemaineEditor">
    <h2><span data-i18n="admin_menu_semaine.modifierTitre">Modifier :</span> <?php echo htmlspecialchars($menuActuel['nom']); ?>
        <span class="panel-sub">— <?php echo htmlspecialchars($libelleSemaine); ?></span>
    </h2>

    <p class="panel-note panel-note-block" data-i18n="admin_menu_semaine.notePluriel">
        Chaque jour peut comporter plusieurs plats : ajoutez des plats, modifiez-les
        (Modifier), retirez-les (Retirer) et réordonnez-les (↑ / ↓). Un même plat peut
        apparaître plusieurs fois dans la semaine (sur plusieurs jours) et dans toutes
        les semaines : chaque semaine reste indépendante. Le samedi est un jour de
        menu libre : aucun plat spécifique, tous les plats de la semaine y sont commandables.
        Le dimanche est exclu : aucune commande n'est possible ce jour-là.
    </p>

    <?php foreach (JOURS_MENU as $jour): ?>
        <?php $itemsJour = $itemsParJour[$jour] ?? []; ?>
        <div class="day-block">
            <div class="day-block-head">
                <h3 data-i18n="jours.<?php echo $jour; ?>"><?php echo ucfirst($jour); ?></h3>
                <span class="day-block-count">
                    <?php echo count($itemsJour); ?> <span data-i18n="admin_menu_semaine.plats">plat(s)</span>
                </span>
            </div>

            <?php if (!empty($itemsJour)): ?>
                <div class="dish-chips">
                    <?php foreach ($itemsJour as $index => $item): ?>
                        <div class="dish-chip" id="item-<?php echo (int) $item['id']; ?>">
                            <div class="dish-chip-info">
                                <span class="dish-chip-name"><?php echo htmlspecialchars($item['plat_nom']); ?></span>
                                <span class="dish-chip-meta">
                                    <span class="chip-cat"><?php echo $item['categorie'] ? htmlspecialchars($item['categorie']) : ''; ?></span>
                                    <?php if (isset($item['prix'])): ?>
                                        <span class="chip-prix"><?php echo ($item['categorie'] ? ' · ' : '') . number_format((float) $item['prix'], 2, ',', ' '); ?> DH</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="dish-chip-actions">
                                <?php if ($index > 0): ?>
                                    <a class="chip-btn" title="Monter" aria-label="Monter <?php echo htmlspecialchars($item['plat_nom']); ?>"
                                       href="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine&deplacer_item=<?php echo (int) $item['id']; ?>&direction=monter&menu_id=<?php echo (int) $menuActuel['id']; ?>">↑</a>
                                <?php endif; ?>
                                <?php if ($index < count($itemsJour) - 1): ?>
                                    <a class="chip-btn" title="Descendre" aria-label="Descendre <?php echo htmlspecialchars($item['plat_nom']); ?>"
                                       href="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine&deplacer_item=<?php echo (int) $item['id']; ?>&direction=descendre&menu_id=<?php echo (int) $menuActuel['id']; ?>">↓</a>
                                <?php endif; ?>
                                <button type="button" class="btn btn-outline btn-sm chip-btn-edit" data-modal-edit
                                        title="Modifier <?php echo htmlspecialchars($item['plat_nom']); ?>"
                                        data-item-id="<?php echo (int) $item['id']; ?>"
                                        data-menu-id="<?php echo (int) $menuActuel['id']; ?>"
                                        data-product-id="<?php echo (int) $item['product_id']; ?>"
                                        data-nom="<?php echo htmlspecialchars($item['nom'] ?? ''); ?>"
                                        data-nom-en="<?php echo htmlspecialchars($item['nom_en'] ?? ''); ?>"
                                        data-nom-ar="<?php echo htmlspecialchars($item['nom_ar'] ?? ''); ?>"
                                        data-category-id="<?php echo (int) ($item['categorie_id'] ?? 0); ?>"
                                        data-prix="<?php echo (float) $item['prix']; ?>"
                                        data-description="<?php echo htmlspecialchars($item['description'] ?? ''); ?>"
                                        data-description-en="<?php echo htmlspecialchars($item['description_en'] ?? ''); ?>"
                                        data-description-ar="<?php echo htmlspecialchars($item['description_ar'] ?? ''); ?>"><span data-i18n="common.modifier">Modifier</span></button>
                                <a class="btn btn-danger btn-sm" title="Retirer" aria-label="Retirer <?php echo htmlspecialchars($item['plat_nom']); ?>"
                                   href="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine&supprimer_item=<?php echo (int) $item['id']; ?>&menu_id=<?php echo (int) $menuActuel['id']; ?>"
                                   data-confirm="Retirer <?php echo htmlspecialchars($item['plat_nom']); ?> du menu ?" data-confirm-i18n="admin_menu_semaine.confirmRetirer"><span data-i18n="common.retirer">Retirer</span></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="panel-note panel-note-block" data-i18n="admin_menu_semaine.aucunPlatJour">Aucun plat pour ce jour.</p>
            <?php endif; ?>

            <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine&semaine=<?php echo urlencode($lundi); ?>" class="day-add-form">
                <input type="hidden" name="menu_id" value="<?php echo (int) $menuActuel['id']; ?>">
                <input type="hidden" name="jour" value="<?php echo $jour; ?>">
                <select name="product_id" required aria-label="Ajouter un plat à <?php echo ucfirst($jour); ?>">
                    <option value="">— <span data-i18n="admin_menu_semaine.ajouterPlat">Ajouter un plat</span> à <span data-i18n="jours.<?php echo $jour; ?>"><?php echo ucfirst($jour); ?></span> —</option>
                    <?php foreach ($plats as $p): ?>
                        <option value="<?php echo (int) $p['id']; ?>">
                            <?php echo htmlspecialchars(localiser($p, 'nom')) . ' (' . number_format((float) $p['prix'], 2, ',', ' ') . ' DH)'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="ajouter_item" class="btn btn-gold btn-sm" data-i18n="common.ajouter">Ajouter</button>
            </form>
        </div>
    <?php endforeach; ?>

    <h3><span data-i18n="jours.<?php echo JOUR_MENU_LIBRE; ?>"><?php echo ucfirst(JOUR_MENU_LIBRE); ?></span> — <span data-i18n="admin_menu_semaine.menuLibre">Menu libre</span></h3>
    <p class="panel-note panel-note-block" data-i18n="admin_menu_semaine.noteSamediLibre">
        Le samedi ne comporte aucun plat spécifique : il n'est pas configurable.
        Tous les plats du menu de la semaine (<?php echo implode(', ', array_map('ucfirst', JOURS_MENU)); ?>)
        y sont proposés à la commande.
    </p>
</div>
<?php else: ?>
<div class="panel">
    <div class="empty-state">
        <span data-i18n="admin_menu_semaine.aucunMenuExiste">Aucun menu n'existe pour la semaine choisie.</span>
        <br><br>
        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine&creer_semaine=<?php echo $lundi; ?>"
           class="btn btn-gold" data-i18n="admin_menu_semaine.creerMenuSemaine">Créer le menu de cette semaine</a>
    </div>
</div>
<?php endif; ?>

<div class="panel">
    <h2 data-i18n="admin_menu_semaine.nomMenu">Créer un menu (semaine choisie)</h2>
    <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine" class="form-inline">
        <div class="form-group">
            <label data-i18n="admin_menu_semaine.nomMenu">Nom du menu</label>
            <input type="text" name="nom" placeholder="Ex: Menu semaine 30" required>
        </div>
        <div class="form-group">
            <label data-i18n="admin_menu_semaine.debutSemaine">Début de semaine</label>
            <input type="date" name="week_start">
        </div>
        <div class="form-group">
            <label data-i18n="admin_menu_semaine.finSemaine">Fin de semaine</label>
            <input type="date" name="week_end">
        </div>
        <button type="submit" name="creer" class="btn btn-gold" data-i18n="admin_menu_semaine.creer">Créer</button>
    </form>
    <p class="panel-note" data-i18n="admin_menu_semaine.notePeriode">
        La période sert à savoir quel menu est actif pour chaque date de livraison (7j/7).
        Utilisez plutôt « + Nouvelle semaine » ou « Créer le menu de cette semaine » pour une
        planification semaine par semaine.
    </p>
</div>

<div class="panel">
    <h2 data-i18n="admin_menu_semaine.menusExistants">Menus existants</h2>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th data-i18n="admin_menu_semaine.nomMenu">Nom</th>
                    <th data-i18n="common.semaine">Semaine</th>
                    <th data-i18n="admin_menu_semaine.dateCreation">Date création</th>
                    <th data-i18n="common.statut">Statut</th>
                    <th data-i18n="common.actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($menus as $m): ?>
                <tr>
                    <td><?php echo $m['id']; ?></td>
                    <td><?php echo htmlspecialchars($m['nom']); ?></td>
                    <td>
                        <?php if ($m['week_start']): ?>
                            <?php
                            $numeroMenu = $m['numero'] !== null ? (int) $m['numero'] : MenuSemaineModele::numeroSemaine($m['week_start']);
                            echo 'Semaine ' . $numeroMenu . ' — '
                                . date('d/m/Y', strtotime($m['week_start']))
                                . ' → ' . date('d/m/Y', strtotime($m['week_end']));
                            ?>
                        <?php else: ?>
                            <em data-i18n="admin_menu_semaine.nonDefinie">Non définie</em>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $m['date_creation']; ?></td>
                    <td>
                        <?php
                        $statutLabel = ['brouillon' => 'Brouillon', 'publie' => 'Publié', 'archive' => 'Archivé'];
                        $statutClass = ['brouillon' => 'st-en_attente', 'publie' => 'st-prete', 'archive' => 'st-annulee'];
                        $statutI18n = ['brouillon' => 'admin_menu_semaine.brouillon', 'publie' => 'admin_menu_semaine.publie', 'archive' => 'admin_menu_semaine.archive'];
                        ?>
                        <span class="badge-status <?php echo $statutClass[$m['statut']] ?? ''; ?>" data-i18n="<?php echo $statutI18n[$m['statut']] ?? ''; ?>">
                            <?php echo $statutLabel[$m['statut']] ?? $m['statut']; ?>
                        </span>
                    </td>
                    <td class="actions-cell">
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine&<?php echo $m['week_start'] ? 'semaine=' . urlencode($m['week_start']) : 'voir=' . (int) $m['id']; ?>" class="btn btn-outline btn-sm" data-i18n="admin_menu_semaine.voirModifier">Voir / Modifier</a>
                        <?php if ($m['statut'] === 'brouillon'): ?>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine&publier=<?php echo (int) $m['id']; ?>&semaine=<?php echo urlencode($lundi); ?>" class="btn btn-gold btn-sm" data-confirm="Publier ce menu ?" data-confirm-i18n="admin_menu_semaine.confirmPublier" data-i18n="common.publier">Publier</a>
                        <?php endif; ?>
                        <?php if ($m['statut'] === 'publie'): ?>
                            <a href="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine&archiver=<?php echo (int) $m['id']; ?>&semaine=<?php echo urlencode($lundi); ?>" class="btn btn-outline btn-sm" data-confirm="Archiver ce menu ?" data-confirm-i18n="admin_menu_semaine.confirmArchiver" data-i18n="common.archiver">Archiver</a>
                        <?php endif; ?>
                        <a href="<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine&supprimer=<?php echo (int) $m['id']; ?>&semaine=<?php echo urlencode($lundi); ?>" class="btn btn-danger btn-sm" data-confirm="Supprimer ce menu ?" data-confirm-i18n="admin_menu_semaine.confirmSupprimer" data-i18n="common.supprimer">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($menus)): ?>
                <tr><td colspan="6" class="empty-state" data-i18n="admin_menu_semaine.aucunMenuCree">Aucun menu créé.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="modalModifierItem" hidden>
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalModifierItemTitle">
        <div class="modal-head">
            <h3 id="modalModifierItemTitle" data-i18n="admin_menu_semaine.modifierLePlat">Modifier le plat</h3>
            <button type="button" class="modal-close" data-modal-close aria-label="Fermer" data-i18n-aria="common.fermer">&times;</button>
        </div>
        <form id="formModifierItem" novalidate>
            <input type="hidden" name="item_id" id="mi-item-id">
            <input type="hidden" name="menu_id" id="mi-menu-id">
            <div class="form-group">
                <label for="mi-product" data-i18n="common.produit">Plat</label>
                <select name="product_id" id="mi-product" required>
                    <?php foreach ($plats as $p): ?>
                        <option value="<?php echo (int) $p['id']; ?>"
                                data-nom="<?php echo htmlspecialchars($p['nom']); ?>"
                                data-nom-en="<?php echo htmlspecialchars($p['nom_en'] ?? ''); ?>"
                                data-nom-ar="<?php echo htmlspecialchars($p['nom_ar'] ?? ''); ?>"
                                data-category="<?php echo (int) $p['category_id']; ?>"
                                data-prix="<?php echo (float) $p['prix']; ?>"
                                data-description="<?php echo htmlspecialchars($p['description'] ?? ''); ?>"
                                data-description-en="<?php echo htmlspecialchars($p['description_en'] ?? ''); ?>"
                                data-description-ar="<?php echo htmlspecialchars($p['description_ar'] ?? ''); ?>">
                            <?php echo htmlspecialchars(localiser($p, 'nom')) . ' (' . number_format((float) $p['prix'], 2, ',', ' ') . ' DH)'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="form-hint" data-i18n="admin_menu_semaine.remplacePlatHint">Remplacer le plat pour cette semaine uniquement.</p>
            </div>
            <div class="form-group">
                <label for="mi-nom" data-i18n="admin_menu_semaine.nomAffiche">Nom affiché cette semaine</label>
                <input type="text" name="nom" id="mi-nom">
            </div>
            <div class="form-group">
                <label for="mi-nom-en" data-i18n="admin_menu_semaine.nomAfficheEn">Nom (EN) — cette semaine</label>
                <input type="text" name="nom_en" id="mi-nom-en">
            </div>
            <div class="form-group">
                <label for="mi-nom-ar" data-i18n="admin_menu_semaine.nomAfficheAr">Nom (AR) — cette semaine</label>
                <input type="text" name="nom_ar" id="mi-nom-ar" dir="rtl">
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="mi-category" data-i18n="admin_menu_semaine.categorieCetteSemaine">Catégorie (cette semaine)</label>
                    <select name="category_id" id="mi-category">
                        <?php foreach ($categories as $c): ?>
                            <option value="<?php echo (int) $c['id']; ?>"><?php echo htmlspecialchars(localiser($c, 'nom')); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="mi-prix" data-i18n="admin_menu_semaine.prixEnDH">Prix en DH (cette semaine)</label>
                    <input type="number" name="prix" id="mi-prix" step="0.01" min="0">
                </div>
            </div>
            <div class="form-group">
                <label for="mi-description" data-i18n="admin_menu_semaine.descriptionCetteSemaine">Description (cette semaine)</label>
                <textarea name="description" id="mi-description" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="mi-description-en" data-i18n="admin_menu_semaine.descriptionCetteSemaineEn">Description (EN) — cette semaine</label>
                <textarea name="description_en" id="mi-description-en" rows="2"></textarea>
            </div>
            <div class="form-group">
                <label for="mi-description-ar" data-i18n="admin_menu_semaine.descriptionCetteSemaineAr">Description (AR) — cette semaine</label>
                <textarea name="description_ar" id="mi-description-ar" rows="2" dir="rtl"></textarea>
            </div>
            <p class="panel-note" data-i18n="admin_menu_semaine.noteModifications">
                Ces modifications ne s'appliquent qu'à cette semaine : le plat réutilisable
                et les autres semaines ne sont pas touchés.
            </p>
            <p class="modal-error" id="mi-error" hidden></p>
            <div class="form-actions">
                <button type="submit" name="modifier_item" class="btn btn-gold" data-i18n="common.enregistrerCourt">Enregistrer</button>
                <button type="button" class="btn btn-outline" data-modal-close><span data-i18n="common.annuler">Annuler</span></button>
            </div>
        </form>
    </div>
</div>

<script>
    window.MENU_SEMAINE_ADMIN = { url: '<?php echo BASE_URL; ?>/index.php?route=admin/menu-semaine&ajax=1' };
</script>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
