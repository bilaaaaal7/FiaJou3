<?php
/**
 * Layout partagé pour les espaces Admin / Cuisinier / Livreur.
 * Variables attendues avant l'include :
 *   $pageTitle   (string) titre de la page (onglet + h1)
 *   $activePage  (string) clé du lien actif dans $navItems
 *   $navItems    (array)  [['key'=>'dashboard','label'=>'Tableau de bord','href'=>'index.php','icon'=>'📊'], ...]
 *   $roleLabel   (string) "Administrateur" | "Cuisinier" | "Livreur"
 */
$roleLabel = $roleLabel ?? 'Espace';
$userName  = $_SESSION['prenom'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($pageTitle); ?> - FiaJou3</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon-16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/images/favicon-180.png">
    <link rel="shortcut icon" href="../assets/images/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="app-shell">

    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <img src="../assets/images/logo.png" alt="FiaJou3">
            <span>FIAJOU3</span>
        </div>
        <span class="role-badge"><?php echo htmlspecialchars($roleLabel); ?></span>

        <nav>
            <?php foreach ($navItems as $item) { ?>
                <a href="<?php echo $item['href']; ?>"
                   class="<?php echo ($activePage === $item['key']) ? 'active' : ''; ?>">
                    <span><?php echo $item['icon']; ?></span>
                    <span><?php echo htmlspecialchars($item['label']); ?></span>
                </a>
            <?php } ?>

            <div class="logout-link">
                <a href="../auth/logout.php">
                    <span>🚪</span>
                    <span>Déconnexion</span>
                </a>
            </div>
        </nav>
    </aside>

    <main class="main">
        <div class="topbar">
            <div style="display:flex; align-items:center; gap:12px;">
                <button class="menu-toggle" id="menuToggle" type="button">☰</button>
                <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
            </div>
            <div class="user-chip">👤 <?php echo htmlspecialchars($userName); ?></div>
        </div>
