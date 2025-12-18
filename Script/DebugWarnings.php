<?php

declare(strict_types=1);

define('ROOT_DIR', dirname(__DIR__));

require_once ROOT_DIR . '/vendor/autoload.php';
$app = require_once ROOT_DIR . '/Bootstrap/App.php';

use App\Core\App;

echo "=== Verifying Warning System ===\n\n";

// 1. Check peringatan_mhs table
echo "1. Recent Warnings (peringatan_mhs):\n";
$stmt = App::$app->db->pdo->query('SELECT pm.*, u.nama as user_nama, ps.nama_peringatan 
    FROM peringatan_mhs pm 
    LEFT JOIN users u ON pm.id_akun = u.id_user 
    LEFT JOIN peringatan_suspensi ps ON pm.id_peringatan = ps.id_peringatan 
    ORDER BY pm.id_peringatan_mhs DESC LIMIT 5');
$warnings = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($warnings as $w) {
    echo "  - ID: {$w['id_peringatan_mhs']}, User: {$w['user_nama']}, Type: {$w['nama_peringatan']}, Date: {$w['tgl_peringatan']}\n";
}

// 2. Check users.peringatan column
echo "\n2. Users peringatan column:\n";
$stmt = App::$app->db->pdo->query('SELECT id_user, nama, peringatan, status FROM users ORDER BY peringatan DESC LIMIT 5');
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $u) {
    echo "  - ID: {$u['id_user']}, Name: {$u['nama']}, Peringatan: {$u['peringatan']}, Status: {$u['status']}\n";
}

echo "\n=== Done ===\n";
