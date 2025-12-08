<div class="min-h-dvh">

    <div class="rounded-2xl p-4 mx-auto max-w-7xl">

        <!-- Header -->
        <div class="flex items-center mt-6 px-6 mb-6 md:bg-primary md:rounded-2xl md:p-6">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">Laporan Booking</h1>
                <p class="text-white">Pantau aktivitas peminjaman ruangan secara lengkap</p>
            </div>
        </div>


        <!-- FILTER PANEL -->
        <form method="get" action="/admin/report"
            class="grid grid-cols-1 gap-4 md:grid-cols-4 bg-white p-6 rounded-2xl shadow">

            <!-- Start Date -->
            <div>
                <label class="block text-sm text-slate-600 mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" value="<?= $filters['start_date'] ?? '' ?>"
                    class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl bg-slate-50">
            </div>

            <!-- End Date -->
            <div>
                <label class="block text-sm text-slate-600 mb-2">Tanggal Akhir</label>
                <input type="date" name="end_date" value="<?= $filters['end_date'] ?? '' ?>"
                    class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl bg-slate-50">
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm text-slate-600 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl bg-slate-50">
                    <option value="">Semua</option>
                    <?php
                    $statuses = ['active', 'completed', 'cancelled'];
                    foreach ($statuses as $st):
                    ?>
                        <option value="<?= $st ?>" <?= ($filters['status'] ?? '') === $st ? 'selected' : '' ?>>
                            <?= ucfirst($st) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Button -->
            <div class="flex items-end">
                <button class="w-full bg-primary text-white px-4 py-3 rounded-xl hover:bg-emerald-700 transition">
                    Terapkan
                </button>
            </div>

        </form>

        <!-- SUMMARY BOX -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">

            <div class="p-6 rounded-2xl bg-white shadow border">
                <h3 class="text-sm text-gray-500">Total Booking</h3>
                <p class="text-3xl font-bold text-primary mt-1">100</p>
            </div>

            <div class="p-6 rounded-2xl bg-white shadow border">
                <h3 class="text-sm text-gray-500">Selesai</h3>
                <p class="text-3xl font-bold text-emerald-600 mt-1">666</p>
            </div>

            <div class="p-6 rounded-2xl bg-white shadow border">
                <h3 class="text-sm text-gray-500">Dibatalkan</h3>
                <p class="text-3xl font-bold text-red-500 mt-1">999</p>
            </div>

        </div>

        <!-- CHART -->
        <div class="bg-white shadow rounded-2xl p-6 mt-6">
            <h2 class="text-xl font-bold mb-4">Grafik Aktivitas Booking</h2>

            <canvas id="bookingChart" class="w-full h-72"></canvas>

            <script>
                window.chartData = <?= json_encode($chartData) ?>;
            </script>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script src="/js/report-chart.js"></script>
        </div>

        <!-- EXPORT BUTTONS -->
        <div class="flex gap-3 mt-6">
            <a href="/admin/report/export?type=csv"
               class="px-4 py-3 bg-primary text-white rounded-xl shadow hover:bg-emerald-700 transition">
               Export CSV
            </a>

            <a href="/admin/report/export?type=pdf"
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
                    <?php
                        $report = [4];
                        foreach ($report as $row): ?>
                        <tr class="border-b hover:bg-slate-50">
                            <td class="py-3 px-3">ABCDEFU</td>
                            <td class="py-3 px-3">Gayle</td>
                            <td class="py-3 px-3">Room for happiness</td>
                            <td class="py-3 px-3 capitalize">Single</td>
                            <td class="py-3 px-3">1945</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>

    </div>
</div>
