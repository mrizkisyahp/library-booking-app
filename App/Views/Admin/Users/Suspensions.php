<?php

use App\Core\App;

$suspensions = $suspensions ?? [];
$paginator = $paginator ?? null;
?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Suspensi Pengguna</h1>
            <p class="text-slate-600">Daftar pengguna yang pernah mendapat suspensi</p>
        </div>
        <a href="/admin/users" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Users
        </a>
    </div>

    <?php if ($message = App::$app->session->getFlash('success')): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow p-6 border-l-4 border-red-500">
            <h3 class="text-sm text-gray-500">Total Suspensi</h3>
            <p class="text-3xl font-bold text-red-600 mt-1"><?= $paginator->total ?? count($suspensions) ?></p>
        </div>
        <div class="bg-white rounded-2xl shadow p-6 border-l-4 border-slate-500">
            <h3 class="text-sm text-gray-500">Halaman</h3>
            <p class="text-3xl font-bold text-slate-600 mt-1"><?= $paginator->currentPage ?? 1 ?> /
                <?= $paginator->lastPage ?? 1 ?></p>
        </div>
    </div>

    <!-- Suspensions Table -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <?php if (empty($suspensions)): ?>
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-gray-600">Tidak ada data suspensi</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-slate-200">
                            <th class="text-left py-3 px-4 font-semibold text-slate-700">Tanggal Suspensi</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-700">Pengguna</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-700">Status Akun</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($suspensions as $suspension): ?>
                            <tr class="border-b border-slate-100 hover:bg-slate-50">
                                <td class="py-4 px-4">
                                    <span class="text-slate-800 font-medium">
                                        <?= date('d M Y', strtotime($suspension['tgl_suspensi'])) ?>
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <?php if (!empty($suspension['nama'])): ?>
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center mr-3">
                                                <span class="text-red-600 font-semibold text-sm">
                                                    <?= strtoupper(substr($suspension['nama'], 0, 1)) ?>
                                                </span>
                                            </div>
                                            <div>
                                                <p class="font-medium text-slate-800"><?= htmlspecialchars($suspension['nama']) ?>
                                                </p>
                                                <p class="text-sm text-slate-500"><?= htmlspecialchars($suspension['email']) ?></p>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">User tidak ditemukan</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-4">
                                    <?php $status = $suspension['user_status'] ?? ''; ?>
                                    <?php if ($status === 'suspended'): ?>
                                        <span
                                            class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-700">
                                            Suspended
                                        </span>
                                    <?php elseif ($status === 'active'): ?>
                                        <span
                                            class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-700">
                                            Aktif
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-700">
                                            <?= ucfirst($status ?: 'Unknown') ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($paginator && $paginator->lastPage > 1): ?>
                <div class="bg-white rounded-2xl shadow-lg p-6 mt-6">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-slate-600">
                            Menampilkan <span
                                class="font-semibold text-slate-800"><?= (($paginator->currentPage - 1) * $paginator->perPage) + 1 ?></span>
                            sampai <span
                                class="font-semibold text-slate-800"><?= min($paginator->currentPage * $paginator->perPage, $paginator->total) ?></span>
                            dari <span class="font-semibold text-slate-800"><?= $paginator->total ?></span> suspensi
                        </p>
                        <div class="flex gap-2 items-center">
                            <!-- First Page -->
                            <?php if ($paginator->currentPage > 1): ?>
                                <a href="/admin/users/suspensions?page=1"
                                    class="px-4 py-2 border-2 border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                    Awal
                                </a>
                            <?php endif; ?>
                            <!-- Previous -->
                            <?php if ($paginator->currentPage > 1): ?>
                                <a href="/admin/users/suspensions?page=<?= $paginator->currentPage - 1 ?>"
                                    class="px-4 py-2 border-2 border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                    ← Sebelumnya
                                </a>
                            <?php endif; ?>
                            <!-- Page Numbers -->
                            <div class="flex gap-1">
                                <?php for ($i = max(1, $paginator->currentPage - 2); $i <= min($paginator->lastPage, $paginator->currentPage + 2); $i++): ?>
                                    <a href="/admin/users/suspensions?page=<?= $i ?>" class="w-10 h-10 flex items-center justify-center rounded-xl text-sm font-semibold transition-all
                                        <?= $i === $paginator->currentPage
                                            ? 'bg-emerald-600 text-white shadow-md'
                                            : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>
                            </div>
                            <!-- Next -->
                            <?php if ($paginator->currentPage < $paginator->lastPage): ?>
                                <a href="/admin/users/suspensions?page=<?= $paginator->currentPage + 1 ?>"
                                    class="px-4 py-2 border-2 border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                    Selanjutnya →
                                </a>
                            <?php endif; ?>
                            <!-- Last Page -->
                            <?php if ($paginator->currentPage < $paginator->lastPage): ?>
                                <a href="/admin/users/suspensions?page=<?= $paginator->lastPage ?>"
                                    class="px-4 py-2 border-2 border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                    Akhir
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>