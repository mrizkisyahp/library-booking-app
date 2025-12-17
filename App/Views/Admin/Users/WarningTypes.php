<?php

use App\Core\App;

$warningTypes = $warningTypes ?? [];
$paginator = $paginator ?? null;
?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Jenis Peringatan</h1>
            <p class="text-slate-600">Kelola jenis peringatan untuk pengguna</p>
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

    <?php if ($message = App::$app->session->getFlash('error')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Add New Warning Type -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-slate-800 mb-4">Tambah Jenis Peringatan</h2>
            <form action="/admin/users/warning-types/store" method="POST">
                <input type="hidden" name="csrf_token" value="<?= App::$app->session->get('csrf_token') ?>">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Peringatan</label>
                    <input type="text" name="nama_peringatan" required placeholder="Contoh: No Show"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                </div>

                <button type="submit"
                    class="w-full bg-emerald-600 text-white py-3 rounded-xl hover:bg-emerald-700 transition font-medium">
                    Tambah Jenis Peringatan
                </button>
            </form>
        </div>

        <!-- Existing Warning Types -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-slate-800 mb-4">Daftar Jenis Peringatan (<?= $paginator->total ?? count($warningTypes) ?>)</h2>

            <?php if (empty($warningTypes)): ?>
                <div class="text-center py-8">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="text-gray-600">Belum ada jenis peringatan</p>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($warningTypes as $type): ?>
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                            <div class="flex items-center">
                                <span
                                    class="w-8 h-8 flex items-center justify-center bg-yellow-100 text-yellow-600 rounded-full text-sm font-bold mr-3">
                                    <?= $type['id_peringatan'] ?>
                                </span>
                                <span
                                    class="font-medium text-slate-800"><?= htmlspecialchars($type['nama_peringatan']) ?></span>
                            </div>
                            <div class="flex gap-2">
                                <!-- Edit Button -->
                                <button
                                    onclick="editType(<?= $type['id_peringatan'] ?>, '<?= htmlspecialchars($type['nama_peringatan'], ENT_QUOTES) ?>')"
                                    class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <!-- Delete Button -->
                                <form action="/admin/users/warning-types/delete" method="POST" class="inline"
                                    onsubmit="return confirm('Hapus jenis peringatan ini?')">
                                    <input type="hidden" name="csrf_token" value="<?= App::$app->session->get('csrf_token') ?>">
                                    <input type="hidden" name="id" value="<?= $type['id_peringatan'] ?>">
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($paginator && $paginator->lastPage > 1): ?>
                    <div class="mt-6 flex flex-wrap gap-2 justify-center">
                        <?php if ($paginator->currentPage > 1): ?>
                            <a href="/admin/users/warning-types?page=<?= $paginator->currentPage - 1 ?>"
                                class="px-3 py-1 border border-slate-300 rounded-lg text-sm hover:bg-slate-50">← Prev</a>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $paginator->lastPage; $i++): ?>
                            <a href="/admin/users/warning-types?page=<?= $i ?>" 
                                class="px-3 py-1 rounded-lg text-sm <?= $i === $paginator->currentPage ? 'bg-emerald-600 text-white' : 'border border-slate-300 hover:bg-slate-50' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        <?php if ($paginator->currentPage < $paginator->lastPage): ?>
                            <a href="/admin/users/warning-types?page=<?= $paginator->currentPage + 1 ?>"
                                class="px-3 py-1 border border-slate-300 rounded-lg text-sm hover:bg-slate-50">Next →</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-xl font-bold text-slate-800 mb-4">Edit Jenis Peringatan</h3>
        <form action="/admin/users/warning-types/update" method="POST">
            <input type="hidden" name="csrf_token" value="<?= App::$app->session->get('csrf_token') ?>">
            <input type="hidden" name="id" id="editId">

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Peringatan</label>
                <input type="text" name="nama_peringatan" id="editName" required
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeModal()"
                    class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-xl hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 bg-emerald-600 text-white py-3 rounded-xl hover:bg-emerald-700 transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function editType(id, name) {
        document.getElementById('editId').value = id;
        document.getElementById('editName').value = name;
        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('editModal').classList.add('flex');
    }

    function closeModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.getElementById('editModal').classList.remove('flex');
    }

    // Close modal on backdrop click
    document.getElementById('editModal').addEventListener('click', function (e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>