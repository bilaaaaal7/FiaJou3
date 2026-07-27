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
?>

<div style="max-width: 800px; margin: 0 auto;">

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

    <div class="panel">
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=client/commander">
            <div class="form-grid">
                <div class="form-group">
                    <label for="date_livraison">Date de livraison</label>
                    <input type="date" id="date_livraison" name="date_livraison"
                           value="<?php echo htmlspecialchars($_POST['date_livraison'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="heure_livraison">Heure de livraison</label>
                    <input type="time" id="heure_livraison" name="heure_livraison"
                           value="<?php echo htmlspecialchars($_POST['heure_livraison'] ?? ''); ?>" required>
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
                    <label for="pause">Pause</label>
                    <input type="text" id="pause" name="pause"
                           value="<?php echo htmlspecialchars($_POST['pause'] ?? ''); ?>"
                           placeholder="Ex: sans oignon, allergie...">
                </div>
            </div>

            <div class="form-group" style="margin-top: 16px;">
                <label for="commentaire">Commentaire</label>
                <textarea id="commentaire" name="commentaire" rows="4"
                          style="width: 100%;"><?php echo htmlspecialchars($_POST['commentaire'] ?? ''); ?></textarea>
            </div>

            <div class="form-group" style="margin-top: 16px;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="priority" value="1"
                        <?php echo !empty($_POST['priority']) ? 'checked' : ''; ?>
                        style="width: 18px; height: 18px; accent-color: var(--gold);">
                    Commande prioritaire
                </label>
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
                <a href="<?php echo BASE_URL; ?>/index.php?route=client/panier" class="btn btn-outline">Retour au panier</a>
            </div>
        </form>
    </div>

</div>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
