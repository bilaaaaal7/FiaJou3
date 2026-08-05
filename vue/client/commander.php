<?php
$pageTitle = "Commander - " . APP_NAME;
$extraCss = ['admin.css'];
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';

$fraisLivraison = 0;
if (isset($_POST['zone_id'])) {
    foreach ($zones as $z) {
        if ((int) $z['id'] === (int) $_POST['zone_id']) {
            $fraisLivraison = (float) $z['prix_livraison'];
            break;
        }
    }
}
$selectedZoneId = $_POST['zone_id'] ?? ($zones[0]['id'] ?? 0);

$dateDefaut = $_POST['date_livraison'] ?? ($panierModele->getDate() ?? '');
$heureDefaut = $_POST['heure_livraison'] ?? '';
$pauseDebutDefaut = $_POST['pause_debut'] ?? '';
$pauseFinDefaut = $_POST['pause_fin'] ?? '';
$priorityDefaut = isset($_POST['priority']) && !empty($_POST['priority']);
?>

<div style="max-width: 800px; margin: 0 auto;">

    <?php require ROOT_PATH . '/assets/inc/back_home.php'; ?>

    <div class="topbar">
        <h1>Finaliser la commande</h1>
    </div>

    <?php if (!empty($erreurs)): ?>
    <div class="alert-box alert-error">
        <ul style="margin:0; padding-left:18px;">
        <?php foreach ($erreurs as $err): ?>
            <li><?php echo htmlspecialchars($err); ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="panel" style="border: 2px solid var(--gold);">
        <h2 style="color: var(--gold-dark); font-size: 1.05rem;">Vos informations (profil)</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; font-size: 0.92rem;">
            <div><strong><?php echo htmlspecialchars($profil['prenom'] ?? ''); ?> <?php echo htmlspecialchars($profil['nom'] ?? ''); ?></strong></div>
            <div><?php echo htmlspecialchars($profil['email'] ?? ''); ?></div>
            <div><?php echo htmlspecialchars($profil['telephone'] ?? ''); ?></div>
            <div><?php echo htmlspecialchars($profil['adresse'] ?? ''); ?><?php echo !empty($profil['ville']) ? ', ' . htmlspecialchars($profil['ville']) : ''; ?></div>
        </div>
        <p style="color: var(--text-muted); font-size: 0.8rem; margin: 8px 0 0;">
            Ces informations proviennent de votre profil. Pour les modifier,
            <a href="<?php echo BASE_URL; ?>/index.php?route=client/profil" style="color: var(--gold-dark);">mettez à jour votre profil</a>.
        </p>
    </div>

    <div class="panel">
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=client/commander">
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0;">
                Livraison 7j/7. Pour être livré un jour J, commandez au plus tard la veille à
                <?php echo HEURE_LIMITE_COMMANDE; ?>. Le samedi, le menu est libre :
                tous les plats de la semaine sont commandables.
            </p>
            <div class="form-grid">
                <div class="form-group">
                    <label for="date_livraison">Date de livraison</label>
                    <input type="date" id="date_livraison" name="date_livraison"
                           value="<?php echo htmlspecialchars($dateDefaut); ?>" required>
                </div>

                <div class="form-group">
                    <label for="heure_livraison">Heure de livraison</label>
                    <input type="time" id="heure_livraison" name="heure_livraison"
                           value="<?php echo htmlspecialchars($heureDefaut); ?>" required>
                </div>

                <div class="form-group">
                    <label for="zone_id">Zone de livraison</label>
                    <select id="zone_id" name="zone_id" required>
                        <?php foreach ($zones as $zone): ?>
                        <option value="<?php echo (int) $zone['id']; ?>"
                            <?php echo ((int) $zone['id'] === (int) $selectedZoneId) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($zone['nom']); ?>
                            (<?php echo number_format((float) $zone['prix_livraison'], 2, ',', ' '); ?> DH)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Commande prioritaire</label>
                    <div style="display: flex; gap: 18px; align-items: center; padding-top: 8px;">
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                            <input type="radio" name="priority" value="1" <?php echo $priorityDefaut ? 'checked' : ''; ?>>
                            Oui
                        </label>
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                            <input type="radio" name="priority" value="0" <?php echo !$priorityDefaut ? 'checked' : ''; ?>>
                            Non
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="pause_debut">Pause — début</label>
                    <input type="time" id="pause_debut" name="pause_debut"
                           value="<?php echo htmlspecialchars($pauseDebutDefaut); ?>">
                </div>

                <div class="form-group">
                    <label for="pause_fin">Pause — fin</label>
                    <input type="time" id="pause_fin" name="pause_fin"
                           value="<?php echo htmlspecialchars($pauseFinDefaut); ?>">
                </div>
            </div>

            <div class="form-group" style="margin-top: 16px;">
                <label for="commentaire">Commentaire</label>
                <textarea id="commentaire" name="commentaire" rows="4"
                          style="width: 100%;"><?php echo htmlspecialchars($_POST['commentaire'] ?? ''); ?></textarea>
            </div>

            <div style="margin-top: 24px; padding: 16px; background: var(--gold-light); border-radius: 10px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span>Sous-total plats</span>
                    <span><?php echo number_format((float) $total, 2, ',', ' '); ?> DH</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span>Frais de livraison</span>
                    <span><?php echo number_format((float) $fraisLivraison, 2, ',', ' '); ?> DH</span>
                </div>
                <hr style="border: none; border-top: 1px solid var(--border); margin: 8px 0;">
                <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.15rem;">
                    <span>Total à payer</span>
                    <span style="color: var(--gold-dark);">
                        <?php echo number_format((float) $total + (float) $fraisLivraison, 2, ',', ' '); ?> DH
                    </span>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="commander" class="btn btn-gold">Valider la commande</button>
                <a href="<?php echo BASE_URL; ?>/index.php?route=client" class="btn btn-outline">Retour au menu</a>
            </div>
        </form>
    </div>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
