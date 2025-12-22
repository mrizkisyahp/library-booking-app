<?php
$validator = $validator ?? null;

$statusOptions = [
  'available' => 'Available',
  'unavailable' => 'Unavailable',
  'adminOnly' => 'Admin Only',
];
$roomTypes = [
  'Audio Visual',
  'Telekonferensi',
  'Kreasi dan Rekreasi',
  'Baca Kelompok',
  'Koleksi Bahasa Prancis',
  'Bimbingan & Konseling',
  'Ruang Rapat',
];
?>

<div class="max-w-5xl mx-auto">
  <!-- Back Button -->
  <div class="mb-6">
    <a href="/admin/rooms" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold group">
      <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none"
        stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      <span class="text-black font-bold text-xl md:text-4xl">
        Kembali ke Daftar Ruangan
      </span>
    </a>
  </div>

  <!-- Page Header -->
  <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
    <div class="flex items-center justify-between mb-2">
      <h2 class="text-3xl font-bold text-slate-800 flex items-center">
        <svg class="w-8 h-8 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Tambah Ruangan Baru
      </h2>
      <span
        class="inline-flex px-4 py-2 rounded-lg font-semibold text-sm border-2 bg-emerald-100 text-emerald-800 border-emerald-300">
        Create Room
      </span>
    </div>
    <p class="text-slate-600">Tambahkan ruangan baru ke sistem</p>
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

  <form action="/admin/rooms" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Main Content -->
      <div class="lg:col-span-2 space-y-6">

        <!-- Basic Information -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
          <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            Informasi Dasar
          </h3>
          <div class="space-y-4">
            <!-- Room Name -->
            <div class="flex items-start p-4 bg-slate-50 rounded-xl">
              <svg class="w-5 h-5 mr-3 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
              </svg>
              <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-600 mb-2">Nama Ruangan</label>
                <input type="text" name="nama_ruangan" value="<?= htmlspecialchars(old('nama_ruangan') ?? '') ?>"
                  placeholder="Contoh: Ruang Diskusi A"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                <?php if ($validator?->hasError('nama_ruangan')): ?>
                  <p class="text-red-500 text-sm mt-2"><?= htmlspecialchars($validator?->getFirstError('nama_ruangan')) ?>
                  </p>
                <?php endif; ?>
              </div>
            </div>

            <!-- Room Type -->
            <div class="flex items-start p-4 bg-slate-50 rounded-xl">
              <svg class="w-5 h-5 mr-3 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
              </svg>
              <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-600 mb-2">Jenis Ruangan</label>
                <select name="jenis_ruangan"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                  <option value="">-- Pilih Jenis --</option>
                  <?php foreach ($roomTypes as $type): ?>
                    <option value="<?= htmlspecialchars($type) ?>" <?= (old('jenis_ruangan') ?? '') === $type ? 'selected' : '' ?>>
                      <?= htmlspecialchars($type) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <?php if ($validator?->hasError('jenis_ruangan')): ?>
                  <p class="text-red-500 text-sm mt-2">
                    <?= htmlspecialchars($validator?->getFirstError('jenis_ruangan')) ?>
                  </p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Room Photos -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
          <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Foto Ruangan
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php for ($i = 1; $i <= 4; $i++): ?>
              <div class="p-4 bg-slate-50 rounded-xl">
                <label class="block text-sm font-semibold text-slate-600 mb-2">
                  <?= $i === 1 ? 'Foto Utama (Thumbnail)' : "Foto Tambahan {$i}" ?>
                </label>
                <div class="relative group mb-3 hidden" id="preview-container-<?= $i ?>">
                  <img id="preview-<?= $i ?>"
                    class="w-full h-48 object-cover rounded-lg border border-gray-200 shadow-sm">
                  <button type="button" onclick="removeImage(<?= $i ?>)"
                    class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>

                <input type="file" name="image_<?= $i ?>" id="image_input_<?= $i ?>"
                  accept="image/png, image/jpeg, image/jpg, image/webp" onchange="previewImage(this, <?= $i ?>)" class="w-full text-sm text-slate-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-emerald-50 file:text-emerald-700
                            hover:file:bg-emerald-100
                            transition-all cursor-pointer">
                <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG, WebP. Max 2MB.</p>
              </div>
            <?php endfor; ?>
          </div>
        </div>

        <!-- Capacity -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
          <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Kapasitas
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Min Capacity -->
            <div class="flex items-start p-4 bg-slate-50 rounded-xl">
              <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-600 mb-2">Kapasitas Minimum</label>
                <input type="number" name="kapasitas_min" min="1"
                  value="<?= htmlspecialchars(old('kapasitas_min') ?? '') ?>" placeholder="Contoh: 2"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                <?php if ($validator?->hasError('kapasitas_min')): ?>
                  <p class="text-red-500 text-sm mt-2">
                    <?= htmlspecialchars($validator?->getFirstError('kapasitas_min')) ?>
                  </p>
                <?php endif; ?>
              </div>
            </div>

            <!-- Max Capacity -->
            <div class="flex items-start p-4 bg-slate-50 rounded-xl">
              <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-600 mb-2">Kapasitas Maksimum</label>
                <input type="number" name="kapasitas_max" min="1"
                  value="<?= htmlspecialchars(old('kapasitas_max') ?? '') ?>" placeholder="Contoh: 10"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                <?php if ($validator?->hasError('kapasitas_max')): ?>
                  <p class="text-red-500 text-sm mt-2">
                    <?= htmlspecialchars($validator?->getFirstError('kapasitas_max')) ?>
                  </p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Description -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
          <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Deskripsi & Fasilitas
          </h3>
          <div class="flex items-start p-4 bg-slate-50 rounded-xl">
            <div class="flex-1">
              <label class="block text-sm font-semibold text-slate-600 mb-2">Deskripsi Ruangan</label>
              <textarea name="deskripsi_ruangan" rows="4" placeholder="Fasilitas: AC, Proyektor, Whiteboard..."
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all resize-none"><?= htmlspecialchars(old('deskripsi_ruangan') ?? '') ?></textarea>
              <?php if ($validator?->hasError('deskripsi_ruangan')): ?>
                <p class="text-red-500 text-sm mt-2">
                  <?= htmlspecialchars($validator?->getFirstError('deskripsi_ruangan')) ?>
                </p>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Status & Submit -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
          <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Status
          </h3>
          <div class="flex items-start p-4 bg-slate-50 rounded-xl mb-6">
            <div class="flex-1">
              <label class="block text-sm font-semibold text-slate-600 mb-2">Status Ruangan</label>
              <select name="status_ruangan"
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                <?php foreach ($statusOptions as $status => $label): ?>
                  <option value="<?= htmlspecialchars($status) ?>" <?= (old('status_ruangan') ?? 'available') === $status ? 'selected' : '' ?>>
                    <?= htmlspecialchars($label) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <?php if ($validator?->hasError('status_ruangan')): ?>
                <p class="text-red-500 text-sm mt-2"><?= htmlspecialchars($validator?->getFirstError('status_ruangan')) ?>
                </p>
              <?php endif; ?>
            </div>
          </div>

          <button type="submit"
            class="w-full bg-primary text-white px-8 py-4 rounded-xl hover:bg-emerald-700 transition-all font-semibold shadow-lg hover:shadow-xl flex items-center justify-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Simpan Ruangan
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
            <li>• Nama ruangan harus unik</li>
            <li>• Kapasitas min ≤ max</li>
            <li>• Deskripsi bisa berisi fasilitas</li>
            <li>• Status menentukan ketersediaan</li>
          </ul>
        </div>

        <!-- Status Info -->
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
              <span><strong>Available</strong> - Dapat dibooking semua user</span>
            </li>
            <li class="flex items-start">
              <span class="flex-shrink-0 w-3 h-3 rounded-full bg-yellow-500 mr-2 mt-1"></span>
              <span><strong>Admin Only</strong> - Hanya admin yang bisa booking</span>
            </li>
            <li class="flex items-start">
              <span class="flex-shrink-0 w-3 h-3 rounded-full bg-red-500 mr-2 mt-1"></span>
              <span><strong>Unavailable</strong> - Tidak dapat dibooking</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
function previewImage(input, index) {
    const preview = document.getElementById('preview-' + index);
    const container = document.getElementById('preview-container-' + index);
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.classList.remove('hidden');
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        container.classList.add('hidden');
    }
}

function removeImage(index) {
    const input = document.getElementById('image_input_' + index);
    const container = document.getElementById('preview-container-' + index);
    
    input.value = ''; // Clear input
    container.classList.add('hidden');
}
</script>