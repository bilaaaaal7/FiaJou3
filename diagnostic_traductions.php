<?php
/**
 * Diagnostic (CLI uniquement, à SUPPRIMER après usage — voir bas de fichier).
 *
 * Contrairement à la version précédente, ce script NE SUPPOSE AUCUN nom de
 * table. Il interroge INFORMATION_SCHEMA sur la base réellement configurée
 * dans config/database.php (DB_NAME), détecte lui-même les tables qui
 * ressemblent à des produits/catégories/menu de la semaine, liste leurs
 * vraies colonnes, puis — uniquement si une colonne contenant "ar" existe
 * réellement — compte les lignes où elle est vide.
 *
 * Usage (depuis la racine du projet, XAMPP ou serveur) :
 *   php database/diagnostics/diagnostic_traductions.php
 *
 * Ne modifie rien. Lecture seule.
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('Ce script est réservé à la ligne de commande (CLI). À supprimer après usage.');
}

define('ROOT_PATH', dirname(__DIR__, 2));
require_once ROOT_PATH . '/config/database.php';

echo "Connexion : host=" . DB_HOST . " db=" . DB_NAME . " user=" . DB_USER . "\n\n";

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit("ÉCHEC DE CONNEXION à la base '" . DB_NAME . "' : " . $e->getMessage() . "\n"
        . "=> Vérifie DB_HOST/DB_NAME/DB_USER/DB_PASS dans config/database.php,\n"
        . "   et confirme que cette base existe bien dans ton serveur MySQL\n"
        . "   (ex: `SHOW DATABASES;` dans phpMyAdmin ou en CLI mysql).\n");
}

// 1) Liste RÉELLE de toutes les tables de la base configurée.
$stmt = $pdo->prepare(
    "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES
     WHERE TABLE_SCHEMA = ? ORDER BY TABLE_NAME"
);
$stmt->execute([DB_NAME]);
$toutesLesTables = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'TABLE_NAME');

if (!$toutesLesTables) {
    exit("Aucune table trouvée dans la base '" . DB_NAME . "'.\n"
        . "=> Cette base est vide ou différente de celle réellement utilisée par le site.\n"
        . "   Vérifie dans phpMyAdmin quelle base contient vraiment tes données\n"
        . "   (regarde en haut de n'importe quelle page admin qui affiche des plats).\n");
}

echo "=== TOUTES LES TABLES de la base '" . DB_NAME . "' (" . count($toutesLesTables) . ") ===\n";
foreach ($toutesLesTables as $t) {
    echo "  - {$t}\n";
}
echo "\n";

// 2) Repère les tables qui ressemblent à produits / catégories / menu semaine,
//    sans jamais présumer un nom exact.
function correspond(string $table, array $motsClefs): bool
{
    $t = mb_strtolower($table);
    foreach ($motsClefs as $mot) {
        if (str_contains($t, $mot)) {
            return true;
        }
    }
    return false;
}

$candidatsProduits   = array_filter($toutesLesTables, fn ($t) => correspond($t, ['plat', 'product', 'produit', 'item', 'dish']));
$candidatsCategories = array_filter($toutesLesTables, fn ($t) => correspond($t, ['categor', 'category']));
$candidatsMenu       = array_filter($toutesLesTables, fn ($t) => correspond($t, ['menu', 'weekly']));

echo "=== Candidats détectés par nom ===\n";
echo "Produits/plats   : " . (implode(', ', $candidatsProduits) ?: '(aucun trouvé par ce filtre)') . "\n";
echo "Catégories       : " . (implode(', ', $candidatsCategories) ?: '(aucun trouvé par ce filtre)') . "\n";
echo "Menu de semaine  : " . (implode(', ', $candidatsMenu) ?: '(aucun trouvé par ce filtre)') . "\n\n";

// 3) Pour chaque table candidate, affiche ses vraies colonnes et — si des
//    colonnes de traduction existent réellement — le compte de lignes vides.
function analyserTable(PDO $pdo, string $dbName, string $table): void
{
    echo "--- TABLE `{$table}` ---\n";

    $stmt = $pdo->prepare(
        "SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? ORDER BY ORDINAL_POSITION"
    );
    $stmt->execute([$dbName, $table]);
    $colonnes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $nomsColonnes = array_column($colonnes, 'COLUMN_NAME');
    echo "Colonnes réelles : " . implode(', ', $nomsColonnes) . "\n";

    $colonnesAr = array_values(array_filter($nomsColonnes, fn ($c) => str_ends_with(mb_strtolower($c), '_ar') || str_ends_with(mb_strtolower($c), 'ar')));
    $colonnesEn = array_values(array_filter($nomsColonnes, fn ($c) => str_ends_with(mb_strtolower($c), '_en') || str_ends_with(mb_strtolower($c), 'en')));

    if (!$colonnesAr && !$colonnesEn) {
        echo "Aucune colonne de traduction (_ar/_en) trouvée sur cette table.\n\n";
        return;
    }

    $nbLignes = (int) $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
    echo "Nombre total de lignes : {$nbLignes}\n";

    foreach (array_merge($colonnesAr, $colonnesEn) as $col) {
        $stmtCount = $pdo->query(
            "SELECT COUNT(*) FROM `{$table}` WHERE `{$col}` IS NULL OR `{$col}` = ''"
        );
        $manquantes = (int) $stmtCount->fetchColumn();
        echo "  Colonne `{$col}` : {$manquantes} ligne(s) vide(s) sur {$nbLignes}\n";
    }
    echo "\n";

    // Affiche un échantillon des lignes concernées (via la 1ère colonne _ar trouvée)
    if ($colonnesAr) {
        $colRef = $colonnesAr[0];
        $colNom = null;
        foreach (['nom', 'name', 'titre', 'title', 'libelle'] as $candidat) {
            if (in_array($candidat, $nomsColonnes, true)) {
                $colNom = $candidat;
                break;
            }
        }
        $selectNom = $colNom ? "`{$colNom}`" : "'(pas de colonne nom détectée)'";
        $stmtEchantillon = $pdo->query(
            "SELECT * FROM `{$table}` WHERE `{$colRef}` IS NULL OR `{$colRef}` = '' LIMIT 15"
        );
        $lignes = $stmtEchantillon->fetchAll(PDO::FETCH_ASSOC);
        if ($lignes) {
            echo "Échantillon des lignes sans `{$colRef}` (max 15) :\n";
            foreach ($lignes as $ligne) {
                $id = $ligne['id'] ?? '?';
                $nomAffiche = $colNom ? ($ligne[$colNom] ?? '') : '';
                echo "    #{$id} : {$nomAffiche}\n";
            }
            echo "\n";
        }
    }
}

foreach ($candidatsProduits as $t) {
    analyserTable($pdo, DB_NAME, $t);
}
foreach ($candidatsCategories as $t) {
    analyserTable($pdo, DB_NAME, $t);
}
foreach ($candidatsMenu as $t) {
    analyserTable($pdo, DB_NAME, $t);
}

echo "=== FIN DU DIAGNOSTIC ===\n";
echo "Colle cette sortie complète dans la conversation pour générer un SQL\n";
echo "de complétion des traductions adapté à tes VRAIES tables/colonnes.\n";
echo "Une fois le diagnostic terminé, supprime ce fichier (il n'a pas sa place\n";
echo "dans le projet final) : rm database/diagnostics/diagnostic_traductions.php\n";
