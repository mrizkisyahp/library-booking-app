<div class="max-w-4xl mx-auto p-6">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="/profile" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold group">
            <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Profile
        </a>
    </div>

    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Detail Akun</h2>
        <p class="text-gray-600">Informasi lengkap akun Anda</p>
    </div>

    <?php if ($user): ?>
        <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Nama</label>
                    <p class="text-gray-900 font-medium"><?= htmlspecialchars($user->nama) ?></p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Email</label>
                    <p class="text-gray-900 font-medium"><?= htmlspecialchars($user->email) ?></p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Role</label>
                    <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 text-sm font-semibold rounded-full">
                        <?= htmlspecialchars($user->nama_role ?? 'Unknown') ?>
                    </span>
                </div>
                <?php if ($user->nim): ?>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">NIM</label>
                        <p class="text-gray-900 font-medium"><?= htmlspecialchars($user->nim) ?></p>
                    </div>
                <?php endif; ?>
                <?php if ($user->nip): ?>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">NIP</label>
                        <p class="text-gray-900 font-medium"><?= htmlspecialchars($user->nip) ?></p>
                    </div>
                <?php endif; ?>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Status</label>
                    <span
                        class="inline-block px-3 py-1 <?= $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?> text-sm font-semibold rounded-full">
                        <?= htmlspecialchars(ucfirst($user->status)) ?>
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Peringatan</label>
                    <span
                        class="inline-block px-3 py-1 <?= $user->peringatan > 0 ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' ?> text-sm font-semibold rounded-full">
                        <?= htmlspecialchars($user->peringatan) ?>
                    </span>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>