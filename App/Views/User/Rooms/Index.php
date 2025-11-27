<?php
use App\Core\Csrf;
use App\Models\Room;
use App\Models\User;
/** @var Room[] $rooms */
/** @var array $filters */
/** @var User $user */

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


<div class="min-h-screen bg-linear-to-br from-slate-50 to-slate-100">
  <div class="max-w-7xl mx-auto px-6 py-12">
    <!-- Header -->
    <div class="mb-8">
      <h2 class="text-4xl font-bold text-slate-800 mb-2">Temukan Ruangan</h2>
      <p class="text-slate-600">Cari ruangan yang sesuai dengan kebutuhan Anda</p>
    </div>

    <!-- Filter Form -->
    <form method="get" action="/rooms" class="bg-white rounded-2xl shadow-lg p-8 mb-10">
      <!-- Keyword Search -->
      <div class="mb-6">
        <label class="block text-sm font-semibold text-slate-700 mb-3">
          <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          Cari Nama Ruangan
        </label>
        <input type="text" name="nama_ruangan" value="<?= htmlspecialchars($filters['nama_ruangan'] ?? '') ?>" placeholder="Cari berdasarkan nama ruangan..." class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-3">
            <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Kapasitas Minimum
          </label>
          <input type="number" name="kapasitas_min" value="<?= htmlspecialchars($filters['kapasitas_min'] ?? '') ?>" min="1" placeholder="Misal: 10" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-3">
            <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Kapasitas Maksimum
          </label>
          <input type="number" name="kapasitas_max" value="<?= htmlspecialchars($filters['kapasitas_max'] ?? '') ?>" min="1" placeholder="Misal: 50" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-3">
            <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            Jenis Ruangan
          </label>
          <select name="jenis_ruangan" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
            <option value="">Semua Jenis</option>
            <?php foreach ($roomTypes as $type): ?>
              <option value="<?= htmlspecialchars($type) ?>" <?= ($filters['jenis_ruangan'] ?? '') === $type ? 'selected' : '' ?>>
                <?= htmlspecialchars($type) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="flex gap-3 items-center justify-end">
        <button type="submit" class="bg-primary text-white px-8 py-3 rounded-xl hover:bg-emerald-700 transition-all font-semibold shadow-lg hover:shadow-xl">
          <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          Cari Ruangan
        </button>
        <?php if (!empty($filters['nama_ruangan']) || !empty($filters['kapasitas_min']) || !empty($filters['kapasitas_max']) || !empty($filters['jenis_ruangan'])): ?>
          <a href="/rooms" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-xl hover:bg-gray-300 transition-all font-semibold">
            Reset Filter
          </a>
        <?php endif; ?>
      </div>
    </form>

    <?php if ($user->status === 'pending kubaca' || $user->status === 'rejected'): ?> 
      <!-- Overlay Blocking Message -->
      <div class="relative mb-8">
        <div class="bg-linear-to-br from-amber-50 to-orange-50 rounded-2xl shadow-xl border-2 border-amber-200 p-8 relative overflow-hidden">
          <!-- Decorative background pattern -->
          <div class="absolute inset-0 opacity-5">
            <div class="absolute transform rotate-12 -right-10 -top-10">
              <svg class="w-40 h-40 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
              </svg>
            </div>
          </div>
          
          <div class="relative flex items-start gap-6">
            <!-- Icon -->
            <div class="shrink-0">
              <?php if ($user->status === 'pending kubaca'): ?>
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center animate-pulse">
                  <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
              <?php else: ?>
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                  <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                </div>
              <?php endif; ?>
            </div>
            
            <!-- Content -->
            <div class="flex-1">
              <?php if ($user->status === 'pending kubaca'): ?>
                <h3 class="text-2xl font-bold text-amber-900 mb-2">Akun Anda Sedang Dalam Verifikasi</h3>
                <p class="text-amber-800 mb-4 leading-relaxed">
                  Terima kasih telah mendaftar! Akun Anda sedang menunggu verifikasi dari admin. 
                  Anda dapat melihat ruangan yang tersedia, namun belum dapat melakukan pemesanan.
                </p>
                <div class="flex items-center gap-2 text-sm text-amber-700 bg-amber-100 rounded-lg px-4 py-2 inline-flex">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span class="font-semibold">Status:</span> Menunggu Persetujuan Admin
                </div>
              <?php else: ?>
                <h3 class="text-2xl font-bold text-red-900 mb-2">Akun Anda Ditolak</h3>
                <p class="text-red-800 mb-4 leading-relaxed">
                  Maaf, verifikasi akun Anda tidak berhasil. Silahkan reupload kembali kubaca di profile
                </p>
                <div class="flex items-center gap-2 text-sm text-red-700 bg-red-100 rounded-lg px-4 py-2 inline-flex">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span class="font-semibold">Status:</span> Ditolak
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
    
    <!-- Room List -->
    <div class="room-list <?= ($user->status === 'pending kubaca' || $user->status === 'rejected') ? 'opacity-75' : '' ?>">
      <?php if (empty($rooms)): ?>
        <div class="bg-white rounded-2xl shadow-lg p-16 text-center">
          <svg class="w-24 h-24 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <h3 class="text-xl font-semibold text-slate-700 mb-2">Tidak Ada Ruangan Ditemukan</h3>
          <p class="text-slate-500">Coba ubah filter pencarian Anda</p>
        </div>
      <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <?php foreach ($rooms as $room): ?>
            <?php $thumbnail = $room->getThumbnail(); ?>
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 relative">
              <!-- Blocked Overlay for rejected/pending users -->
              <?php if ($user->status === 'pending kubaca' || $user->status === 'rejected'): ?>
                <div class="absolute top-4 right-4 z-0">
                  <div class="bg-slate-900/90 backdrop-blur-sm text-white px-3 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5 shadow-lg">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    View Only
                  </div>
                </div>
              <?php endif; ?>
              
              <div class="flex">
                <?php if ($thumbnail): ?>
                  <img src="<?= $thumbnail ?>" alt="<?= htmlspecialchars($room->nama_ruangan) ?>" class="w-48 h-full object-cover">
                <?php else: ?>
                  <div class="w-48 bg-gradient-to-br from-slate-200 to-slate-300 flex items-center justify-center">
                    <svg class="w-16 h-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                <?php endif; ?>
                
                <div class="flex-1 p-6">
                  <h3 class="font-bold text-xl text-slate-800 mb-3"><?= htmlspecialchars($room->nama_ruangan) ?></h3>
                  
                  <div class="space-y-2 mb-4">
                    <div class="flex items-center text-slate-600">
                      <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                      </svg>
                      <span class="font-semibold"><?= (int)$room->kapasitas_min ?> - <?= (int)$room->kapasitas_max ?></span> orang
                    </div>
                    
                    <div class="flex items-center text-slate-600">
                      <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                      </svg>
                      <?= htmlspecialchars($room->jenis_ruangan) ?>
                    </div>
                    
                    <?php $facilities = $room->getFacilities(); ?>
                    <?php if (!empty($facilities)): ?>
                      <div class="flex items-start text-slate-600">
                        <svg class="w-5 h-5 mr-2 mt-0.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm"><?= htmlspecialchars(implode(', ', array_slice($facilities, 0, 3))) ?><?= count($facilities) > 3 ? '...' : '' ?></span>
                      </div>
                    <?php endif; ?>
                  </div>
                  
                  <a href="/rooms/show?id_ruangan=<?= (int)$room->id_ruangan ?>" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold group">
                    Lihat Detail
                    <svg class="w-5 h-5 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
