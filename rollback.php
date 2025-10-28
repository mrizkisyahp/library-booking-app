<?php
declare(strict_types=1);

$app = require_once __DIR__ . '/Bootstrap/App.php';
$app->db->rollbackLastMigration();
