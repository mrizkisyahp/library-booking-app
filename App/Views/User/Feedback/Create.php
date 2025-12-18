<?php $validator = $validator ?? null; ?>

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-12 px-4">
  <div class="max-w-2xl mx-auto">
    <!-- Flash Messages -->
    <?php if ($message = flash('error')): ?>
      <div class="bg-red-50 border-2 border-red-200 text-red-700 px-6 py-4 rounded-xl mb-6">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <!-- Header -->
    <div class="mb-8">
      <h2 class="text-4xl font-bold text-slate-800 mb-2">Berikan Feedback</h2>
      <p class="text-slate-600">Bantu kami meningkatkan layanan dengan memberikan penilaian Anda</p>
    </div>
    <!-- Booking Info Card -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
      <h3 class="text-lg font-bold text-slate-800 mb-4">Detail Booking</h3>

      <div class="space-y-3">
        <div class="flex items-start">
          <svg class="w-5 h-5 text-slate-500 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
          <div>
            <p class="text-sm text-slate-500">Ruangan</p>
            <p class="font-semibold text-slate-800"><?= htmlspecialchars($booking->nama_ruangan) ?></p>
            <p class="text-sm text-slate-600"><?= htmlspecialchars($booking->jenis_ruangan) ?></p>
          </div>
        </div>
        <div class="flex items-start">
          <svg class="w-5 h-5 text-slate-500 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <div>
            <p class="text-sm text-slate-500">Tanggal & Waktu</p>
            <p class="font-semibold text-slate-800">
              <?= date('d F Y', strtotime($booking->tanggal_penggunaan_ruang)) ?>
            </p>
            <p class="text-sm text-slate-600">
              <?= substr($booking->waktu_mulai, 0, 5) ?> - <?= substr($booking->waktu_selesai, 0, 5) ?>
            </p>
          </div>
        </div>
        <div class="flex items-start">
          <svg class="w-5 h-5 text-slate-500 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <div>
            <p class="text-sm text-slate-500">Tujuan</p>
            <p class="text-slate-800"><?= htmlspecialchars($booking->tujuan) ?></p>
          </div>
        </div>
      </div>
    </div>
    <!-- Feedback Form -->
    <form action="/feedback/store" method="POST" class="bg-white rounded-2xl shadow-lg p-6">
      <?= csrf_field() ?>
      <input type="hidden" name="booking_id" value="<?= $booking->id_booking ?>">
      <!-- Rating -->
      <div class="mb-6">
        <label class="block text-sm font-bold text-slate-700 mb-3">
          Rating Pengalaman Anda <span class="text-red-500">*</span>
        </label>
        <div class="flex gap-2" id="star-rating">
<div class="flex flex-row-reverse justify-center gap-1 rating-group">
    <?php for ($i = 5; $i >= 1; $i--): ?>
        <input
            type="radio"
            name="rating"
            id="rating-<?= $i ?>"
            value="<?= $i ?>"
            class="peer hidden"
            required
            <?= old('rating') == $i ? 'checked' : '' ?>
        >

        <label
            for="rating-<?= $i ?>"
            class="cursor-pointer text-gray-300 transition-colors duration-200
                   peer-hover:text-yellow-300
                   peer-checked:text-yellow-400"
        >
            <svg
                class="w-12 h-12 fill-current"
                viewBox="0 0 20 20"
                aria-hidden="true"
            >
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
        </label>
    <?php endfor; ?>
</div>

        </div>
        <?php if ($validator?->hasError('rating')): ?>
          <p class="mt-2 text-sm text-red-600"><?= htmlspecialchars($validator->getFirstError('rating')) ?></p>
        <?php endif; ?>
        <p class="text-xs text-slate-500 mt-2">1 = Sangat Buruk, 5 = Sangat Baik</p>
      </div>
      <!-- Comment -->
      <div class="mb-6">
        <label class="block text-sm font-bold text-slate-700 mb-3">
          Komentar (Opsional)
        </label>
        <textarea name="komentar" rows="5"
          class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:outline-none resize-none"
          placeholder="Ceritakan pengalaman Anda menggunakan ruangan ini..."></textarea>
        <p class="text-xs text-slate-500 mt-2">Feedback Anda akan membantu kami meningkatkan kualitas layanan</p>
      </div>
      <!-- Actions -->
      <div class="flex gap-3">
        <button type="submit"
          class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-6 rounded-xl transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
          Kirim Feedback
        </button>
        <a href="/my-bookings"
          class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-3 px-6 rounded-xl text-center transition-colors">
          Batal
        </a>
      </div>
    </form>
  </div>
</div>
