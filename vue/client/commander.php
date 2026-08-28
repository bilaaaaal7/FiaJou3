<?php
$pageTitle = "Commander - " . APP_NAME;
$extraCss = ['admin.css', 'profile-menu.css', 'client-public.css'];
$bodyClass = 'client-public-layout';
$i18nPage = 'commander';
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/client_navbar.php';

$fraisLivraison = $fraisLivraisonDefaut ?? 0;

$heureDefaut = $_POST['heure_livraison'] ?? '';
$societeDefaut = trim((string) ($_POST['societe_nom'] ?? ''));
$pauseDebutDefaut = $_POST['pause_debut'] ?? '';
$pauseFinDefaut = $_POST['pause_fin'] ?? '';
$priorityDefaut = isset($_POST['priority']) && !empty($_POST['priority']);

$latDefaut = (isset($_POST['lat']) && $_POST['lat'] !== '') ? htmlspecialchars($_POST['lat']) : '';
$lngDefaut = (isset($_POST['lng']) && $_POST['lng'] !== '') ? htmlspecialchars($_POST['lng']) : '';

$zoneDetecteeAffichage = $zoneDetectee ?? false;

// Données des zones exposées pour la détection côté navigateur (lecture seule).
$zonesPourJs = [];
foreach ($zones as $z) {
    $zonesPourJs[] = [
        'id'    => (int) $z['id'],
        'nom'   => htmlspecialchars(localiser($z, 'nom')),
        'prix'  => (float) $z['prix_livraison'],
        'lat'   => (float) ($z['lat'] ?? 0),
        'lng'   => (float) ($z['lng'] ?? 0),
        'rayon' => (float) ($z['rayon_km'] ?? 0),
    ];
}
$zonesJson = json_encode($zonesPourJs, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP);
?>

<div style="max-width: 800px; margin: 0 auto;">

    <?php require ROOT_PATH . '/assets/inc/back_home.php'; ?>

    <div class="topbar">
        <h1 data-i18n="commander.titre">Finaliser la commande</h1>
    </div>

    <?php if (!empty($erreurs)): ?>
    <div class="alert-box alert-error">
        <ul style="margin:0; padding-inline-start:18px;">
        <?php foreach ($erreurs as $err): ?>
            <li><?php echo htmlspecialchars($err); ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="panel" style="border: 2px solid var(--gold);">
        <h2 style="color: var(--gold-dark); font-size: 1.05rem;" data-i18n="commander.vosInfos">Vos informations (profil)</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; font-size: 0.92rem;">
            <div><strong><?php echo htmlspecialchars($profil['prenom'] ?? ''); ?> <?php echo htmlspecialchars($profil['nom'] ?? ''); ?></strong></div>
            <div><?php echo htmlspecialchars($profil['email'] ?? ''); ?></div>
            <div><?php echo htmlspecialchars($profil['telephone'] ?? ''); ?></div>
            <div><?php echo htmlspecialchars($profil['adresse'] ?? ''); ?><?php echo !empty($profil['ville']) ? ', ' . htmlspecialchars($profil['ville']) : ''; ?></div>
            <?php if (!empty($profil['societe'])): ?>
            <div><?php echo htmlspecialchars($profil['societe']); ?></div>
            <?php endif; ?>
        </div>
        <p style="color: var(--text-muted); font-size: 0.8rem; margin: 8px 0 0;">
            <span data-i18n="commander.infosProfil">Ces informations proviennent de votre profil. Pour les modifier,</span>
            <a href="<?php echo BASE_URL; ?>/index.php?route=client/profil" style="color: var(--gold-dark);" data-i18n="commander.majProfil">mettez à jour votre profil</a>.
        </p>
    </div>

    <div class="panel">
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=client/commander" id="formCommande">
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0;">
                <span data-i18n="commander.livraisonInfo">Livraison du lundi au samedi. Pour être livré un jour J, commandez au plus tard la veille à</span>
                <?php echo HEURE_LIMITE_COMMANDE; ?>.
                <span data-i18n="commander.livraisonInfoFin">Le samedi, le menu est libre : tous les plats de la semaine sont commandables.</span>
            </p>
            <div class="form-grid">
                <div class="form-group">
                    <label for="heure_livraison" data-i18n="commander.heureLivraison">Heure de livraison</label>
                    <input type="time" id="heure_livraison" name="heure_livraison"
                           value="<?php echo htmlspecialchars($heureDefaut); ?>" required>
                </div>

                <div class="form-group">
                    <label data-i18n="commander.zoneLivraison">Zone de livraison</label>
                    <input type="hidden" id="lat" name="lat" value="<?php echo $latDefaut; ?>">
                    <input type="hidden" id="lng" name="lng" value="<?php echo $lngDefaut; ?>">
                    <input type="hidden" id="zone_id" name="zone_id" value="<?php echo (int) $zoneIdDetecte; ?>">

                    <div class="geo-box" style="padding: 12px; border: 1px solid var(--border); border-radius: 10px; background: var(--surface);">
                        <div id="geoStatus">
                            <span class="geo-loading-msg" id="geoInitialMsg" style="display:<?php echo $zoneDetecteeAffichage ? 'none' : 'block'; ?>;"
                                data-i18n="commander.localisationEnCours">Détection de votre position pour déterminer la zone de livraison…</span>
                            <span class="geo-error-msg" id="geoErrorMsg" style="display:none;"
                                data-i18n="commander.localisationErreur">Impossible de déterminer votre zone de livraison. Veuillez autoriser la localisation et réessayer.</span>
                            <span class="geo-nazone-msg" id="geoNoZoneMsg" style="display:none;"
                                data-i18n="commander.aucuneZone">Aucune zone de livraison ne couvre votre position actuelle.</span>
                            <div id="geoZoneInfos" style="display:<?php echo $zoneDetecteeAffichage ? 'block' : 'none'; ?>; color: var(--text);">
                                <span style="color: var(--text-muted); font-size: 0.85rem;" data-i18n="commander.zoneDetectee">Zone de livraison détectée :</span>
                                <strong id="geoZoneNom"><?php echo $zoneDetecteeAffichage ? htmlspecialchars(localiser($zoneDetecteeAffichage, 'nom')) : ''; ?></strong>
                                <span id="geoZonePrix"><?php echo $zoneDetecteeAffichage ? '(' . number_format((float) $zoneDetecteeAffichage['prix_livraison'], 2, ',', ' ') . ' DH)' : ''; ?></span>
                            </div>
                        </div>
                        <button type="button" id="geoRetry" class="btn btn-outline btn-sm" style="margin-top:10px; display:<?php echo $zoneDetecteeAffichage ? 'none' : 'inline-flex'; ?>;"
                            data-i18n="commander.reessayer">Réessayer la localisation</button>
                        <p class="geo-hint" style="margin:8px 0 0; font-size:0.78rem; color: var(--text-muted);"
                            data-i18n="commander.localisationInfo">La zone de livraison est déterminée automatiquement à partir de votre position. Elle est en lecture seule.</p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="societe_nom" data-i18n="commander.societe">Société</label>
                    <input type="text" id="societe_nom" name="societe_nom"
                           value="<?php echo htmlspecialchars($societeDefaut); ?>"
                           data-i18n-placeholder="commander.societePlaceholder"
                           placeholder="Votre société (ex : Personnel, Entreprise X…)" required>
                </div>

            </div>

            <div class="form-group" style="margin-top: 16px;">
                <label for="commentaire" data-i18n="commander.commentaire">Commentaire</label>
                <textarea id="commentaire" name="commentaire" rows="4"
                          style="width: 100%;"><?php echo htmlspecialchars($_POST['commentaire'] ?? ''); ?></textarea>
            </div>

            <div style="margin-top: 24px; padding: 16px; background: var(--gold-light); border-radius: 10px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span data-i18n="commander.sousTotal">Sous-total plats</span>
                    <span id="lblSousTotal"><?php echo number_format((float) $total, 2, ',', ' '); ?> DH</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span data-i18n="commander.fraisLivraison">Frais de livraison</span>
                    <span id="lblFraisLivraison"><?php echo number_format((float) $fraisLivraison, 2, ',', ' '); ?> DH</span>
                </div>
                <?php if ($remiseMontant > 0): ?>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; color: #27ae60;">
                    <span data-i18n="commander.remise" data-i18n-params='{"pourcent": <?php echo REMISE_SEMAINE_POURCENT; ?>}'>Remise semaine complète (<?php echo REMISE_SEMAINE_POURCENT; ?>%)</span>
                    <span id="lblRemise">-<?php echo number_format($remiseMontant, 2, ',', ' '); ?> DH</span>
                </div>
                <?php endif; ?>
                <hr style="border: none; border-top: 1px solid var(--border); margin: 8px 0;">
                <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.15rem;">
                    <span data-i18n="commander.totalPayer">Total à payer</span>
                    <span style="color: var(--gold-dark);" id="lblTotalPayer">
                        <?php echo number_format((float) $totalPayer, 2, ',', ' '); ?> DH
                    </span>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="commander" class="btn btn-gold" id="btnValider" data-i18n="commander.valider">Valider la commande</button>
                <a href="<?php echo BASE_URL; ?>/index.php?route=client" class="btn btn-outline" data-i18n="common.retourMenu">Retour au menu</a>
            </div>
        </form>
    </div>

</div>

<script>
(function () {
    var zones = <?php echo $zonesJson; ?>;
    var form = document.getElementById('formCommande');
    var btnValider = document.getElementById('btnValider');
    var inputLat = document.getElementById('lat');
    var inputLng = document.getElementById('lng');
    var geoStatus = document.getElementById('geoStatus');
    var geoInitialMsg = document.getElementById('geoInitialMsg');
    var geoErrorMsg = document.getElementById('geoErrorMsg');
    var geoNoZoneMsg = document.getElementById('geoNoZoneMsg');
    var geoZoneInfos = document.getElementById('geoZoneInfos');
    var geoRetry = document.getElementById('geoRetry');
    var geoZoneNom = document.getElementById('geoZoneNom');
    var geoZonePrix = document.getElementById('geoZonePrix');
    var sousTotal = <?php echo (float) $total; ?>;
    var remise = <?php echo (float) $remiseMontant; ?>;

    var zoneValide = false;
    var detectionEnCours = false;

    function fmt(n) {
        return n.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function majTotaux(frais) {
        document.getElementById('lblFraisLivraison').textContent = fmt(frais) + ' DH';
        var totalFinal = sousTotal + frais - remise;
        document.getElementById('lblTotalPayer').textContent = fmt(totalFinal) + ' DH';
    }

    function detecterZone() {
        if (detectionEnCours) { return; }
        if (!('geolocation' in navigator)) { afficherErreur(); return; }

        detectionEnCours = true;
        if (geoInitialMsg) { geoInitialMsg.style.display = 'block'; }
        if (geoErrorMsg) { geoErrorMsg.style.display = 'none'; }
        if (geoNoZoneMsg) { geoNoZoneMsg.style.display = 'none'; }
        if (geoZoneInfos) { geoZoneInfos.style.display = 'none'; }
        if (geoRetry) { geoRetry.style.display = 'none'; }
        btnValider.disabled = true;

        navigator.geolocation.getCurrentPosition(
            function (position) {
                detectionEnCours = false;
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;
                inputLat.value = lat;
                inputLng.value = lng;

                var meilleure = null;
                var distMin = Infinity;
                zones.forEach(function (z) {
                    if (!z.lat && !z.lng) { return; }
                    var d = distanceKm(lat, lng, z.lat, z.lng);
                    if (d <= z.rayon && d < distMin) {
                        distMin = d;
                        meilleure = z;
                    }
                });

                if (!meilleure) { afficherAucuneZone(); return; }
                afficherZone(meilleure);
            },
            function () {
                detectionEnCours = false;
                afficherErreur();
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 30000 }
        );
    }

    function distanceKm(lat1, lng1, lat2, lng2) {
        var R = 6371;
        var dLat = (lat2 - lat1) * Math.PI / 180;
        var dLng = (lng2 - lng1) * Math.PI / 180;
        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLng / 2) * Math.sin(dLng / 2);
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    function afficherZone(z) {
        zoneValide = true;
        if (geoInitialMsg) { geoInitialMsg.style.display = 'none'; }
        if (geoErrorMsg) { geoErrorMsg.style.display = 'none'; }
        if (geoNoZoneMsg) { geoNoZoneMsg.style.display = 'none'; }
        if (geoRetry) { geoRetry.style.display = 'none'; }
        if (geoZoneInfos) { geoZoneInfos.style.display = 'block'; }
        if (geoZoneNom) { geoZoneNom.textContent = z.nom; }
        if (geoZonePrix) { geoZonePrix.textContent = '(' + fmt(z.prix) + ' DH)'; }
        document.getElementById('zone_id').value = z.id;
        majTotaux(z.prix);
        btnValider.disabled = false;
    }

    function afficherErreur() {
        zoneValide = false;
        if (geoInitialMsg) { geoInitialMsg.style.display = 'none'; }
        if (geoErrorMsg) { geoErrorMsg.style.display = 'block'; }
        if (geoNoZoneMsg) { geoNoZoneMsg.style.display = 'none'; }
        if (geoZoneInfos) { geoZoneInfos.style.display = 'none'; }
        if (geoRetry) { geoRetry.style.display = 'inline-flex'; }
        btnValider.disabled = true;
    }

    function afficherAucuneZone() {
        zoneValide = false;
        if (geoInitialMsg) { geoInitialMsg.style.display = 'none'; }
        if (geoErrorMsg) { geoErrorMsg.style.display = 'none'; }
        if (geoNoZoneMsg) { geoNoZoneMsg.style.display = 'block'; }
        if (geoZoneInfos) { geoZoneInfos.style.display = 'none'; }
        if (geoRetry) { geoRetry.style.display = 'inline-flex'; }
        btnValider.disabled = true;
    }

    if (geoRetry) { geoRetry.addEventListener('click', detecterZone); }

    form.addEventListener('submit', function (e) {
        if (!zoneValide || !inputLat.value || !inputLng.value) {
            e.preventDefault();
            btnValider.blur();
            if (detectionEnCours) {
                if (geoInitialMsg) { geoInitialMsg.style.display = 'block'; }
            } else {
                afficherErreur();
            }
            return;
        }
    });

    // Au chargement : si aucune zone encore détectée (premier affichage), on lance la détection.
    if (!document.getElementById('zone_id').value) {
        detecterZone();
    } else {
        zoneValide = true;
        btnValider.disabled = false;
    }
})();
</script>

<?php require ROOT_PATH . '/assets/inc/client_footer.php'; ?>
