<?php
use App\Core\App;
use App\Core\Csrf;
?>

<div class="max-w-7xl mx-auto">  
  <!-- Header -->
  <div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-900 mb-2">Check-in Booking</h2>
    <p class="text-gray-600">Masukkan kode check-in untuk memverifikasi kedatangan</p>
  </div>

  <!-- Flash Messages -->
  <?php if ($m = App::$app->session->getFlash('success')): ?>
    <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-lg">
      <div class="flex items-center">
        <svg class="w-5 h-5 text-emerald-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <p class="text-emerald-800 font-medium"><?= htmlspecialchars($m) ?></p>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($m = App::$app->session->getFlash('error')): ?>
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
      <div class="flex items-center">
        <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <p class="text-red-800 font-medium"><?= htmlspecialchars($m) ?></p>
      </div>
    </div>
  <?php endif; ?>

  <!-- Check-in Form Card -->
  <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
    <form action="/checkin" method="post" class="space-y-6">
      <?= Csrf::field() ?>
      
      <div>
        <label for="checkin_code" class="block text-sm font-semibold text-gray-700 mb-2">
          Kode Check-in
        </label>
        <input 
          type="text" 
          id="checkin_code" 
          name="checkin_code" 
          maxlength="8" 
          required
          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all text-center text-2xl font-mono tracking-widest uppercase"
          placeholder="XXXXXXXX"
        >
        <p class="mt-2 text-sm text-gray-500">Masukkan 8 karakter kode check-in</p>
      </div>

      <button 
        type="submit"
        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5"
      >
        <span class="flex items-center justify-center">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          Verifikasi Check-in
        </span>
      </button>
    </form>
  </div>

  <!-- Back Link -->
  <div class="mt-6">
    <a href="/admin/bookings" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-medium transition-colors">
      <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
      </svg>
      Kembali ke Manajemen Booking
    </a>
  </div>
</div>
