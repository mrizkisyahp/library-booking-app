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
            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
        </svg>
        Tambah User Baru
      </h2>
      <span
        class="inline-flex px-4 py-2 rounded-lg font-semibold text-sm border-2 bg-emerald-100 text-emerald-800 border-emerald-300">
        Create User
      </span>
    </div>
    <p class="text-slate-600">Tambahkan user baru ke sistem</p>
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

  <form action="/admin/users" method="post">
    <?= csrf_field() ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Main Content -->
      <div class="lg:col-span-2 space-y-6">

        <!-- Identity -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
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
                <input type="text" name="nama" value="<?= htmlspecialchars(old('nama') ?? '') ?>" required
                  placeholder="Masukkan nama lengkap"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
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
                <input type="email" name="email" value="<?= htmlspecialchars(old('email') ?? '') ?>" required
                  placeholder="contoh@email.com"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
              </div>
            </div>

            <!-- Phone & Jurusan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="flex items-start p-4 bg-slate-50 rounded-xl">
                <div class="flex-1">
                  <label class="block text-sm font-semibold text-slate-600 mb-2">Nomor HP</label>
                  <input type="text" name="nomor_hp" value="<?= htmlspecialchars(old('nomor_hp') ?? '') ?>"
                    placeholder="08xxxxxxxxxx"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                </div>
              </div>
              <div class="flex items-start p-4 bg-slate-50 rounded-xl">
                <div class="flex-1">
                  <label class="block text-sm font-semibold text-slate-600 mb-2">Jurusan</label>
                  <input type="text" name="jurusan" value="<?= htmlspecialchars(old('jurusan') ?? '') ?>"
                    placeholder="Teknik Informatika"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Academic Identifier -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
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
                <input type="text" name="nim" value="<?= htmlspecialchars(old('nim') ?? '') ?>"
                  placeholder="Contoh: 12345678"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
              </div>
            </div>

            <!-- NIP -->
            <div class="flex items-start p-4 bg-slate-50 rounded-xl">
              <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-600 mb-2">NIP (Dosen/Pegawai)</label>
                <input type="text" name="nip" value="<?= htmlspecialchars(old('nip') ?? '') ?>"
                  placeholder="Contoh: 198501012010011001"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
              </div>
            </div>
          </div>
          <p class="text-sm text-slate-500 mt-4">Isi salah satu sesuai jenis user (NIM untuk mahasiswa, NIP untuk
            dosen/pegawai)</p>
        </div>

        <!-- Access & Status -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
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
                <select name="id_role" required
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                  <option value="">Pilih Role</option>
                  <?php foreach ($roles as $role): ?>
                    <option value="<?= htmlspecialchars($role->id_role) ?>">
                      <?= htmlspecialchars($role->nama_role) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <!-- Status -->
            <div class="flex items-start p-4 bg-slate-50 rounded-xl">
              <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-600 mb-2">Status</label>
                <select name="status"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                  <?php foreach ($statuses as $status): ?>
                    <option value="<?= htmlspecialchars($status) ?>" <?= $status === 'active' ? 'selected' : '' ?>>
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
                <input type="number" name="peringatan" min="0" value="0"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
              </div>
            </div>
          </div>
        </div>

        <!-- Security -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
          <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            Keamanan
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Password -->
            <div class="flex items-start p-4 bg-slate-50 rounded-xl">
              <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-600 mb-2">Password</label>
                <input type="password" name="password" required placeholder="Minimal 8 karakter"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
              </div>
            </div>

            <!-- Confirm Password -->
            <div class="flex items-start p-4 bg-slate-50 rounded-xl">
              <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-600 mb-2">Konfirmasi Password</label>
                <input type="password" name="confirm_password" required placeholder="Ulangi password"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
              </div>
            </div>
          </div>

          <button type="submit"
            class="w-full mt-6 bg-primary text-white px-8 py-4 rounded-xl hover:bg-emerald-700 transition-all font-semibold shadow-lg hover:shadow-xl flex items-center justify-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Simpan User
          </button>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Info Card -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl shadow-lg p-6 border-2 border-blue-200">
          <h3 class="font-bold text-blue-800 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Informasi
          </h3>
          <ul class="space-y-2 text-sm text-blue-700">
            <li>• User akan mendapat email aktivasi</li>
            <li>• NIM/NIP harus unik</li>
            <li>• Password minimal 8 karakter</li>
            <li>• Email tidak boleh duplikat</li>
          </ul>
        </div>

        <!-- Role Guide -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h3 class="font-bold text-slate-800 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Role Guide
          </h3>
          <ul class="space-y-3 text-sm text-slate-600">
            <li class="flex items-start">
              <span class="flex-shrink-0 w-3 h-3 rounded-full bg-red-500 mr-2 mt-1"></span>
              <span><strong>Admin</strong> - Akses penuh ke sistem</span>
            </li>
            <li class="flex items-start">
              <span class="flex-shrink-0 w-3 h-3 rounded-full bg-blue-500 mr-2 mt-1"></span>
              <span><strong>Dosen/Tendik</strong> - Dapat booking ruangan</span>
            </li>
            <li class="flex items-start">
              <span class="flex-shrink-0 w-3 h-3 rounded-full bg-emerald-500 mr-2 mt-1"></span>
              <span><strong>Mahasiswa</strong> - Dapat booking dengan batasan</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </form>
</div>