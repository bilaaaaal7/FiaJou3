<?php
exiger_role(ROLE_ADMIN);

require_once ROOT_PATH . '/modele/MenuSemaineModele.php';
require_once ROOT_PATH . '/modele/PlatModele.php';
require_once ROOT_PATH . '/modele/CategorieModele.php';

$menuModele = new MenuSemaineModele();
$platModele = new PlatModele();
$categorieModele = new CategorieModele();

/* ================= Semaine consultée =================
   La page est centrée sur une semaine (lundi → dimanche) :
   navigation semaine précédente / suivante, création et duplication. */
$semaineRef = $_GET['semaine'] ?? date('Y-m-d');
$lundi = MenuSemaineModele::debutSemaine($semaineRef);
$dimanche = MenuSemaineModele::finSemaine($lundi);

if (isset($_POST['creer'])) {
    $nom = trim($_POST['nom']);
    $weekStart = trim($_POST['week_start'] ?? '');
    $weekEnd = trim($_POST['week_end'] ?? '');

    if (empty($nom)) {
        header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine&erreur=nom');
        exit;
    }
    if ($weekStart !== '' && $weekEnd !== '' && $weekStart > $weekEnd) {
        header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine&erreur=dates');
        exit;
    }
    if ($weekStart !== '' && $weekEnd !== '') {
        $stmt = $menuModele->checkerChevauchement($weekStart, $weekEnd);
        if ($stmt) {
            header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine&erreur=chevauchement');
            exit;
        }
    }

    $menuModele->creer($nom, $weekStart !== '' ? $weekStart : null, $weekEnd !== '' ? $weekEnd : null);
    journaliser_audit('menu_semaine.creer', 'nom="' . $nom . '" semaine=' . ($weekStart ?: '-') . ' -> ' . ($weekEnd ?: '-'));
    if ($weekStart !== '') {
        header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine&semaine=' . urlencode($weekStart));
    } else {
        header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine');
    }
    exit;
}

/* Créer le menu d'une semaine donnée ("+ Nouvelle semaine" passe la semaine
   suivante ; l'état vide passe la semaine consultée). Si cette semaine possède
   déjà un menu, on s'y rend simplement. */
if (isset($_GET['creer_semaine'])) {
    $cibleLundi = MenuSemaineModele::debutSemaine(trim($_GET['creer_semaine']) ?: null);
    $cibleDimanche = MenuSemaineModele::finSemaine($cibleLundi);

    $existant = $menuModele->getParSemaine($cibleLundi, $cibleDimanche);
    if ($existant) {
        header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine&semaine=' . $cibleLundi);
        exit;
    }
    $nom = 'Menu ' . MenuSemaineModele::libelleSemaine($cibleLundi, $cibleDimanche);
    $menuModele->creer($nom, $cibleLundi, $cibleDimanche);
    journaliser_audit('menu_semaine.creer_semaine', 'semaine=' . $cibleLundi . ' -> ' . $cibleDimanche);
    header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine&semaine=' . $cibleLundi);
    exit;
}

/* Dupliquer la semaine consultée vers la suivante. */
if (isset($_GET['dupliquer'])) {
    $sourceId = (int) $_GET['dupliquer'];
    $baseLundi = MenuSemaineModele::debutSemaine(trim($_GET['semaine'] ?? $lundi) ?: null);
    $cibleLundi = date('Y-m-d', strtotime($baseLundi . ' +7 days'));
    $cibleDimanche = MenuSemaineModele::finSemaine($cibleLundi);

    $existant = $menuModele->getParSemaine($cibleLundi, $cibleDimanche);
    if ($existant) {
        header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine&semaine=' . $baseLundi . '&erreur=duplicat_semaine');
        exit;
    }
    $nom = 'Menu ' . MenuSemaineModele::libelleSemaine($cibleLundi, $cibleDimanche);
    $menuModele->dupliquer($sourceId, $nom, $cibleLundi, $cibleDimanche);
    journaliser_audit('menu_semaine.dupliquer', 'source_id=' . $sourceId . ' semaine=' . $cibleLundi . ' -> ' . $cibleDimanche);
    header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine&semaine=' . $cibleLundi);
    exit;
}

if (isset($_GET['publier'])) {
    $menuModele->mettreAJourStatut((int) $_GET['publier'], 'publie');
    journaliser_audit('menu_semaine.publier', 'id=' . (int) $_GET['publier']);
    header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine&semaine=' . $lundi);
    exit;
}

if (isset($_GET['archiver'])) {
    $menuModele->mettreAJourStatut((int) $_GET['archiver'], 'archive');
    journaliser_audit('menu_semaine.archiver', 'id=' . (int) $_GET['archiver']);
    header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine&semaine=' . $lundi);
    exit;
}

if (isset($_GET['supprimer'])) {
    $menuModele->supprimer((int) $_GET['supprimer']);
    journaliser_audit('menu_semaine.supprimer', 'id=' . (int) $_GET['supprimer']);
    header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine&semaine=' . $lundi);
    exit;
}

/* Ajout d'un plat : un même produit peut apparaître plusieurs fois dans la
   même semaine (plusieurs jours) et dans toutes les semaines. */
if (isset($_POST['ajouter_item'])) {
    $menuId = (int) $_POST['menu_id'];
    $productId = (int) $_POST['product_id'];
    $jour = $_POST['jour'];

    $menuModele->ajouterItem($menuId, $productId, $jour);
    journaliser_audit('menu_semaine.ajouter_plat', 'menu_id=' . $menuId . ' product_id=' . $productId . ' jour="' . $jour . '"');
    $menuRedir = $menuModele->getParId($menuId);
    $semaineRedir = ($menuRedir && $menuRedir['week_start']) ? $menuRedir['week_start'] : $lundi;
    header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine&semaine=' . urlencode($semaineRedir));
    exit;
}

if (isset($_GET['deplacer_item'])) {
    $menuId = (int) ($_GET['menu_id'] ?? 0);
    $decalage = (($_GET['direction'] ?? '') === 'descendre') ? 1 : -1;
    $menuModele->deplacerItem((int) $_GET['deplacer_item'], $decalage);
    journaliser_audit('menu_semaine.deplacer_plat', 'item_id=' . (int) $_GET['deplacer_item'] . ' direction=' . ($decalage > 0 ? 'descendre' : 'monter') . ' menu_id=' . $menuId);
    $menuRedir = $menuModele->getParId($menuId);
    $semaineRedir = ($menuRedir && $menuRedir['week_start']) ? $menuRedir['week_start'] : $lundi;
    header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine&semaine=' . urlencode($semaineRedir));
    exit;
}

if (isset($_GET['supprimer_item'])) {
    $menuId = (int) ($_GET['menu_id'] ?? 0);
    $menuModele->supprimerItem((int) $_GET['supprimer_item']);
    journaliser_audit('menu_semaine.supprimer_plat', 'item_id=' . (int) $_GET['supprimer_item'] . ' menu_id=' . ($menuId ?: '-'));
    $semaineRedir = $lundi;
    if ($menuId) {
        $menuRedir = $menuModele->getParId($menuId);
        if ($menuRedir && $menuRedir['week_start']) {
            $semaineRedir = $menuRedir['week_start'];
        }
    }
    header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine&semaine=' . urlencode($semaineRedir));
    exit;
}

/* Modification d'une entrée du menu (bouton "Modifier" → modal).
   En AJAX la réponse est du JSON ; sinon repli sur redirection. */
if (isset($_POST['modifier_item'])) {
    $itemId = (int) ($_POST['item_id'] ?? 0);
    $menuId = (int) ($_POST['menu_id'] ?? 0);
    $estAjax = (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest') || isset($_GET['ajax']);

    $item = $menuModele->getItemParId($itemId);
    $ok = false;
    $message = '';
    $itemData = null;

    if (!$item || (int) $item['weekly_menu_id'] !== $menuId) {
        $message = 'Entrée de menu introuvable.';
    } elseif (empty($_POST['product_id'])) {
        $message = 'Veuillez choisir un plat.';
    } else {
        $menuModele->modifierItem($itemId, $_POST);
        $ok = true;
        journaliser_audit('menu_semaine.modifier_plat', 'item_id=' . $itemId . ' product_id=' . (int) $_POST['product_id'] . ' menu_id=' . $menuId);
        foreach ($menuModele->getItems($menuId) as $it) {
            if ((int) $it['id'] === $itemId) {
                $itemData = $it;
                break;
            }
        }
    }

    if ($estAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => $ok, 'message' => $message, 'item' => $itemData]);
        exit;
    }
    header('Location: ' . BASE_URL . '/index.php?route=admin/menu-semaine&semaine=' . urlencode($lundi) . ($ok ? '' : '&erreur=modification'));
    exit;
}

/* Rétrocompatibilité : "voir=ID" (menus hérités sans période). */
$menuVoir = null;
if (isset($_GET['voir'])) {
    $menuVoir = $menuModele->getParId((int) $_GET['voir']);
    if ($menuVoir && $menuVoir['week_start']) {
        $lundi = $menuVoir['week_start'];
        $dimanche = $menuVoir['week_end'];
    }
}

$menus = $menuModele->getTous();
$plats = $platModele->getTous();
$categories = $categorieModele->getToutes();

$menuActuel = $menuModele->getParSemaine($lundi, $dimanche);
if (!$menuActuel && $menuVoir) {
    $menuActuel = $menuVoir;
}
$itemsParJour = [];
if ($menuActuel) {
    $itemsParJour = $menuModele->getItemsParJour((int) $menuActuel['id']);
}

$semainePrecedente = date('Y-m-d', strtotime($lundi . ' -7 days'));
$semaineSuivante = date('Y-m-d', strtotime($lundi . ' +7 days'));
$estSemaineCourante = ($lundi === MenuSemaineModele::debutSemaine());
$libelleSemaine = MenuSemaineModele::libelleSemaine($lundi, $dimanche);

require ROOT_PATH . '/vue/admin/menu_semaine.php';
