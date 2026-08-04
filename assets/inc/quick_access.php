<?php
/**
 * Composant partagé : grille de cartes "Accès rapide" pour les dashboards.
 * Variable attendue avant l'include :
 *   $quickAccessItems : array de ['icon' => string, 'label' => string, 'route' => string]
 */
$quickAccessItems = $quickAccessItems ?? [];
?>
<?php if (!empty($quickAccessItems)): ?>
<div class="quick-access-grid">
    <?php foreach ($quickAccessItems as $item): ?>
        <a class="quick-access-card" href="<?php echo BASE_URL; ?>/index.php?route=<?php echo htmlspecialchars($item['route']); ?>">
            <span class="quick-access-icon">
                <?php
                $icone = $item['icon'] ?? '';
                if (str_contains($icone, '<')) {
                    echo $icone;
                } else {
                    echo '<i data-lucide="' . htmlspecialchars($icone) . '" aria-hidden="true"></i>';
                }
                ?>
            </span>
            <span class="quick-access-label"><?php echo htmlspecialchars($item['label']); ?></span>
        </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>
