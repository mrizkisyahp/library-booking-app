<div class="p-4 bg-white shadow-md w-full mb-6">
  <div class="flex items-center gap-4 py-4">
    <div class="flex items-center gap-4 ">
      <a href="/admin/users">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
          class="lucide lucide-chevron-left-icon lucide-chevron-left size-9">
          <path d="m15 18-6-6 6-6" />
        </svg>
      </a>
      <span class="text-black font-bold text-4xl">
        Kembali ke daftar user
      </span>
    </div>
  </div>
</div>

<div class="min-h-screen bg-gray-50 p-6">
  <div class="max-w-7xl mx-auto">
    <!-- Header -->
    <!-- Flash Messages -->
    <?php if ($message = flash('success')): ?>
      <div class="mb-6 bg-green-50 border-l-4 border-emerald-500 rounded-lg p-4 shadow-sm">
        <p class="text-emerald-800 font-medium"><?= htmlspecialchars($message) ?></p>
      </div>
    <?php endif; ?>
    <?php if ($message = flash('error')): ?>
      <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm">
        <p class="text-red-800 font-medium"><?= htmlspecialchars($message) ?></p>
      </div>
    <?php endif; ?>
    <!-- Main Grid: KuBaca Image Left, Form Right -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <!-- LEFT: KuBaca Image Block -->
      <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-6">
          <h3 class="text-lg font-bold text-gray-800 mb-4">KuBaca Image</h3>
          <?php if ($user->kubaca_img): ?>
            <div class="aspect-[3/4] bg-gray-100 rounded-lg overflow-hidden mb-4">
              <img src="/uploads/kubaca/<?= htmlspecialchars($user->kubaca_img) ?>"
                alt="KuBaca <?= htmlspecialchars($user->nama) ?>" class="w-full h-full object-contain">
            </div>
            <p class="text-sm text-gray-600 break-all">
              <span class="font-semibold">File:</span> <?= htmlspecialchars($user->kubaca_img) ?>
            </p>
          <?php else: ?>
            <div class="aspect-[3/4] bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
              <div class="text-center">
                <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-sm">Kubaca belum di-upload</p>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
      <!-- RIGHT: User Details & Actions -->
      <div class="lg:col-span-2 space-y-6">

        <!-- User Detail Header -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-start justify-between mb-4">
            <div>
              <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Detail User</p>
              <p class="text-gray-600 mt-1">Periksa detail booking sebelum melakukan verifikasi.</p>
            </div>
            <span class="px-4 py-2 rounded-full text-sm font-semibold
                <?= $user->status === 'pending kubaca' ? 'bg-yellow-100 text-yellow-800' : '' ?>
                <?= $user->status === 'active' ? 'bg-green-100 text-green-800' : '' ?>
                <?= $user->status === 'suspended' ? 'bg-red-100 text-red-800' : '' ?>
                <?= $user->status === 'rejected' ? 'bg-orange-100 text-orange-800' : '' ?>">
              <?= ucwords(htmlspecialchars($user->status)) ?>
            </span>
          </div>
        </div>
        <!-- Detail User -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Detail User
          </h2>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Nama User</p>
              <p class="font-semibold text-gray-900"><?= htmlspecialchars($user->nama) ?></p>
            </div>
            <div>
              <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Email</p>
              <p class="text-gray-700"><?= htmlspecialchars($user->email) ?></p>
            </div>
            <div>
              <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Nomor Telepon</p>
              <p class="text-gray-700"><?= htmlspecialchars($user->nomor_hp ?? '-') ?></p>
            </div>
            <div>
              <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Role User</p>
              <p class="text-gray-700"><?= $user->role?->nama_role ?></p>
            </div>
            <div>
              <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">NIM User</p>
              <p class="text-gray-700"><?= htmlspecialchars($user->nim ?? '-') ?></p>
            </div>
            <div>
              <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Jurusan</p>
              <p class="text-gray-700"><?= htmlspecialchars($user->jurusan ?? '-') ?></p>
            </div>
            <div>
              <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Jumlah Peringatan</p>
              <p class="text-gray-700"><?= (int) $user->peringatan ?></p>
            </div>
            <div>
              <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Masa Aktif</p>
              <p class="text-gray-700"><?= $user->masa_aktif ?></p>
            </div>
            <div class="col-span-2 pt-4 border-t">
              <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Status User</p>
              <p class="font-semibold text-gray-900"><?= ucwords(htmlspecialchars($user->status)) ?></p>

              <?php if ($user->kubaca_img): ?>
                <p class="text-sm text-gray-600 mt-1">
                  Bukti KuBaca PNJ: Uploaded: <span
                    class="font-mono text-xs bg-gray-100 px-2 py-1 rounded"><?= htmlspecialchars($user->kubaca_img) ?></span>
                </p>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <!-- Actions -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            </svg>
            Actions
          </h2>
          <!-- Edit User Button -->
          <div class="mb-4">
            <a href="/admin/users/edit?id_user=<?= $user->id_user ?>"
              class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition-colors shadow-md">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              Edit User
            </a>
          </div>
          <!-- KuBaca Approval Section -->
          <?php if ($user->status === 'pending kubaca'): ?>
            <!-- Buttons -->
            <div class="grid grid-cols-2 gap-3 mb-3">
              <!-- Approve Button - LEFT (opens modal) -->
              <a href="/admin/users/show?id=<?= $user->id_user ?>#modal-masa-aktif"
                class="w-full px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold transition-colors shadow-md text-center cursor-pointer">
                Approve KuBaca
              </a>
              <!-- Reject Form - RIGHT -->
              <form method="post" action="/admin/users/reject-kubaca" id="rejectForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
                <button type="submit"
                  class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold transition-colors shadow-md">
                  Reject KuBaca
                </button>
              </form>
            </div>
            <!-- Reason Field (part of reject form) -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2" for="reject-reason">Alasan</label>
              <input type="text" name="reason" id="reject-reason" form="rejectForm" required
                class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:outline-none transition-all">
            </div>
            <!-- Modal for Masa Aktif -->
            <div id="modal-masa-aktif"
              class="fixed inset-0 bg-black/50 opacity-0 pointer-events-none duration-300 transition-all target:opacity-100 target:pointer-events-auto flex justify-center items-center z-999">

              <div
                class="bg-white p-6 rounded-2xl w-11/12 max-w-md shadow-lg scale-95 transition-all duration-300 target:scale-100 relative">
                <a href="/admin/users/show?id=<?= $user->id_user ?>"
                  class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-xl font-bold cursor-pointer">
                  &times;
                </a>
                <h1 class="text-lg font-bold text-slate-800 mb-2">
                  Set Masa Aktif KuBaca
                </h1>
                <p class="text-sm text-slate-600 mb-4">
                  Tentukan tanggal masa aktif KuBaca untuk user ini
                </p>
                <form action="/admin/users/approve-kubaca" method="post" class="space-y-3">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Masa Aktif Sampai</label>
                    <input type="date" name="masa_aktif" required
                      class="w-full px-3 py-2 text-sm border-2 border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500">
                  </div>
                  <button type="submit"
                    class="w-full bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition-all font-semibold text-sm shadow cursor-pointer">
                    Approve KuBaca
                  </button>
                </form>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>