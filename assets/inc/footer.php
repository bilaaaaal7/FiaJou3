<?php
/**
 * Pied de page commun (fermeture du <body>/<html> + scripts additionnels).
 * Variable attendue (optionnelle) avant l'include :
 *   $extraJs : array - fichiers JS additionnels dans assets/js/
 */

$extraJs = $extraJs ?? [];
?>
<?php if (est_connecte()): ?>
    </div>
</div>
<?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php foreach ($extraJs as $js): ?>
    <script src="<?php echo BASE_URL; ?>/assets/js/<?php echo htmlspecialchars($js); ?>"></script>
    <?php endforeach; ?>
</body>
</html>
