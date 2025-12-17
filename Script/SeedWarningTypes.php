<?php

declare(strict_types=1);

define('ROOT_DIR', dirname(__DIR__));

require_once ROOT_DIR . '/vendor/autoload.php';
$app = require_once ROOT_DIR . '/Bootstrap/App.php';

use App\Core\App;

echo "Seeding warning types (peringatan_suspensi)...\n";

$warningTypes = [
    'No Show',
    'PIC Batalkan Booking Verified',
    'Melanggar Aturan Penggunaan Ruangan',
    'Merusak Fasilitas'
];

$db = App::$app->db;

foreach ($warningTypes as $type) {
    // Check if already exists
    $stmt = $db->prepare("SELECT id_peringatan FROM peringatan_suspensi WHERE nama_peringatan = :nama");
    $stmt->execute([':nama' => $type]);

    if (!$stmt->fetch()) {
        $stmt = $db->prepare("INSERT INTO peringatan_suspensi (nama_peringatan) VALUES (:nama)");
        $stmt->execute([':nama' => $type]);
        echo "  Added: $type\n";
    } else {
        echo "  Exists: $type\n";
    }
}

echo "\nDone! Checking all warning types:\n";
$stmt = $db->pdo->query("SELECT * FROM peringatan_suspensi");
$types = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($types);
