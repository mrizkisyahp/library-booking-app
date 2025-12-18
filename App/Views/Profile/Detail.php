<!-- Back Button -->
<div class="p-4 bg-white shadow-md w-full">
    <div class="flex items-center gap-4 py-4">
        <div class="flex items-center gap-4 ">
            <a href="/profile">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-chevron-left-icon lucide-chevron-left size-9">
                    <path d="m15 18-6-6 6-6" />
                </svg>
            </a>
            <span class="text-black font-bold text-4xl">
                Detail Akun
            </span>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto p-6">
    <!-- Header -->

    <?php if ($user): ?>
        <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100 mb-6">
            <div class="mb-8">
                <p class="text-gray-600">Informasi lengkap akun Anda</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Nama</label>
                    <p class="text-gray-900 font-medium select-all"><?= htmlspecialchars($user->nama) ?></p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Email</label>
                    <p class="text-gray-900 font-medium select-all"><?= htmlspecialchars($user->email) ?></p>
                </div>
                <?php if ($user->nim): ?>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">NIM</label>
                        <p class="text-gray-900 font-medium select-all"><?= htmlspecialchars($user->nim) ?></p>
                    </div>
                <?php endif; ?>
                <?php if ($user->nip): ?>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">NIP</label>
                        <p class="text-gray-900 font-medium select-all"><?= htmlspecialchars($user->nip) ?></p>
                    </div>
                <?php endif; ?>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Jurusan</label>
                    <p class="text-gray-900 font-medium select-all"><?= htmlspecialchars($user->jurusan) ?></p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Nomor Telepon</label>
                    <p class="text-gray-900 font-medium select-all"><?= htmlspecialchars($user->nomor_hp) ?></p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Peringatan</label>
                    <span
                        class="inline-block px-3 py-1 <?= $user->peringatan > 0 ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' ?> text-sm font-semibold rounded-full">
                        <?= htmlspecialchars($user->peringatan) ?> peringatan
                    </span>
                    <?php if (!empty($userWarnings) && count($userWarnings) > 0): ?>
                        <div class="mt-3 space-y-2">
                            <?php foreach ($userWarnings as $warning): ?>
                                <div class="flex items-center gap-2 p-2 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <svg class="w-4 h-4 text-yellow-500 shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div class="flex-1">
                                        <span
                                            class="text-sm font-medium text-yellow-800"><?= htmlspecialchars($warning['nama_peringatan'] ?? 'Peringatan') ?></span>
                                        <span
                                            class="text-xs text-yellow-600 ml-2"><?= date('d M Y', strtotime($warning['tgl_peringatan'])) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Role</label>
                    <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 text-sm font-semibold rounded-full">
                        <?= htmlspecialchars($user->nama_role ?? 'Unknown') ?>
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Status</label>
                    <span
                        class="inline-block px-3 py-1 <?= $user->status === 'active' ? 'bg-green-100 text-green-800' : ($user->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?> text-sm font-semibold rounded-full">
                        <?= htmlspecialchars(ucfirst($user->status)) ?>
                    </span>
                    <?php if ($user->status === 'rejected' && !empty($user->alasan_reject)): ?>
                        <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-xs font-semibold text-red-700 mb-1">Alasan Penolakan:</p>
                            <p class="text-sm text-red-800"><?= htmlspecialchars($user->alasan_reject) ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if ($user->status === 'suspended' && !empty($user->suspensi_terakhir)): ?>
                        <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-xs font-semibold text-red-700 mb-1">Disuspend sampai:</p>
                            <p class="text-sm text-red-800"><?= date('d M Y', strtotime($user->suspensi_terakhir)) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- <a href="/profile/edit"
            class="flex items-center w-full gap-4 justify-center px-8 py-4 rounded-2xl hover:bg-emerald-700 bg-emerald-600 md:bg-primary shadow-md text-white transition-colors cursor-pointer focus:outline-none focus:ring-2 focus:bg-emerald-600 focus:ring-emerald-500 focus:ring-offset-2">
            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Ubah data akun
        </a> -->

    <?php endif; ?>
</div>
