<?php
/**
 * Bouton de basculement de thème (Light/Dark), réutilisable dans toutes
 * les interfaces. La variable optionnelle $themeToggleClass permet
 * d'ajouter des modificateurs CSS (placement sidebar, fixe, etc.).
 *
 * Le comportement est géré par assets/js/theme.js :
 *   - persistance dans localStorage ;
 *   - l'attribut data-theme posé sur <html> pilote en pur CSS la visibilité
 *     des icônes soleil/lune et la variante du logo.
 */
$themeToggleClass = $themeToggleClass ?? '';
?>
<button type="button"
        class="theme-toggle <?php echo htmlspecialchars($themeToggleClass); ?>"
        data-theme-toggle
        aria-pressed="false"
        title="Basculer le thème (clair / sombre)">
    <i data-lucide="sun" data-theme-icon="sun" aria-hidden="true"></i>
    <i data-lucide="moon" data-theme-icon="moon" aria-hidden="true"></i>
</button>
