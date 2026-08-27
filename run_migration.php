<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/modele/Database.php';
$pdo = Database::getConnection();
$sql = file_get_contents(__DIR__ . '/database/migrations/20260825_000001_add_societes_to_orders.sql');
$pdo->exec($sql);
echo "Migration OK\n";
