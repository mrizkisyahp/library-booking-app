<?php

define('ROOT_DIR', dirname(__DIR__));
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(ROOT_DIR);
$dotenv->load();

require_once __DIR__ . '/../Bootstrap/App.php';

$config = [
    'userClass' => \App\Models\User::class,
    'database' => [
        'host' => $_ENV['DB_HOST'],
        'port' => (int) ($_ENV['DB_PORT']),
        'name' => $_ENV['DB_NAME'],
        'user' => $_ENV['DB_USER'],
        'pass' => $_ENV['DB_PASS'],
        'charset' => $_ENV['DB_CHARSET'],
    ]
];

$App = new \App\Core\App(ROOT_DIR, $config);

$db = $App->db;
$log = $App->log;

$log->info('Running Seed.php');

try {
    $roles = [
        ['id_role' => 1, 'nama_role' => 'admin'],
        ['id_role' => 2, 'nama_role' => 'dosen'],
        ['id_role' => 3, 'nama_role' => 'mahasiswa'],
        ['id_role' => 4, 'nama_role' => 'tendik'],
    ];

    $stmt = $db->pdo->prepare("
        INSERT INTO role (id_role, nama_role)
        VALUES (:id_role, :nama_role)
        ON DUPLICATE KEY UPDATE nama_role = VALUES(nama_role), id_role = VALUES(id_role)
    ");

    foreach ($roles as $role) {
        $stmt->execute($role);
    }

    $log->info('Role table seeded successfully!');

    $users = [
        ['id_user' => 1, 'id_role' => 1, 'nama' => 'admin', 'nip' => '123456789012345678', 'nim' => null, 'email' => 'admin@pnj.ac.id', 'password' => password_hash('akuadmin', PASSWORD_DEFAULT), 'status' => 'active', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456789', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 2, 'id_role' => 2, 'nama' => 'dosen', 'nip' => '987654321098765432', 'nim' => null, 'email' => 'dosen@pnj.ac.id', 'password' => password_hash('akudosen', PASSWORD_DEFAULT), 'status' => 'active', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456789', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 3, 'id_role' => 3, 'nama' => 'mahasiswa', 'nip' => null, 'nim' => '1234567890', 'email' => 'mahasiswa@stu.pnj.ac.id', 'password' => password_hash('akumahasiswa', PASSWORD_DEFAULT), 'status' => 'active', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456789', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],

        ['id_user' => 4, 'id_role' => 2, 'nama' => 'Dr. Budi Santoso, M.Kom', 'nip' => '198012345678901234', 'nim' => null, 'email' => 'budi.santoso@pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'active', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456790', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 5, 'id_role' => 2, 'nama' => 'Dr. Ani Lestari, M.T', 'nip' => '198112345678901235', 'nim' => null, 'email' => 'ani.lestari@pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'active', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456791', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 6, 'id_role' => 2, 'nama' => 'Prof. Dr. Joko Widodo, S.T., M.T.', 'nip' => '197512345678901236', 'nim' => null, 'email' => 'joko.widodo@pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'active', 'jurusan' => 'Teknik Elektro', 'nomor_hp' => '08123456792', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],

        ['id_user' => 7, 'id_role' => 3, 'nama' => 'Ahmad Fauzi', 'nip' => null, 'nim' => '2345678901', 'email' => 'ahmad.fauzi@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456793', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 8, 'id_role' => 3, 'nama' => 'Dewi Lestari', 'nip' => null, 'nim' => '2345678902', 'email' => 'dewi.lestari@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456794', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 9, 'id_role' => 3, 'nama' => 'Rizki Ramadhan', 'nip' => null, 'nim' => '2345678903', 'email' => 'rizki.ramadhan@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Elektro', 'nomor_hp' => '08123456795', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 10, 'id_role' => 3, 'nama' => 'Siti Aisyah', 'nip' => null, 'nim' => '2345678904', 'email' => 'siti.aisyah@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456796', 'suspensi_terakhir' => date('Y-m-d H:i:s'), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 11, 'id_role' => 3, 'nama' => 'Budi Santoso', 'nip' => null, 'nim' => '2345678905', 'email' => 'budi.santoso@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456797', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 12, 'id_role' => 3, 'nama' => 'Ani Lestari', 'nip' => null, 'nim' => '2345678906', 'email' => 'ani.lestari@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Elektro', 'nomor_hp' => '08123456798', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 13, 'id_role' => 3, 'nama' => 'Joko Susilo', 'nip' => null, 'nim' => '2345678907', 'email' => 'joko.susilo@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456799', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 14, 'id_role' => 3, 'nama' => 'Dian Puspita', 'nip' => null, 'nim' => '2345678908', 'email' => 'dian.puspita@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456800', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 15, 'id_role' => 3, 'nama' => 'Rudi Hermawan', 'nip' => null, 'nim' => '2345678909', 'email' => 'rudi.hermawan@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Elektro', 'nomor_hp' => '08123456801', 'suspensi_terakhir' => date('Y-m-d H:i:s'), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 16, 'id_role' => 3, 'nama' => 'Siti Rahayu', 'nip' => null, 'nim' => '2345678910', 'email' => 'siti.rahayu@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456802', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 17, 'id_role' => 3, 'nama' => 'Bambang Setiawan', 'nip' => null, 'nim' => '2345678911', 'email' => 'bambang.setiawan@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456803', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 18, 'id_role' => 3, 'nama' => 'Dewi Kurnia', 'nip' => null, 'nim' => '2345678912', 'email' => 'dewi.kurnia@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Elektro', 'nomor_hp' => '08123456804', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 19, 'id_role' => 3, 'nama' => 'Agus Prabowo', 'nip' => null, 'nim' => '2345678913', 'email' => 'agus.prabowo@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456805', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 20, 'id_role' => 3, 'nama' => 'Rina Wijaya', 'nip' => null, 'nim' => '2345678914', 'email' => 'rina.wijaya@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456806', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 21, 'id_role' => 3, 'nama' => 'Fajar Nugraha', 'nip' => null, 'nim' => '2345678915', 'email' => 'fajar.nugraha@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Elektro', 'nomor_hp' => '08123456807', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 22, 'id_role' => 3, 'nama' => 'Maya Sari', 'nip' => null, 'nim' => '2345678916', 'email' => 'maya.sari@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456808', 'suspensi_terakhir' => date('Y-m-d H:i:s'), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 23, 'id_role' => 3, 'nama' => 'Hendra Kurniawan', 'nip' => null, 'nim' => '2345678917', 'email' => 'hendra.kurniawan@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456809', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 24, 'id_role' => 3, 'nama' => 'Lina Marlina', 'nip' => null, 'nim' => '2345678918', 'email' => 'lina.marlina@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Elektro', 'nomor_hp' => '08123456810', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 25, 'id_role' => 3, 'nama' => 'Doni Prasetyo', 'nip' => null, 'nim' => '2345678919', 'email' => 'doni.prasetyo@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456811', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 26, 'id_role' => 3, 'nama' => 'Rina Amelia', 'nip' => null, 'nim' => '2345678920', 'email' => 'rina.amelia@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456812', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 27, 'id_role' => 3, 'nama' => 'Adi Saputra', 'nip' => null, 'nim' => '2345678921', 'email' => 'adi.saputra@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Elektro', 'nomor_hp' => '08123456813', 'suspensi_terakhir' => date('Y-m-d H:i:s'), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 28, 'id_role' => 3, 'nama' => 'Siti Maimunah', 'nip' => null, 'nim' => '2345678922', 'email' => 'siti.maimunah@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456814', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 29, 'id_role' => 3, 'nama' => 'Budi Santosa', 'nip' => null, 'nim' => '2345678923', 'email' => 'budi.santosa@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456815', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
        ['id_user' => 30, 'id_role' => 3, 'nama' => 'Rina Wulandari', 'nip' => null, 'nim' => '2345678924', 'email' => 'rina.wulandari@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Elektro', 'nomor_hp' => '08123456816', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'masa_aktif' => '2030-12-31'],
    ];

    $stmt = $db->pdo->prepare("
        INSERT INTO users (
            id_user, id_role, nama, nip, nim, email, password,
            status, jurusan, nomor_hp, suspensi_terakhir, masa_aktif, created_at, updated_at
        ) VALUES (
            :id_user, :id_role, :nama, :nip, :nim, :email, :password,
            :status, :jurusan, :nomor_hp, :suspensi_terakhir, :masa_aktif, :created_at, :updated_at
        )
        ON DUPLICATE KEY UPDATE
            id_role = VALUES(id_role),
            nama = VALUES(nama),
            nip = VALUES(nip),
            nim = VALUES(nim),
            email = VALUES(email),
            password = VALUES(password),
            status = VALUES(status),
            jurusan = VALUES(jurusan),
            nomor_hp = VALUES(nomor_hp),
            suspensi_terakhir = VALUES(suspensi_terakhir),
            masa_aktif = VALUES(masa_aktif),
            updated_at = VALUES(updated_at)
    ");

    foreach ($users as $user) {
        $stmt->execute($user);
    }

    $log->info('Users table seeded successfully!');

    $rooms = [
        ['id_ruangan' => 1, 'nama_ruangan' => 'Ruang Layar', 'kapasitas_min' => 6, 'kapasitas_max' => 12, 'jenis_ruangan' => 'Audio Visual', 'deskripsi_ruangan' => 'Ruangan Audio Visual', 'status_ruangan' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'requires_special_approval' => 0],
        ['id_ruangan' => 2, 'nama_ruangan' => 'Ruang Sinergi', 'kapasitas_min' => 6, 'kapasitas_max' => 12, 'jenis_ruangan' => 'Telekonferensi', 'deskripsi_ruangan' => 'Ruangan Telekonferensi', 'status_ruangan' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'requires_special_approval' => 0],
        ['id_ruangan' => 3, 'nama_ruangan' => 'Zona Interaktif', 'kapasitas_min' => 6, 'kapasitas_max' => 12, 'jenis_ruangan' => 'Kreasi dan Rekreasi', 'deskripsi_ruangan' => 'Ruangan Kreasi dan Rekreasi', 'status_ruangan' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'requires_special_approval' => 0],
        ['id_ruangan' => 4, 'nama_ruangan' => 'Sudut Pustaka', 'kapasitas_min' => 6, 'kapasitas_max' => 12, 'jenis_ruangan' => 'Baca Kelompok', 'deskripsi_ruangan' => 'Ruangan Baca Kelompok', 'status_ruangan' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'requires_special_approval' => 0],
        ['id_ruangan' => 5, 'nama_ruangan' => 'Galeri Literasi', 'kapasitas_min' => 5, 'kapasitas_max' => 12, 'jenis_ruangan' => 'Baca Kelompok', 'deskripsi_ruangan' => 'Ruangan Baca Kelompok', 'status_ruangan' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'requires_special_approval' => 0],
        ['id_ruangan' => 6, 'nama_ruangan' => 'Ruang Cendekia', 'kapasitas_min' => 5, 'kapasitas_max' => 12, 'jenis_ruangan' => 'Baca Kelompok', 'deskripsi_ruangan' => 'Ruangan Baca Kelompok', 'status_ruangan' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'requires_special_approval' => 0],
        ['id_ruangan' => 7, 'nama_ruangan' => 'Pusat Prancis', 'kapasitas_min' => 6, 'kapasitas_max' => 12, 'jenis_ruangan' => 'Koleksi Bahasa Prancis', 'deskripsi_ruangan' => 'Ruangan Koleksi Bahasa Prancis', 'status_ruangan' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'requires_special_approval' => 0],
        ['id_ruangan' => 8, 'nama_ruangan' => 'Ruang Asa', 'kapasitas_min' => 2, 'kapasitas_max' => 3, 'jenis_ruangan' => 'Bimbingan & Konseling', 'deskripsi_ruangan' => 'Ruangan Bimbingan & Konseling', 'status_ruangan' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'requires_special_approval' => 0],
        ['id_ruangan' => 9, 'nama_ruangan' => 'Lentera Edukasi', 'kapasitas_min' => 2, 'kapasitas_max' => 4, 'jenis_ruangan' => 'Bimbingan & Konseling', 'deskripsi_ruangan' => 'Ruangan Bimbingan & Konseling', 'status_ruangan' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'requires_special_approval' => 0],
        ['id_ruangan' => 10, 'nama_ruangan' => 'Ruang Rapat', 'kapasitas_min' => 2, 'kapasitas_max' => 20, 'jenis_ruangan' => 'Ruang Rapat', 'deskripsi_ruangan' => 'Ruangan Rapat', 'status_ruangan' => 'adminOnly', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'requires_special_approval' => 0],
    ];

    $stmt = $db->pdo->prepare("
        INSERT INTO ruangan (id_ruangan, nama_ruangan, kapasitas_min, kapasitas_max, jenis_ruangan, deskripsi_ruangan, status_ruangan, created_at, updated_at, requires_special_approval)
        VALUES
        (:id_ruangan, :nama_ruangan, :kapasitas_min, :kapasitas_max, :jenis_ruangan, :deskripsi_ruangan, :status_ruangan, :created_at, :updated_at, :requires_special_approval)
        ON DUPLICATE KEY UPDATE
            id_ruangan = VALUES(id_ruangan),
            nama_ruangan = VALUES(nama_ruangan),
            kapasitas_min = VALUES(kapasitas_min),
            kapasitas_max = VALUES(kapasitas_max),
            jenis_ruangan = VALUES(jenis_ruangan),
            deskripsi_ruangan = VALUES(deskripsi_ruangan),
            status_ruangan = VALUES(status_ruangan),
            requires_special_approval = VALUES(requires_special_approval),
            updated_at = VALUES(updated_at)
    ");

    foreach ($rooms as $room) {
        $stmt->execute($room);
    }

    $log->info('Ruangan table seeded successfully!');

    $bookings = [];
    $tujuanList = ['Belajar Kelompok', 'Diskusi Tugas Akhir', 'Rapat Organisasi', 'Presentasi Proyek', 'Pembuatan Konten Edukasi', 'Bimbingan Akademik', 'Meeting Online', 'Workshop', 'Seminar Internal', 'Latihan Presentasi'];
    $mahasiswaIds = [3, 16, 18, 19, 21, 23, 25, 26, 28, 30];

    for ($i = 1; $i <= 100; $i++) {
        $daysAgo = rand(1, 60);
        $tanggal = date('Y-m-d', strtotime("-{$daysAgo} days"));
        $jamMulai = rand(8, 14);
        $jamSelesai = $jamMulai + rand(1, 2);

        $bookings[] = [
            'id_booking' => $i,
            'user_id' => $mahasiswaIds[array_rand($mahasiswaIds)],
            'ruangan_id' => rand(1, 9),
            'tanggal_booking' => $tanggal,
            'tanggal_penggunaan_ruang' => $tanggal,
            'waktu_mulai' => sprintf('%02d:15:00', $jamMulai),
            'waktu_selesai' => sprintf('%02d:15:00', $jamSelesai),
            'tujuan' => $tujuanList[array_rand($tujuanList)],
            'status' => 'completed',
            'checkin_code' => strtoupper(substr(md5(uniqid()), 0, 6)),
            'invite_token' => null,
            'has_been_rescheduled' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }
    $stmt = $db->pdo->prepare("
        INSERT INTO booking (id_booking, user_id, ruangan_id, tanggal_booking, tanggal_penggunaan_ruang, waktu_mulai, waktu_selesai, tujuan, status, checkin_code, invite_token, has_been_rescheduled, created_at, updated_at)
        VALUES (:id_booking, :user_id, :ruangan_id, :tanggal_booking, :tanggal_penggunaan_ruang, :waktu_mulai, :waktu_selesai, :tujuan, :status, :checkin_code, :invite_token, :has_been_rescheduled, :created_at, :updated_at)
        ON DUPLICATE KEY UPDATE
            user_id = VALUES(user_id),
            ruangan_id = VALUES(ruangan_id),
            tanggal_penggunaan_ruang = VALUES(tanggal_penggunaan_ruang),
            waktu_mulai = VALUES(waktu_mulai),
            waktu_selesai = VALUES(waktu_selesai),
            tujuan = VALUES(tujuan),
            status = VALUES(status),
            checkin_code = VALUES(checkin_code),
            updated_at = VALUES(updated_at)
    ");
    foreach ($bookings as $booking) {
        $stmt->execute($booking);
    }
    $log->info('Bookings table seeded successfully!');

    $anggota_bookings = [];
    $mahasiswaIds = [3, 16, 18, 19, 21, 23, 25, 26, 28, 30];
    $anggotaId = 1;

    for ($bookingId = 1; $bookingId <= 100; $bookingId++) {
        // Get the PIC (user who made the booking) - we'll add them first
        $picUserId = $bookings[$bookingId - 1]['user_id'];

        // Add PIC as first member
        $anggota_bookings[] = [
            'id_anggota' => $anggotaId++,
            'booking_id' => $bookingId,
            'user_id' => $picUserId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Add 2-4 more random members (excluding PIC)
        $jumlahAnggotaTambahan = rand(2, 4);
        $availableMembers = array_diff($mahasiswaIds, [$picUserId]);
        $randomMembers = array_rand(array_flip($availableMembers), min($jumlahAnggotaTambahan, count($availableMembers)));

        if (!is_array($randomMembers)) {
            $randomMembers = [$randomMembers];
        }

        foreach ($randomMembers as $memberId) {
            $anggota_bookings[] = [
                'id_anggota' => $anggotaId++,
                'booking_id' => $bookingId,
                'user_id' => $memberId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
    }
    $stmt = $db->pdo->prepare("
        INSERT INTO anggota_booking (id_anggota, booking_id, user_id, created_at, updated_at)
        VALUES (:id_anggota, :booking_id, :user_id, :created_at, :updated_at)
        ON DUPLICATE KEY UPDATE
            booking_id = VALUES(booking_id),
            user_id = VALUES(user_id),
            updated_at = VALUES(updated_at)
    ");

    foreach ($anggota_bookings as $anggota) {
        $stmt->execute($anggota);
    }
    $log->info('Anggota Booking table seeded successfully!');

    $feedbacks = [];
    $komentarList = [
        'Ruangannya nyaman dan bersih!',
        'AC dingin, cocok untuk diskusi.',
        'Fasilitas lengkap, recommended!',
        'Tempatnya strategis dan tenang.',
        'Proyektor berfungsi dengan baik.',
        'Meja dan kursi nyaman.',
        'WiFi lancar, mantap!',
        'Pelayanan admin ramah.',
        'Ruangan luas, cukup untuk kelompok besar.',
        'Pencahayaan bagus untuk presentasi.'
    ];

    for ($i = 1; $i <= 100; $i++) {
        $feedbacks[] = [
            'id_feedback' => $i,
            'booking_id' => $i,
            'user_id' => $bookings[$i - 1]['user_id'], // PIC yang kasih feedback
            'rating' => rand(7, 10),
            'komentar' => $komentarList[array_rand($komentarList)],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }
    $stmt = $db->pdo->prepare("
        INSERT INTO feedback (id_feedback, booking_id, user_id, rating, komentar, created_at, updated_at)
        VALUES (:id_feedback, :booking_id, :user_id, :rating, :komentar, :created_at, :updated_at)
        ON DUPLICATE KEY UPDATE
            booking_id = VALUES(booking_id),
            user_id = VALUES(user_id),
            rating = VALUES(rating),
            komentar = VALUES(komentar),
            updated_at = VALUES(updated_at)
    ");

    foreach ($feedbacks as $feedback) {
        $stmt->execute($feedback);
    }
    $log->info('Feedback table seeded successfully!');

    $warnings = [
        ['id_peringatan' => 1, 'nama_peringatan' => 'No Show', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ['id_peringatan' => 2, 'nama_peringatan' => 'PIC Batalkan Booking Verified', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ['id_peringatan' => 3, 'nama_peringatan' => 'Melanggar Aturan Penggunaan Ruangan', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ['id_peringatan' => 4, 'nama_peringatan' => 'Merusak Fasilitas', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ];
    $stmt = $db->pdo->prepare("
        INSERT INTO peringatan_suspensi (id_peringatan, nama_peringatan, created_at, updated_at)
        VALUES (:id_peringatan, :nama_peringatan, :created_at, :updated_at)
        ON DUPLICATE KEY UPDATE
            nama_peringatan = VALUES(nama_peringatan),
            updated_at = VALUES(updated_at)
    ");

    foreach ($warnings as $warning) {
        $stmt->execute($warning);
    }
    $log->info('Peringatan Suspensi table seeded successfully!');
} catch (Exception $e) {
    $log->error('Seeder failed', ['error' => $e->getMessage()]);
}


