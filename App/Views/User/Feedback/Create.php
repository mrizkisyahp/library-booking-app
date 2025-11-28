<?php
use App\Core\Csrf;
/** @var \App\Models\Booking $booking */
?>

<div class="max-w-3xl mx-auto p-6">
  <!-- Header -->
  <div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-900 mb-2">Feedback Booking</h2>
    <p class="text-gray-600">Berikan penilaian Anda terhadap layanan dan ruangan</p>
  </div>

  <!-- Booking Info Card -->
  <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl p-6 mb-6 border border-emerald-100">
    <div class="flex items-start">
      <svg class="w-6 h-6 text-emerald-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
      </svg>
      <div>
        <p class="text-sm font-semibold text-gray-600">Ruangan ID</p>
        <p class="text-lg font-bold text-gray-900">#<?= htmlspecialchars($booking->ruangan_id) ?></p>
        <p class="text-sm text-gray-600 mt-2">
          <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <?= htmlspecialchars($booking->tanggal_penggunaan_ruang) ?> • <?= htmlspecialchars($booking->waktu_mulai) ?>
        </p>
      </div>
    </div>
  </div>

  <!-- Feedback Form Card -->
  <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
    <form action="/feedback" method="post" class="space-y-6">
      <?= csrf_field() ?>
      <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">

      <!-- Service Rating -->
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
          Rating Pelayanan
        </label>
        <div class="flex items-center space-x-2">
          <input type="number" name="service_rating" min="1" max="5" required
            class="w-24 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all text-center font-semibold"
            placeholder="1-5">
          <span class="text-gray-500 text-sm">(1 = Buruk, 5 = Sangat Baik)</span>
        </div>
      </div>

      <!-- Room Rating -->
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
          Rating Ruangan
        </label>
        <div class="flex items-center space-x-2">
          <input type="number" name="room_rating" min="1" max="5" required
            class="w-24 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all text-center font-semibold"
            placeholder="1-5">
          <span class="text-gray-500 text-sm">(1 = Buruk, 5 = Sangat Baik)</span>
        </div>
      </div>

      <!-- Comments -->
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
          Komentar
        </label>
        <textarea name="comments" rows="4" placeholder="Tulis komentar Anda tentang pengalaman menggunakan ruangan..."
          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all resize-none"></textarea>
        <p class="mt-2 text-sm text-gray-500">Opsional - Bagikan pengalaman Anda untuk membantu kami meningkatkan
          layanan</p>
      </div>

      <!-- Submit Button -->
      <button type="submit"
        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
        <span class="flex items-center justify-center">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          Kirim Feedback
        </span>
      </button>
    </form>
  </div>

  <!-- Back Link -->
  <div class="mt-6">
    <a href="/dashboard"
      class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-medium transition-colors">
      <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      Kembali ke Dashboard
    </a>
  </div>
</div>