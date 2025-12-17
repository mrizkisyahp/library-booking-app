<?php
// Default values
$filters = $filters ?? ['start_date' => '', 'end_date' => '', 'status' => '', 'chart_type' => 'day'];
$summary = $summary ?? ['total' => 0, 'completed' => 0, 'no_show' => 0, 'cancelled' => 0, 'active' => 0, 'verified' => 0];
$chartData = $chartData ?? ['labels' => [], 'values' => []];
$reportRows = $reportRows ?? [];
$chartType = $chartType ?? 'day';

// Chart type labels
$chartTypes = [
    'day' => 'Per Hari',
    'week' => 'Per Minggu',
    'month' => 'Per Bulan',
    'semester' => 'Per Semester',
    'year' => 'Per Tahun',
    'room' => 'Ruangan Favorit',
    'department' => 'Per Jurusan',
    'purpose' => 'Tujuan Booking',
    'hours' => 'Jam Sibuk',
];

// Quick period filters
$today = date('Y-m-d');
$weekStart = date('Y-m-d', strtotime('monday this week'));
$monthStart = date('Y-m-01');
$yearStart = date('Y-01-01');
?>

<div class="min-h-dvh">
    <div class="rounded-2xl p-4 mx-auto max-w-7xl">

        <!-- Header -->
        <div class="flex items-center mt-6 px-6 mb-6 md:bg-primary md:rounded-2xl md:p-6">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">Laporan Booking</h1>
                <p class="text-white/80">Statistik peminjaman ruangan (tidak termasuk draft & pending)</p>
            </div>
        </div>

        <!-- Date Filters -->
        <form method="get" action="/admin/reports"
            class="grid grid-cols-1 gap-4 md:grid-cols-4 bg-white p-6 rounded-2xl shadow">
            <input type="hidden" name="chart_type" value="<?= htmlspecialchars($chartType) ?>">

            <div>
                <label class="block text-sm text-slate-600 mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" value="<?= htmlspecialchars($filters['start_date']) ?>"
                    class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl bg-slate-50 focus:border-primary focus:outline-none">
            </div>

            <div>
                <label class="block text-sm text-slate-600 mb-2">Tanggal Akhir</label>
                <input type="date" name="end_date" value="<?= htmlspecialchars($filters['end_date']) ?>"
                    class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl bg-slate-50 focus:border-primary focus:outline-none">
            </div>

            <div>
                <label class="block text-sm text-slate-600 mb-2">Status (Opsional)</label>
                <select name="status"
                    class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl bg-slate-50 focus:border-primary focus:outline-none">
                    <option value="">Semua Status</option>
                    <?php foreach (['verified', 'active', 'completed', 'cancelled', 'no_show', 'expired'] as $st): ?>
                        <option value="<?= $st ?>" <?= $filters['status'] === $st ? 'selected' : '' ?>>
                            <?= ucfirst(str_replace('_', ' ', $st)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit"
                    class="w-full bg-primary text-white px-4 py-3 rounded-xl hover:bg-emerald-700 transition font-medium">
                    Terapkan Filter
                </button>
            </div>
        </form>

        <!-- Quick Period Buttons -->
        <div class="flex gap-2 mt-4 flex-wrap">
            <?php
            $periods = [
                ['label' => 'Minggu Ini', 'start' => $weekStart, 'end' => $today],
                ['label' => 'Bulan Ini', 'start' => $monthStart, 'end' => $today],
                ['label' => 'Tahun Ini', 'start' => $yearStart, 'end' => $today],
            ];
            foreach ($periods as $p):
                $isActive = ($filters['start_date'] === $p['start'] && $filters['end_date'] === $p['end']);
                $params = http_build_query(['start_date' => $p['start'], 'end_date' => $p['end'], 'chart_type' => $chartType]);
                ?>
                <a href="/admin/reports?<?= $params ?>"
                    class="px-4 py-2 text-sm rounded-lg border transition <?= $isActive ? 'bg-emerald-100 border-emerald-500 text-emerald-700' : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50' ?>">
                    <?= $p['label'] ?>
                </a>
            <?php endforeach; ?>
            <a href="/admin/reports"
                class="px-4 py-2 text-sm rounded-lg border bg-white border-gray-300 text-gray-600 hover:bg-gray-50">
                Reset Filter
            </a>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mt-6">
            <div class="p-5 rounded-2xl bg-white shadow border">
                <h3 class="text-sm text-gray-500">Total</h3>
                <p class="text-3xl font-bold text-primary mt-1"><?= number_format($summary['total']) ?></p>
            </div>
            <div class="p-5 rounded-2xl bg-white shadow border">
                <h3 class="text-sm text-gray-500">Verified</h3>
                <p class="text-3xl font-bold text-blue-600 mt-1"><?= number_format($summary['verified']) ?></p>
            </div>
            <div class="p-5 rounded-2xl bg-white shadow border">
                <h3 class="text-sm text-gray-500">Active</h3>
                <p class="text-3xl font-bold text-amber-600 mt-1"><?= number_format($summary['active']) ?></p>
            </div>
            <div class="p-5 rounded-2xl bg-white shadow border">
                <h3 class="text-sm text-gray-500">Completed</h3>
                <p class="text-3xl font-bold text-emerald-600 mt-1"><?= number_format($summary['completed']) ?></p>
            </div>
            <div class="p-5 rounded-2xl bg-white shadow border">
                <h3 class="text-sm text-gray-500">No Show</h3>
                <p class="text-3xl font-bold text-orange-500 mt-1"><?= number_format($summary['no_show']) ?></p>
            </div>
            <div class="p-5 rounded-2xl bg-white shadow border">
                <h3 class="text-sm text-gray-500">Cancelled</h3>
                <p class="text-3xl font-bold text-red-500 mt-1"><?= number_format($summary['cancelled']) ?></p>
            </div>
        </div>

        <!-- Chart Type Selector -->
        <div class="flex gap-2 mt-6 flex-wrap">
            <?php foreach ($chartTypes as $key => $label): ?>
                <?php
                $params = http_build_query([
                    'start_date' => $filters['start_date'],
                    'end_date' => $filters['end_date'],
                    'status' => $filters['status'],
                    'chart_type' => $key
                ]);
                ?>
                <a href="/admin/reports?<?= $params ?>" class="px-4 py-2 rounded-full border transition text-sm font-medium
                          <?= $chartType === $key
                              ? 'bg-primary text-white border-primary'
                              : 'bg-white border-gray-300 text-slate-600 hover:bg-gray-100' ?>">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Chart -->
        <div class="bg-white shadow rounded-2xl p-6 mt-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold"><?= $chartTypes[$chartType] ?? 'Grafik' ?></h2>

                <!-- Export Buttons -->
                <div class="flex gap-2">
                    <?php
                    $exportParams = http_build_query($filters);
                    ?>
                    <a href="/admin/reports/export-csv?<?= $exportParams ?>"
                        class="inline-flex items-center px-3 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        CSV
                    </a>
                    <a href="/admin/reports/export-pdf?<?= $exportParams ?>" target="_blank"
                        class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        PDF
                    </a>
                </div>
            </div>

            <canvas id="reportChart" class="w-full" style="max-height: 400px;"></canvas>
        </div>

        <!-- Data Table -->
        <div class="mt-6 bg-white p-6 rounded-2xl shadow overflow-x-auto">
            <h2 class="text-xl font-bold mb-4">Detail Data (<?= count($reportRows) ?> data)</h2>

            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b bg-slate-100 text-slate-600">
                        <th class="py-3 px-3 text-left">Kode</th>
                        <th class="py-3 px-3 text-left">User</th>
                        <th class="py-3 px-3 text-left">Jurusan</th>
                        <th class="py-3 px-3 text-left">Ruangan</th>
                        <th class="py-3 px-3 text-left">Tujuan</th>
                        <th class="py-3 px-3 text-left">Tanggal</th>
                        <th class="py-3 px-3 text-left">Waktu</th>
                        <th class="py-3 px-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reportRows)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-500">
                                Tidak ada data untuk filter yang dipilih
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reportRows as $row): ?>
                            <tr class="border-b hover:bg-slate-50">
                                <td class="py-3 px-3 font-mono text-xs"><?= htmlspecialchars($row['kode_booking'] ?? '-') ?>
                                </td>
                                <td class="py-3 px-3"><?= htmlspecialchars($row['user_name'] ?? '-') ?></td>
                                <td class="py-3 px-3"><?= htmlspecialchars($row['user_jurusan'] ?? '-') ?></td>
                                <td class="py-3 px-3"><?= htmlspecialchars($row['room_name'] ?? '-') ?></td>
                                <td class="py-3 px-3 max-w-[200px] truncate"
                                    title="<?= htmlspecialchars($row['tujuan'] ?? '') ?>">
                                    <?= htmlspecialchars($row['tujuan'] ?? '-') ?>
                                </td>
                                <td class="py-3 px-3"><?= $row['tanggal'] ?? '-' ?></td>
                                <td class="py-3 px-3">
                                    <?= ($row['waktu_mulai'] ?? '-') . ' - ' . ($row['waktu_selesai'] ?? '-') ?></td>
                                <td class="py-3 px-3">
                                    <?php
                                    $statusColors = [
                                        'verified' => 'bg-blue-100 text-blue-700',
                                        'active' => 'bg-amber-100 text-amber-700',
                                        'completed' => 'bg-emerald-100 text-emerald-700',
                                        'cancelled' => 'bg-red-100 text-red-700',
                                        'no_show' => 'bg-orange-100 text-orange-700',
                                        'expired' => 'bg-gray-100 text-gray-700',
                                    ];
                                    $status = $row['status'] ?? '';
                                    $color = $statusColors[$status] ?? 'bg-gray-100 text-gray-700';
                                    ?>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium <?= $color ?>">
                                        <?= ucfirst(str_replace('_', ' ', $status)) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- Chart.js -->
<script src="/js/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('reportChart').getContext('2d');
        const chartData = <?= json_encode($chartData) ?>;
        const chartType = '<?= $chartType ?>';

        // Determine chart type based on data
        const isBarChart = ['day', 'week', 'month', 'semester', 'year', 'room', 'department', 'purpose', 'hours'].includes(chartType);

        // Colors for bars
        const colors = [
            'rgba(5, 150, 105, 0.8)',   // primary
            'rgba(16, 185, 129, 0.8)',
            'rgba(52, 211, 153, 0.8)',
            'rgba(110, 231, 183, 0.8)',
            'rgba(167, 243, 208, 0.8)',
        ];

        new Chart(ctx, {
            type: isBarChart ? 'bar' : 'line',
            data: {
                labels: chartData.labels || [],
                datasets: [{
                    label: 'Total Booking',
                    data: chartData.values || [],
                    backgroundColor: isBarChart
                        ? chartData.values.map((_, i) => colors[i % colors.length])
                        : 'rgba(5, 150, 105, 0.2)',
                    borderColor: 'rgba(5, 150, 105, 1)',
                    borderWidth: isBarChart ? 0 : 2,
                    borderRadius: isBarChart ? 8 : 0,
                    tension: 0.4,
                    fill: !isBarChart,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>