<?php

use App\Core\App;
use App\Models\Booking;
use App\Core\Csrf;
use App\Models\User;

$currentUser = App::$app->user instanceof User ? App::$app->user : null;

?>

<!-- echo '<pre>';
print_r($bookings);
echo '</pre>'; -->

<div class="min-h-screen bg-linear-to-br from-slate-50 to-slate-100">
  <div class="max-w-7xl mx-auto px-6 py-12">
    <!-- Header -->
    <div class="mb-8">
      <h2 class="text-4xl font-bold text-slate-800 mb-2">Riwayat Booking</h2>
      <p class="text-slate-600">Monitor seluruh riwayat ruangan yang pernah digunakan</p>
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
    </form>
    </div>
</div>
