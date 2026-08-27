<?php
$pageTitle = "Paiement - " . APP_NAME;
$extraCss = ['admin.css', 'profile-menu.css', 'client-public.css'];
$bodyClass = 'client-public-layout';
$i18nPage = 'abonnement';
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/client_navbar.php';

$nomDefaut = isset($_POST['nom_carte']) ? htmlspecialchars(trim((string) $_POST['nom_carte'])) : '';
$numeroDefaut = isset($_POST['numero_carte']) ? htmlspecialchars(trim((string) $_POST['numero_carte'])) : '';
$expirationDefaut = isset($_POST['expiration']) ? htmlspecialchars(trim((string) $_POST['expiration'])) : '';
$cvvDefaut = isset($_POST['cvv']) ? htmlspecialchars(trim((string) $_POST['cvv'])) : '';
$nomUser = trim(($_SESSION['prenom'] ?? '') . ' ' . ($_SESSION['nom'] ?? ''));
?>

<div style="max-width: 800px; margin: 0 auto;">

    <?php require ROOT_PATH . '/assets/inc/back_home.php'; ?>

    <div class="topbar">
        <h1 data-i18n="abonnement.paiementTitre">Paiement de l'abonnement</h1>
        <p class="profil-subtitle" data-i18n="abonnement.paiementSousTitre">Saisissez vos informations bancaires pour souscrire à l'offre Premium.</p>
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

        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--border-soft);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="font-size: 1.6rem; color: var(--gold-dark); display:flex; align-items:center;">
                    <i data-lucide="crown" aria-hidden="true" style="width: 30px; height: 30px;"></i>
                </div>
                <div>
                    <div style="font-weight: 700; color: var(--gold-dark); font-size: 1.05rem;" data-i18n="abonnement.offreTitre">Abonnement Premium</div>
                    <div style="color: var(--text-muted); font-size: 0.85rem;" data-i18n="abonnement.facturation">Facturation mensuelle</div>
                </div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--gold-dark);">
                    <?php echo number_format($prix, 2, ',', ' '); ?> DH
                    <span style="font-size: 0.85rem; font-weight: 400; color: var(--text-muted);">/mois</span>
                </div>
            </div>
        </div>

        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=client/abonnement/paiement" id="formPaiement" novalidate>

            <div class="form-group">
                <label for="nom_carte" data-i18n="abonnement.nomCarte">Nom sur la carte</label>
                <input type="text" id="nom_carte" name="nom_carte" value="<?php echo $nomDefaut !== '' ? $nomDefaut : htmlspecialchars($nomUser); ?>"
                       placeholder="Ex : Jean Dupont" autocomplete="cc-name" required>
                <span class="field-error" id="err-nom_carte"></span>
            </div>

            <div class="form-group" style="margin-top: 16px;">
                <label for="numero_carte" data-i18n="abonnement.numeroCarte">Numéro de carte</label>
                <input type="text" id="numero_carte" name="numero_carte" value="<?php echo $numeroDefaut; ?>"
                       inputmode="numeric" placeholder="1234 5678 9012 3456" autocomplete="cc-number" required>
                <span class="field-error" id="err-numero_carte"></span>
            </div>

            <div class="form-grid" style="margin-top: 16px;">
                <div class="form-group">
                    <label for="expiration" data-i18n="abonnement.expiration">Date d'expiration (MM/AA)</label>
                    <input type="text" id="expiration" name="expiration" value="<?php echo $expirationDefaut; ?>"
                           inputmode="numeric" placeholder="MM/AA" autocomplete="cc-exp" maxlength="5" required>
                    <span class="field-error" id="err-expiration"></span>
                </div>
                <div class="form-group">
                    <label for="cvv" data-i18n="abonnement.cvv">CVV</label>
                    <input type="text" id="cvv" name="cvv" value="<?php echo $cvvDefaut; ?>"
                           inputmode="numeric" placeholder="123" autocomplete="cc-csc" maxlength="4" required>
                    <span class="field-error" id="err-cvv"></span>
                </div>
            </div>

            <div style="margin-top: 18px; padding: 12px 14px; background: var(--gold-light); border-radius: 10px; font-size: 0.82rem; color: var(--gold-dark); display:flex; gap:10px; align-items:flex-start;">
                <i data-lucide="shield-check" aria-hidden="true" style="width:18px; height:18px; flex-shrink:0; margin-top:1px;"></i>
                <span data-i18n="abonnement.secuNote">
                    Mode test (sandbox) : aucune carte réelle n'est débitée et aucune donnée bancaire sensible n'est enregistrée. Vos informations sont validées et masquées.
                </span>
            </div>

            <div class="form-actions">
                <button type="submit" name="paiement" class="btn btn-gold" style="padding: 12px 34px; font-size: 1rem;" data-i18n="abonnement.validerPaiement">Payer 500,00 DH</button>
                <a href="<?php echo BASE_URL; ?>/index.php?route=client/abonnement" class="btn btn-outline" data-i18n="common.annuler">Annuler</a>
            </div>

        </form>
    </div>

</div>

<script>
(function () {
    var form = document.getElementById('formPaiement');
    if (!form) return;

    var nom      = document.getElementById('nom_carte');
    var numero   = document.getElementById('numero_carte');
    var expire   = document.getElementById('expiration');
    var cvv      = document.getElementById('cvv');
    var errNom   = document.getElementById('err-nom_carte');
    var errNum   = document.getElementById('err-numero_carte');
    var errExp   = document.getElementById('err-expiration');
    var errCvv   = document.getElementById('err-cvv');

    function creerErreur() {
        return function (champ, errEl, msg) {
            errEl.textContent = msg;
            champ.classList.add('input-error');
        };
    }
    var afficherErreur = creerErreur();

    function effacerErreurs() {
        [errNom, errNum, errExp, errCvv].forEach(function (e) { e.textContent = ''; });
        [nom, numero, expire, cvv].forEach(function (f) { f.classList.remove('input-error'); });
    }

    function luhn(num) {
        var somme = 0, inverser = false;
        for (var i = num.length - 1; i >= 0; i--) {
            var n = parseInt(num.charAt(i), 10);
            if (inverser) { n *= 2; if (n > 9) { n -= 9; } }
            somme += n;
            inverser = !inverser;
        }
        return somme % 10 === 0;
    }

    function formatNumero(v) {
        return v.replace(/\D/g, '').replace(/(.{4})/g, '$1 ').trim();
    }

    function formatExpiration(v) {
        v = v.replace(/\D/g, '').slice(0, 4);
        if (v.length >= 3) { return v.slice(0, 2) + '/' + v.slice(2); }
        return v;
    }

    numero.addEventListener('input', function () {
        numero.value = formatNumero(numero.value);
        errNum.textContent = '';
        numero.classList.remove('input-error');
    });

    expire.addEventListener('input', function () {
        expire.value = formatExpiration(expire.value);
        errExp.textContent = '';
        expire.classList.remove('input-error');
    });

    cvv.addEventListener('input', function () {
        cvv.value = cvv.value.replace(/\D/g, '').slice(0, 4);
        errCvv.textContent = '';
        cvv.classList.remove('input-error');
    });

    nom.addEventListener('input', function () {
        errNom.textContent = '';
        nom.classList.remove('input-error');
    });

    form.addEventListener('submit', function (e) {
        effacerErreurs();
        var valide = true;

        if (!nom.value.trim()) {
            afficherErreur(nom, errNom, 'Veuillez saisir le nom sur la carte.');
            valide = false;
        }

        var numSansEspaces = numero.value.replace(/\s+/g, '');
        if (!numSansEspaces) {
            afficherErreur(numero, errNum, 'Veuillez saisir le numéro de carte.');
            valide = false;
        } else if (!/^\d{13,19}$/.test(numSansEspaces)) {
            afficherErreur(numero, errNum, 'Le numéro de carte est invalide.');
            valide = false;
        } else if (!luhn(numSansEspaces)) {
            afficherErreur(numero, errNum, 'Le numéro de carte est invalide.');
            valide = false;
        }

        var expVal = expire.value.trim();
        var execExp = /^(0[1-9]|1[0-2])\/(\d{2})$/.exec(expVal);
        if (!expVal) {
            afficherErreur(expire, errExp, 'Veuillez saisir la date d\'expiration (MM/AA).');
            valide = false;
        } else if (!execExp) {
            afficherErreur(expire, errExp, 'Format invalide. Utilisez MM/AA.');
            valide = false;
        } else {
            var annee = 2000 + parseInt(execExp[2], 10);
            var mois = parseInt(execExp[1], 10);
            var finMois = new Date(annee, mois, 0, 23, 59, 59);
            if (finMois < new Date()) {
                afficherErreur(expire, errExp, 'La carte est expirée.');
                valide = false;
            }
        }

        if (!cvv.value.trim()) {
            afficherErreur(cvv, errCvv, 'Veuillez saisir le CVV.');
            valide = false;
        } else if (!/^\d{3,4}$/.test(cvv.value)) {
            afficherErreur(cvv, errCvv, 'Le CVV doit contenir 3 à 4 chiffres.');
            valide = false;
        }

        if (!valide) {
            e.preventDefault();
        }
    });
})();
</script>

<?php require ROOT_PATH . '/assets/inc/client_footer.php'; ?>
