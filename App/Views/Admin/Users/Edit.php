<?php
$validator = $validator ?? null;
?>

<div class="max-w-5xl mx-auto">
  <!-- Back Button -->
  <div class="mb-6">
    <a href="/admin/users" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold group">
      <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none"
        stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      Kembali ke Daftar User
    </a>
  </div>

  <!-- Page Header -->
  <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
    <div class="flex items-center justify-between mb-2">
      <h2 class="text-3xl font-bold text-slate-800 flex items-center">
        <svg class="w-8 h-8 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
        Edit User
      </h2>
      <span
        class="inline-flex px-4 py-2 rounded-lg font-semibold text-sm border-2 bg-amber-100 text-amber-800 border-amber-300">
        Edit Mode
      </span>
    </div>
    <p class="text-slate-600">Edit informasi user <strong><?= htmlspecialchars($user->nama ?? '') ?></strong></p>
  </div>

  <?php if ($message = flash('success')): ?>
    <div class="bg-emerald-50 border-2 border-emerald-200 text-emerald-700 px-6 py-4 rounded-xl mb-6">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <?php if ($message = flash('error')): ?>
    <div class="bg-red-50 border-2 border-red-200 text-red-700 px-6 py-4 rounded-xl mb-6">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">

      <!-- Quick Actions -->
      <div class="bg-white rounded-2xl shadow-lg p-8">
        <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
          <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
          Aksi Cepat
        </h3>
        <div class="flex flex-wrap gap-3">
          <!-- Reset Password -->
          <form method="post" action="/admin/users/reset-password" class="inline">
            <?= csrf_field() ?>
            <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
            <button type="submit"
              class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-xl hover:bg-blue-200 transition-all font-medium">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
              </svg>
              Reset Password
            </button>
          </form>

          <?php if ($user->status !== 'suspended'): ?>
            <!-- Suspend -->
            <form method="post" action="/admin/users/suspend" class="inline"
              onsubmit="return confirm('Yakin ingin suspend user ini?');">
              <?= csrf_field() ?>
              <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
              <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-xl hover:bg-red-200 transition-all font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
                Suspend User
              </button>
            </form>
          <?php else: ?>
            <!-- Unsuspend -->
            <form method="post" action="/admin/users/unsuspend" class="inline">
              <?= csrf_field() ?>
              <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
              <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-emerald-100 text-emerald-700 rounded-xl hover:bg-emerald-200 transition-all font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Unsuspend User
              </button>
            </form>
          <?php endif; ?>

          <?php if ($user->status === 'pending kubaca'): ?>
            <!-- Approve KuBaca -->
            <form method="post" action="/admin/users/approve-kubaca" class="inline">
              <?= csrf_field() ?>
              <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
              <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-emerald-100 text-emerald-700 rounded-xl hover:bg-emerald-200 transition-all font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Approve KuBaca
              </button>
            </form>

            <!-- Reject KuBaca -->
            <form method="post" action="/admin/users/reject-kubaca" class="inline"
              onsubmit="return confirm('Yakin ingin reject KuBaca user ini?');">
              <?= csrf_field() ?>
              <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
              <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-xl hover:bg-red-200 transition-all font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Reject KuBaca
              </button>
            </form>
          <?php endif; ?>
        </div>
      </div>

      <!-- Edit Form -->
      <form action="/admin/users/update" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="id_user" value="<?= $user->id_user ?>">

        <!-- Identity -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
          <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Identitas
          </h3>
          <div class="space-y-4">
            <!-- Name -->
            <div class="flex items-start p-4 bg-slate-50 rounded-xl">
              <svg class="w-5 h-5 mr-3 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-600 mb-2">Nama Lengkap</label>
                <input type="text" name="nama" value="<?= htmlspecialchars(old('nama') ?? $user->nama ?? '') ?>"
                  class="w-full px-4 py-3 border-2 <?= $validator?->hasError('nama') ? 'border-red-500' : 'border-gray-200' ?> rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                <?php if ($validator?->hasError('nama')): ?>
                  <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($validator->getFirstError('nama')) ?></p>
                <?php endif; ?>
              </div>
            </div>

            <!-- Email -->
            <div class="flex items-start p-4 bg-slate-50 rounded-xl">
              <svg class="w-5 h-5 mr-3 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-600 mb-2">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars(old('email') ?? $user->email ?? '') ?>"
                  class="w-full px-4 py-3 border-2 <?= $validator?->hasError('email') ? 'border-red-500' : 'border-gray-200' ?> rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                <?php if ($validator?->hasError('email')): ?>
                  <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($validator->getFirstError('email')) ?></p>
                <?php endif; ?>
              </div>
            </div>

            <!-- Phone & Jurusan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="flex items-start p-4 bg-slate-50 rounded-xl">
                <div class="flex-1">
                  <label class="block text-sm font-semibold text-slate-600 mb-2">Nomor HP</label>
                  <input type="text" name="nomor_hp" value="<?= htmlspecialchars($user->nomor_hp ?? '') ?>"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                </div>
              </div>
              <div class="flex items-start p-4 bg-slate-50 rounded-xl">
                <div class="flex-1">
                  <label class="block text-sm font-semibold text-slate-600 mb-2">Jurusan</label>
                  <input type="text" name="jurusan" value="<?= htmlspecialchars($user->jurusan ?? '') ?>"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Academic Identifier -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
          <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
            </svg>
            Identitas Akademik
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- NIM -->
            <div class="flex items-start p-4 bg-slate-50 rounded-xl">
              <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-600 mb-2">NIM (Mahasiswa)</label>
                <input type="text" name="nim" value="<?= htmlspecialchars($user->nim ?? '') ?>"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
              </div>
            </div>

            <!-- NIP -->
            <div class="flex items-start p-4 bg-slate-50 rounded-xl">
              <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-600 mb-2">NIP (Dosen/Pegawai)</label>
                <input type="text" name="nip" value="<?= htmlspecialchars($user->nip ?? '') ?>"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
              </div>
            </div>
          </div>
        </div>

        <!-- Access & Status -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
          <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            Akses & Status
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Role -->
            <div class="flex items-start p-4 bg-slate-50 rounded-xl">
              <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-600 mb-2">Role</label>
                <select name="id_role"
                  class="w-full px-4 py-3 border-2 <?= $validator?->hasError('id_role') ? 'border-red-500' : 'border-gray-200' ?> rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                  <option value="">Pilih Role</option>
                  <?php foreach ($roles as $role): ?>
                    <option value="<?= htmlspecialchars($role->id_role) ?>" <?= (int) (old('id_role') ?? $user->id_role ?? 0) === (int) $role->id_role ? 'selected' : '' ?>>
                      <?= htmlspecialchars($role->nama_role) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <?php if ($validator?->hasError('id_role')): ?>
                  <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($validator->getFirstError('id_role')) ?></p>
                <?php endif; ?>
              </div>
            </div>

            <!-- Status -->
            <div class="flex items-start p-4 bg-slate-50 rounded-xl">
              <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-600 mb-2">Status</label>
                <select name="status"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                  <?php foreach ($statuses as $status): ?>
                    <option value="<?= htmlspecialchars($status) ?>" <?= ($user->status ?? '') === $status ? 'selected' : '' ?>>
                      <?= htmlspecialchars(ucwords($status)) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <!-- Peringatan -->
            <div class="flex items-start p-4 bg-slate-50 rounded-xl">
              <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-600 mb-2">Peringatan</label>
                <input type="number" name="peringatan" min="0"
                  value="<?= htmlspecialchars((string) ($user->peringatan ?? 0)) ?>"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
              </div>
            </div>
          </div>
        </div>

        <!-- Security -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
          <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            Keamanan
          </h3>
          <p class="text-sm text-slate-500 mb-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
            💡 Kosongkan field password jika tidak ingin mengubah password saat ini.
          </p>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Password -->
            <div class="flex items-start p-4 bg-slate-50 rounded-xl">
              <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-600 mb-2">Password Baru</label>
                <input type="password" name="password" placeholder="Kosongkan jika tidak diubah"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
              </div>
            </div>

            <!-- Confirm Password -->
            <div class="flex items-start p-4 bg-slate-50 rounded-xl">
              <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-600 mb-2">Konfirmasi Password</label>
                <input type="password" name="confirm_password" placeholder="Ulangi password baru"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
              </div>
            </div>
          </div>

          <button type="submit"
            class="w-full mt-6 bg-primary text-white px-8 py-4 rounded-xl hover:bg-emerald-700 transition-all font-semibold shadow-lg hover:shadow-xl flex items-center justify-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Simpan Perubahan
          </button>
        </div>
      </form>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
      <!-- User Info Card -->
      <div
        class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-2xl shadow-lg p-6 border-2 border-emerald-200">
        <h3 class="font-bold text-emerald-800 mb-3 flex items-center">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          Info User
        </h3>
        <ul class="space-y-2 text-sm text-emerald-700">
          <li><strong>ID:</strong> <?= htmlspecialchars((string) $user->id_user) ?></li>
          <li><strong>Nama:</strong> <?= htmlspecialchars($user->nama ?? '-') ?></li>
          <li><strong>Status:</strong>
            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium
              <?php
              $statusClass = match ($user->status ?? '') {
                'active' => 'bg-emerald-200 text-emerald-800',
                'suspended' => 'bg-red-200 text-red-800',
                'pending kubaca' => 'bg-yellow-200 text-yellow-800',
                default => 'bg-gray-200 text-gray-800',
              };
              echo $statusClass;
              ?>">
              <?= htmlspecialchars(ucwords($user->status ?? '-')) ?>
            </span>
          </li>
          <li><strong>Peringatan:</strong> <?= htmlspecialchars((string) ($user->peringatan ?? 0)) ?>/3</li>
        </ul>
      </div>

      <!-- KuBaca Status -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <h3 class="font-bold text-slate-800 mb-3 flex items-center">
          <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
          </svg>
          KuBaca
        </h3>
        <?php if ($user->kubaca_img): ?>
          <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-3 text-sm text-emerald-700">
            ✅ KuBaca sudah diupload
          </div>
          <p class="text-xs text-slate-500 mt-2 break-all"><?= htmlspecialchars($user->kubaca_img) ?></p>
        <?php else: ?>
          <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-sm text-yellow-700">
            ⚠️ KuBaca belum diupload
          </div>
        <?php endif; ?>
      </div>

      <!-- Status Guide -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <h3 class="font-bold text-slate-800 mb-3 flex items-center">
          <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          Status Guide
        </h3>
        <ul class="space-y-3 text-sm text-slate-600">
          <li class="flex items-start">
            <span class="flex-shrink-0 w-3 h-3 rounded-full bg-emerald-500 mr-2 mt-1"></span>
            <span><strong>Active</strong> - User dapat login dan booking</span>
          </li>
          <li class="flex items-start">
            <span class="flex-shrink-0 w-3 h-3 rounded-full bg-yellow-500 mr-2 mt-1"></span>
            <span><strong>Pending KuBaca</strong> - Menunggu verifikasi</span>
          </li>
          <li class="flex items-start">
            <span class="flex-shrink-0 w-3 h-3 rounded-full bg-red-500 mr-2 mt-1"></span>
            <span><strong>Suspended</strong> - Tidak dapat login</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>