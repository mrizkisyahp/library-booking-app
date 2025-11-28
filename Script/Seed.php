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
    ['id_user' => 1, 'id_role' => 1, 'nama' => 'admin', 'nip' => '123456789012345678', 'nim' => null, 'email' => 'admin@pnj.ac.id', 'password' => password_hash('admin', PASSWORD_DEFAULT), 'status' => 'active', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456789', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 2, 'id_role' => 2, 'nama' => 'dosen', 'nip' => '987654321098765432', 'nim' => null, 'email' => 'dosen@pnj.ac.id', 'password' => password_hash('dosen', PASSWORD_DEFAULT), 'status' => 'active', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456789', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 3, 'id_role' => 3, 'nama' => 'mahasiswa', 'nip' => null, 'nim' => '1234567890', 'email' => 'mahasiswa@stu.pnj.ac.id', 'password' => password_hash('mahasiswa', PASSWORD_DEFAULT), 'status' => 'active', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456789', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],

    ['id_user' => 4, 'id_role' => 2, 'nama' => 'Dr. Budi Santoso, M.Kom', 'nip' => '198012345678901234', 'nim' => null, 'email' => 'budi.santoso@pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'active', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456790', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 5, 'id_role' => 2, 'nama' => 'Dr. Ani Lestari, M.T', 'nip' => '198112345678901235', 'nim' => null, 'email' => 'ani.lestari@pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'active', 'jurusan' => 'Sistem Informasi', 'nomor_hp' => '08123456791', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 6, 'id_role' => 2, 'nama' => 'Prof. Dr. Joko Widodo, S.T., M.T.', 'nip' => '197512345678901236', 'nim' => null, 'email' => 'joko.widodo@pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'active', 'jurusan' => 'Teknik Elektro', 'nomor_hp' => '08123456792', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],

    ['id_user' => 7, 'id_role' => 3, 'nama' => 'Ahmad Fauzi', 'nip' => null, 'nim' => '2345678901', 'email' => 'ahmad.fauzi@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456793', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 8, 'id_role' => 3, 'nama' => 'Dewi Lestari', 'nip' => null, 'nim' => '2345678902', 'email' => 'dewi.lestari@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Sistem Informasi', 'nomor_hp' => '08123456794', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 9, 'id_role' => 3, 'nama' => 'Rizki Ramadhan', 'nip' => null, 'nim' => '2345678903', 'email' => 'rizki.ramadhan@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Elektro', 'nomor_hp' => '08123456795', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 10, 'id_role' => 3, 'nama' => 'Siti Aisyah', 'nip' => null, 'nim' => '2345678904', 'email' => 'siti.aisyah@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'rejected', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456796', 'suspensi_terakhir' => date('Y-m-d H:i:s'), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 11, 'id_role' => 3, 'nama' => 'Budi Santoso', 'nip' => null, 'nim' => '2345678905', 'email' => 'budi.santoso@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Sistem Informasi', 'nomor_hp' => '08123456797', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 12, 'id_role' => 3, 'nama' => 'Ani Lestari', 'nip' => null, 'nim' => '2345678906', 'email' => 'ani.lestari@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Elektro', 'nomor_hp' => '08123456798', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 13, 'id_role' => 3, 'nama' => 'Joko Susilo', 'nip' => null, 'nim' => '2345678907', 'email' => 'joko.susilo@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456799', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 14, 'id_role' => 3, 'nama' => 'Dian Puspita', 'nip' => null, 'nim' => '2345678908', 'email' => 'dian.puspita@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Sistem Informasi', 'nomor_hp' => '08123456800', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 15, 'id_role' => 3, 'nama' => 'Rudi Hermawan', 'nip' => null, 'nim' => '2345678909', 'email' => 'rudi.hermawan@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'suspended', 'jurusan' => 'Teknik Elektro', 'nomor_hp' => '08123456801', 'suspensi_terakhir' => date('Y-m-d H:i:s'), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 16, 'id_role' => 3, 'nama' => 'Siti Rahayu', 'nip' => null, 'nim' => '2345678910', 'email' => 'siti.rahayu@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'active', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456802', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 17, 'id_role' => 3, 'nama' => 'Bambang Setiawan', 'nip' => null, 'nim' => '2345678911', 'email' => 'bambang.setiawan@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Sistem Informasi', 'nomor_hp' => '08123456803', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 18, 'id_role' => 3, 'nama' => 'Dewi Kurnia', 'nip' => null, 'nim' => '2345678912', 'email' => 'dewi.kurnia@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'active', 'jurusan' => 'Teknik Elektro', 'nomor_hp' => '08123456804', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 19, 'id_role' => 3, 'nama' => 'Agus Prabowo', 'nip' => null, 'nim' => '2345678913', 'email' => 'agus.prabowo@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'active', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456805', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 20, 'id_role' => 3, 'nama' => 'Rina Wijaya', 'nip' => null, 'nim' => '2345678914', 'email' => 'rina.wijaya@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Sistem Informasi', 'nomor_hp' => '08123456806', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 21, 'id_role' => 3, 'nama' => 'Fajar Nugraha', 'nip' => null, 'nim' => '2345678915', 'email' => 'fajar.nugraha@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'active', 'jurusan' => 'Teknik Elektro', 'nomor_hp' => '08123456807', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 22, 'id_role' => 3, 'nama' => 'Maya Sari', 'nip' => null, 'nim' => '2345678916', 'email' => 'maya.sari@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'suspended', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456808', 'suspensi_terakhir' => date('Y-m-d H:i:s'), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 23, 'id_role' => 3, 'nama' => 'Hendra Kurniawan', 'nip' => null, 'nim' => '2345678917', 'email' => 'hendra.kurniawan@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'active', 'jurusan' => 'Sistem Informasi', 'nomor_hp' => '08123456809', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 24, 'id_role' => 3, 'nama' => 'Lina Marlina', 'nip' => null, 'nim' => '2345678918', 'email' => 'lina.marlina@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Teknik Elektro', 'nomor_hp' => '08123456810', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 25, 'id_role' => 3, 'nama' => 'Doni Prasetyo', 'nip' => null, 'nim' => '2345678919', 'email' => 'doni.prasetyo@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'active', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456811', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 26, 'id_role' => 3, 'nama' => 'Rina Amelia', 'nip' => null, 'nim' => '2345678920', 'email' => 'rina.amelia@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'active', 'jurusan' => 'Sistem Informasi', 'nomor_hp' => '08123456812', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 27, 'id_role' => 3, 'nama' => 'Adi Saputra', 'nip' => null, 'nim' => '2345678921', 'email' => 'adi.saputra@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'suspended', 'jurusan' => 'Teknik Elektro', 'nomor_hp' => '08123456813', 'suspensi_terakhir' => date('Y-m-d H:i:s'), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 28, 'id_role' => 3, 'nama' => 'Siti Maimunah', 'nip' => null, 'nim' => '2345678922', 'email' => 'siti.maimunah@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'active', 'jurusan' => 'Teknik Informatika', 'nomor_hp' => '08123456814', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 29, 'id_role' => 3, 'nama' => 'Budi Santosa', 'nip' => null, 'nim' => '2345678923', 'email' => 'budi.santosa@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'pending kubaca', 'jurusan' => 'Sistem Informasi', 'nomor_hp' => '08123456815', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ['id_user' => 30, 'id_role' => 3, 'nama' => 'Rina Wulandari', 'nip' => null, 'nim' => '2345678924', 'email' => 'rina.wulandari@stu.pnj.ac.id', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'status' => 'active', 'jurusan' => 'Teknik Elektro', 'nomor_hp' => '08123456816', 'suspensi_terakhir' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ];

    $stmt = $db->pdo->prepare("
        INSERT INTO users (
            id_user, id_role, nama, nip, nim, email, password,
            status, jurusan, nomor_hp, suspensi_terakhir, created_at, updated_at
        ) VALUES (
            :id_user, :id_role, :nama, :nip, :nim, :email, :password,
            :status, :jurusan, :nomor_hp, :suspensi_terakhir, :created_at, :updated_at
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
            updated_at = VALUES(updated_at)
    ");

    foreach ($users as $user) {
        $stmt->execute($user);
    }

    $log->info('Users table seeded successfully!');

    $rooms = [
        ['id_ruangan' => 1, 'nama_ruangan' => 'Ruang Layar', 'kapasitas_min' => 6, 'kapasitas_max' => 12, 'jenis_ruangan' => 'Audio Visual', 'deskripsi_ruangan' => 'Ruangan Audio Visual', 'status_ruangan' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ['id_ruangan' => 2, 'nama_ruangan' => 'Ruang Sinergi', 'kapasitas_min' => 6, 'kapasitas_max' => 12, 'jenis_ruangan' => 'Telekonferensi', 'deskripsi_ruangan' => 'Ruangan Telekonferensi', 'status_ruangan' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ['id_ruangan' => 3, 'nama_ruangan' => 'Zona Interaktif', 'kapasitas_min' => 6, 'kapasitas_max' => 12, 'jenis_ruangan' => 'Kreasi dan Rekreasi', 'deskripsi_ruangan' => 'Ruangan Kreasi dan Rekreasi', 'status_ruangan' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ['id_ruangan' => 4, 'nama_ruangan' => 'Sudut Pustaka', 'kapasitas_min' => 6, 'kapasitas_max' => 12, 'jenis_ruangan' => 'Baca Kelompok', 'deskripsi_ruangan' => 'Ruangan Baca Kelompok', 'status_ruangan' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ['id_ruangan' => 5, 'nama_ruangan' => 'Galeri Literasi', 'kapasitas_min' => 5, 'kapasitas_max' => 12, 'jenis_ruangan' => 'Baca Kelompok', 'deskripsi_ruangan' => 'Ruangan Baca Kelompok', 'status_ruangan' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ['id_ruangan' => 6, 'nama_ruangan' => 'Ruang Cendekia', 'kapasitas_min' => 5, 'kapasitas_max' => 12, 'jenis_ruangan' => 'Baca Kelompok', 'deskripsi_ruangan' => 'Ruangan Baca Kelompok', 'status_ruangan' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ['id_ruangan' => 7, 'nama_ruangan' => 'Pusat Prancis', 'kapasitas_min' => 6, 'kapasitas_max' => 12, 'jenis_ruangan' => 'Koleksi Bahasa Prancis', 'deskripsi_ruangan' => 'Ruangan Koleksi Bahasa Prancis', 'status_ruangan' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ['id_ruangan' => 8, 'nama_ruangan' => 'Ruang Asa', 'kapasitas_min' => 2, 'kapasitas_max' => 3, 'jenis_ruangan' => 'Bimbingan & Konseling', 'deskripsi_ruangan' => 'Ruangan Bimbingan & Konseling', 'status_ruangan' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ['id_ruangan' => 9, 'nama_ruangan' => 'Lentera Edukasi', 'kapasitas_min' => 2, 'kapasitas_max' => 4, 'jenis_ruangan' => 'Bimbingan & Konseling', 'deskripsi_ruangan' => 'Ruangan Bimbingan & Konseling', 'status_ruangan' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ['id_ruangan' => 10, 'nama_ruangan' => 'Ruang Rapat', 'kapasitas_min' => 2, 'kapasitas_max' => 20, 'jenis_ruangan' => 'Ruang Rapat', 'deskripsi_ruangan' => 'Ruangan Rapat', 'status_ruangan' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    ];

    $stmt = $db->pdo->prepare("
        INSERT INTO ruangan (id_ruangan, nama_ruangan, kapasitas_min, kapasitas_max, jenis_ruangan, deskripsi_ruangan, status_ruangan, created_at, updated_at) 
        VALUES 
        (:id_ruangan, :nama_ruangan, :kapasitas_min, :kapasitas_max, :jenis_ruangan, :deskripsi_ruangan, :status_ruangan, :created_at, :updated_at)
        ON DUPLICATE KEY UPDATE
            id_ruangan = VALUES(id_ruangan),
            nama_ruangan = VALUES(nama_ruangan),
            kapasitas_min = VALUES(kapasitas_min),
            kapasitas_max = VALUES(kapasitas_max),
            jenis_ruangan = VALUES(jenis_ruangan),
            deskripsi_ruangan = VALUES(deskripsi_ruangan),
            status_ruangan = VALUES(status_ruangan),
            updated_at = VALUES(updated_at)
    ");

    foreach ($rooms as $room) {
        $stmt->execute($room);
    }

    $log->info('Ruangan table seeded successfully!');

    // $bookings = [
    //     ['id_booking' => 1, 'user_id' => 1, 'ruangan_id' => 1, 'tanggal_booking' => '2025-11-05', 'tanggal_penggunaan_ruang' => '2025-11-05', 'waktu_mulai' => '08:00:00', 'waktu_selesai' => '03:00:00', 'tujuan' => 'Meeting', 'status' => 'pending', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    //     ['id_booking' => 2, 'user_id' => 2, 'ruangan_id' => 2, 'tanggal_booking' => '2025-11-05', 'tanggal_penggunaan_ruang' => '2025-11-05', 'waktu_mulai' => '08:00:00', 'waktu_selesai' => '03:00:00', 'tujuan' => 'Meeting', 'status' => 'pending', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    //     ['id_booking' => 3, 'user_id' => 3, 'ruangan_id' => 3, 'tanggal_booking' => '2025-11-05', 'tanggal_penggunaan_ruang' => '2025-11-05', 'waktu_mulai' => '08:00:00', 'waktu_selesai' => '03:00:00', 'tujuan' => 'Meeting', 'status' => 'pending', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    // ];

    // $stmt = $db->pdo->prepare("
    //     INSERT INTO booking (id_booking, user_id, ruangan_id, tanggal_booking, tanggal_penggunaan_ruang, waktu_mulai, waktu_selesai, tujuan, status, created_at, updated_at) 
    //     VALUES 
    //     (:id_booking, :user_id, :ruangan_id, :tanggal_booking, :tanggal_penggunaan_ruang, :waktu_mulai, :waktu_selesai, :tujuan, :status, :created_at, :updated_at)
    //     ON DUPLICATE KEY UPDATE
    //         id_booking = VALUES(id_booking),
    //         user_id = VALUES(user_id),
    //         ruangan_id = VALUES(ruangan_id),
    //         tanggal_booking = VALUES(tanggal_booking),
    //         tanggal_penggunaan_ruang = VALUES(tanggal_penggunaan_ruang),
    //         waktu_mulai = VALUES(waktu_mulai),
    //         waktu_selesai = VALUES(waktu_selesai),
    //         tujuan = VALUES(tujuan),
    //         status = VALUES(status),
    //         updated_at = VALUES(updated_at)
    // ");

    // foreach ($bookings as $booking) {
    //     $stmt->execute($booking);
    // }

    // $log->info('Bookings table seeded successfully!');

    // $anggota_bookings = [
    //     ['id_anggota' => 1, 'booking_id' => 1, 'user_id' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    //     ['id_anggota' => 2, 'booking_id' => 2, 'user_id' => 2, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    //     ['id_anggota' => 3, 'booking_id' => 3, 'user_id' => 3, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    // ];

    // $stmt = $db->pdo->prepare("
    //     INSERT INTO anggota_booking (id_anggota, booking_id, user_id, created_at, updated_at) 
    //     VALUES 
    //     (:id_anggota, :booking_id, :user_id, :created_at, :updated_at)
    //     ON DUPLICATE KEY UPDATE
    //         id_anggota = VALUES(id_anggota),
    //         booking_id = VALUES(booking_id),
    //         user_id = VALUES(user_id),
    //         updated_at = VALUES(updated_at)
    // ");
    // foreach ($anggota_bookings as $anggota_booking) {
    //     $stmt->execute($anggota_booking);
    // }

    // $log->info('Anggota Booking table seeded successfully!');

    // $feedbacks = [
    //     ['id_feedback' => 1, 'booking_id' => 1, 'user_id' => 1, 'rating' => 10, 'komentar' => 'wah ruangannya bagus', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    //     ['id_feedback' => 2, 'booking_id' => 2, 'user_id' => 2, 'rating' => 10, 'komentar' => 'wah ruangannya bagus', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    //     ['id_feedback' => 3, 'booking_id' => 3, 'user_id' => 3, 'rating' => 10, 'komentar' => 'wah ruangannya bagus', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    // ];

    // $stmt = $db->pdo->prepare("
    //     INSERT INTO feedback (id_feedback, booking_id, user_id, rating, komentar, created_at, updated_at) 
    //     VALUES 
    //     (:id_feedback, :booking_id, :user_id, :rating, :komentar, :created_at, :updated_at)
    //     ON DUPLICATE KEY UPDATE
    //         id_feedback = VALUES(id_feedback),
    //         booking_id = VALUES(booking_id),
    //         user_id = VALUES(user_id),
    //         rating = VALUES(rating),
    //         komentar = VALUES(komentar),
    //         updated_at = VALUES(updated_at)
    // ");
    // foreach ($feedbacks as $feedback) {
    //     $stmt->execute($feedback);
    // }

    // $log->info('Feedback table seeded successfully!');

    // $warnings = [
    //     ['id_peringatan' => 1, 'nama_peringatan' => 'Terlambat', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    //     ['id_peringatan' => 2, 'nama_peringatan' => 'Cancel by User', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    //     ['id_peringatan' => 3, 'nama_peringatan' => 'Automatic Cancel - Not Enough Users', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    // ];

    // $stmt = $db->pdo->prepare("
    //     INSERT INTO peringatan_suspensi (id_peringatan, nama_peringatan, created_at, updated_at) 
    //     VALUES 
    //     (:id_peringatan, :nama_peringatan, :created_at, :updated_at)
    //     ON DUPLICATE KEY UPDATE
    //         id_peringatan = VALUES(id_peringatan),
    //         nama_peringatan = VALUES(nama_peringatan),
    //         updated_at = VALUES(updated_at)
    // ");
    // foreach ($warnings as $warning) {
    //     $stmt->execute($warning);
    // }

    // $log->info('Peringatan table seeded successfully!');

    // $warnings_mahasiswa = [
    //     ['id_peringatan_mhs' => 1, 'id_akun' => 1, 'id_peringatan' => 1, 'tgl_peringatan' => date('Y-m-d H:i:s'), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    //     ['id_peringatan_mhs' => 2, 'id_akun' => 2, 'id_peringatan' => 2, 'tgl_peringatan' => date('Y-m-d H:i:s'), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    //     ['id_peringatan_mhs' => 3, 'id_akun' => 3, 'id_peringatan' => 3, 'tgl_peringatan' => date('Y-m-d H:i:s'), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    // ];

    // $stmt = $db->pdo->prepare("
    //     INSERT INTO peringatan_mhs (id_peringatan_mhs, id_akun, id_peringatan, tgl_peringatan, created_at, updated_at) 
    //     VALUES 
    //     (:id_peringatan_mhs, :id_akun, :id_peringatan, :tgl_peringatan, :created_at, :updated_at)
    //     ON DUPLICATE KEY UPDATE
    //         id_peringatan_mhs = VALUES(id_peringatan_mhs),
    //         id_akun = VALUES(id_akun),
    //         id_peringatan = VALUES(id_peringatan),
    //         tgl_peringatan = VALUES(tgl_peringatan),
    //         updated_at = VALUES(updated_at)
    // ");
    // foreach ($warnings_mahasiswa as $warning_mahasiswa) {
    //     $stmt->execute($warning_mahasiswa);
    // }

    // $log->info('Peringatan Mahasiswa table seeded successfully!');

    // $suspends = [
    //     ['id_suspensi' => 1, 'id_akun' => 1, 'tgl_suspensi' => date('Y-m-d H:i:s'), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    //     ['id_suspensi' => 2, 'id_akun' => 2, 'tgl_suspensi' => date('Y-m-d H:i:s'), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    //     ['id_suspensi' => 3, 'id_akun' => 3, 'tgl_suspensi' => date('Y-m-d H:i:s'), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    // ];

    // $stmt = $db->pdo->prepare("
    //     INSERT INTO suspensi (id_suspensi, id_akun, tgl_suspensi, created_at, updated_at) 
    //     VALUES 
    //     (:id_suspensi, :id_akun, :tgl_suspensi, :created_at, :updated_at)
    //     ON DUPLICATE KEY UPDATE
    //         id_suspensi = VALUES(id_suspensi),
    //         id_akun = VALUES(id_akun),
    //         tgl_suspensi = VALUES(tgl_suspensi),
    //         updated_at = VALUES(updated_at)
    // ");
    // foreach ($suspends as $suspend) {
    //     $stmt->execute($suspend);
    // }

    // $log->info('Suspensi table seeded successfully!');

    $log->info('Seeder completed successfully!');
} catch (Exception $e) {
    $log->error('Seeder failed', ['error' => $e->getMessage()]);
}


