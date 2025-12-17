<?php
// default values/safety fallback
$filters = $filters ?? ['start_date' => '', 'end_date' => '', 'status' => ''];
$summary = $summary ?? ['total' => 0, 'completed' => 0, 'cancelled' => 0];
$chartData = $chartData ?? ['labels' => [], 'values' => []];
$reportRows = $reportRows ?? [];

?>

<div class="min-h-dvh">

    <div class="rounded-2xl p-4 mx-auto max-w-7xl">

        <!-- Header -->
        <div class="flex items-center mt-6 px-6 mb-6 md:bg-primary md:rounded-2xl md:p-6">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">Laporan Booking</h1>
                <p class="text-white">Pantau aktivitas peminjaman ruangan secara lengkap</p>
            </div>
        </div>


        <!-- filtres -->
        <form method="get" action="/admin/reports"
            class="grid grid-cols-1 gap-4 md:grid-cols-4 bg-white p-6 rounded-2xl shadow">

            <!-- Start Date -->
            <div>
                <label class="block text-sm text-slate-600 mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" value="<?= htmlspecialchars($filters['start_date']) ?>"
                    class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl bg-slate-50">
            </div>

            <!-- End Date -->
            <div>
                <label class="block text-sm text-slate-600 mb-2">Tanggal Akhir</label>
                <input type="date" name="end_date" value="<?= htmlspecialchars($filters['end_date']) ?>"
                    class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl bg-slate-50">
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm text-slate-600 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl bg-slate-50">
                    <option value="">Semua</option>
                    <?php foreach (['active', 'completed', 'cancelled'] as $st): ?>
                        <option value="<?= $st ?>" <?= $filters['status'] === $st ? 'selected' : '' ?>>
                            <?= ucfirst($st) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Apply -->
            <div class="flex items-end">
                <button class="w-full bg-primary text-white px-4 py-3 rounded-xl hover:bg-emerald-700 transition">
                    Terapkan
                </button>
            </div>

        </form>

        <!-- Quick Period Filters -->
        <div class="flex gap-2 mt-4 flex-wrap">
            <?php
            $today = date('Y-m-d');
            $weekStart = date('Y-m-d', strtotime('monday this week'));
            $monthStart = date('Y-m-01');

            // Build query params preserving chart_type and status
            $weekParams = http_build_query([
                'start_date' => $weekStart,
                'end_date' => $today,
                'chart_type' => $filters['chart_type'] ?? 'booking',
                'status' => $filters['status'] ?? ''
            ]);

            $monthParams = http_build_query([
                'start_date' => $monthStart,
                'end_date' => $today,
                'chart_type' => $filters['chart_type'] ?? 'booking',
                'status' => $filters['status'] ?? ''
            ]);
            ?>
            <a href="/admin/reports?<?= $weekParams ?>"
                class="px-3 py-1.5 text-sm rounded-lg border <?= ($filters['start_date'] === $weekStart && $filters['end_date'] === $today) ? 'bg-emerald-100 border-emerald-500 text-emerald-700' : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50' ?>">
                Minggu Ini
            </a>
            <a href="/admin/reports?<?= $monthParams ?>"
                class="px-3 py-1.5 text-sm rounded-lg border <?= ($filters['start_date'] === $monthStart && $filters['end_date'] === $today) ? 'bg-emerald-100 border-emerald-500 text-emerald-700' : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50' ?>">
                Bulan Ini
            </a>
            <a href="/admin/reports"
                class="px-3 py-1.5 text-sm rounded-lg border bg-white border-gray-300 text-gray-600 hover:bg-gray-50">
                Reset Filter
            </a>
        </div>

        <!-- Export Buttons -->
        <div class="flex gap-2 mt-4">
            <?php
            $exportParams = http_build_query([
                'start_date' => $filters['start_date'] ?? '',
                'end_date' => $filters['end_date'] ?? '',
                'status' => $filters['status'] ?? '',
                'chart_type' => $filters['chart_type'] ?? 'booking',
            ]);
            ?>
            <a href="/admin/reports/export-csv?<?= $exportParams ?>"
                class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export CSV
            </a>
            <a href="/admin/reports/export-pdf?<?= $exportParams ?>" target="_blank"
                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Export PDF
            </a>
        </div>

        <!-- hasil $getSummary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">

            <div class="p-6 rounded-2xl bg-white shadow border">
                <h3 class="text-sm text-gray-500">Total Booking</h3>
                <p class="text-3xl font-bold text-primary mt-1">
                    <?= (int) $summary['total'] ?>
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-white shadow border">
                <h3 class="text-sm text-gray-500">Selesai</h3>
                <p class="text-3xl font-bold text-emerald-600 mt-1">
                    <?= (int) $summary['completed'] ?>
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-white shadow border">
                <h3 class="text-sm text-gray-500">Dibatalkan</h3>
                <p class="text-3xl font-bold text-red-500 mt-1">
                    <?= (int) $summary['cancelled'] ?>
                </p>
            </div>

        </div>

        <!-- CHIP PEMILIHAN CHART -->
        <div class="flex gap-3 mt-6 flex-wrap">
            <?php
            $chips = [
                'booking' => 'Trend Booking',
                'daily' => 'Per Hari (Senin-Minggu)',
                'weekly' => 'Per Minggu',
                'monthly' => 'Per Bulan',
                'room' => 'Ruangan Terpopuler',
                'department' => 'Per Jurusan',
                'reason' => 'Tujuan Booking',
                'hours' => 'Jam Sibuk',
                'feedback' => 'Feedback'
            ];
            ?>

            <?php foreach ($chips as $key => $label): ?>
                <a href="/admin/reports?chart_type=<?= $key ?>" class="px-4 py-2 rounded-full border transition
                        <?= ($chartType === $key)
                            ? 'bg-primary text-white border-primary'
                            : 'bg-white border-gray-300 text-slate-600 hover:bg-gray-100'
                            ?>">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- CHART -->
        <div class="bg-white shadow rounded-2xl p-6 mt-6">
            <h2 class="text-xl font-bold mb-4">
                <?= isset($chips[(string) $chartType]) ? $chips[(string) $chartType] : 'Grafik Aktivitas' ?>
            </h2>

            <canvas id="bookingChart" class="w-full max-h-96"></canvas>

            <script>
                window.chartData = <?= json_encode($chartData) ?>;
                window.activeChartType = "<?= $chartType ?>";
            </script>

            <script src="/js/chart.umd.min.js"></script>
            <script src="/js/report-chart.js"></script>
        </div>



        <!-- EXPORT BUTTONS -->
        <div class="flex gap-3 mt-6">
            <a href="/admin/reports/export?type=csv"
                class="px-4 py-3 bg-primary text-white rounded-xl shadow hover:bg-emerald-700 transition">
                Export CSV
            </a>

            <a href="/admin/reports/export?type=pdf"
                class="px-4 py-3 bg-red-600 text-white rounded-xl shadow hover:bg-red-700 transition">
                Export PDF
            </a>
        </div>


        <!-- TABLE -->
        <div class="mt-6 bg-white p-6 rounded-2xl shadow overflow-x-auto">

            <h2 class="text-xl font-bold mb-4">Detail Laporan</h2>

            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b bg-slate-100 text-slate-600">
                        <th class="py-3 px-3 text-left">Kode</th>
                        <th class="py-3 px-3 text-left">User</th>
                        <th class="py-3 px-3 text-left">Ruangan</th>
                        <th class="py-3 px-3 text-left">Status</th>
                        <th class="py-3 px-3 text-left">Tanggal</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($reportRows)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-6 text-slate-500">
                                Tidak ada data laporan
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reportRows as $row): ?>
                            <tr class="border-b hover:bg-slate-50">
                                <td class="py-3 px-3"><?= htmlspecialchars($row['kode_booking']) ?></td>
                                <td class="py-3 px-3"><?= htmlspecialchars($row['user_name']) ?></td>
                                <td class="py-3 px-3"><?= htmlspecialchars($row['room_name']) ?></td>
                                <td class="py-3 px-3 capitalize"><?= htmlspecialchars($row['status']) ?></td>
                                <td class="py-3 px-3"><?= htmlspecialchars($row['tanggal']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>

            </table>

        </div>

    </div>
</div>