<?php

use App\Core\App;
use App\Models\Booking;
use App\Core\Csrf;
use App\Models\User;

$currentUser = App::$app->user instanceof User ? App::$app->user : null;
$isOwner = $currentUser && (int)$currentUser->id_user === (int)$booking->user_id;

/** @var Booking $booking */
?>

<div class="max-w-5xl mx-auto">
  <!-- Back Button -->
  <div class="mb-6">
    <a href="/dashboard" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold group">
      <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      Kembali ke Dashboard
    </a>
  </div>

  <!-- Page Header -->
  <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
    <div class="flex items-center justify-between mb-2">
      <h2 class="text-3xl font-bold text-slate-800 flex items-center">
        <svg class="w-8 h-8 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Draft Booking
      </h2>
      <span class="inline-flex px-4 py-2 rounded-lg font-semibold text-sm border-2 bg-gray-100 text-gray-800 border-gray-300">
        <?= htmlspecialchars(ucfirst($booking->status)) ?>
      </span>
    </div>
    <p class="text-slate-600">Lengkapi informasi booking Anda sebelum mengirim ke admin</p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
      <!-- Booking Details -->
      <div class="bg-white rounded-2xl shadow-lg p-8">
        <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
          <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          Detail Booking
        </h3>
        
        <div class="space-y-4">
          <div class="flex items-start p-4 bg-slate-50 rounded-xl">
            <svg class="w-5 h-5 mr-3 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <div>
              <p class="text-sm font-semibold text-slate-600">Ruangan</p>
              <p class="text-lg font-bold text-slate-800">Ruangan #<?= htmlspecialchars($booking->ruangan_id) ?></p>
            </div>
          </div>

          <div class="flex items-start p-4 bg-slate-50 rounded-xl">
            <svg class="w-5 h-5 mr-3 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <div>
              <p class="text-sm font-semibold text-slate-600">Tanggal Penggunaan</p>
              <p class="text-lg font-bold text-slate-800"><?= date('l, d F Y', strtotime($booking->tanggal_penggunaan_ruang)) ?></p>
            </div>
          </div>

          <div class="flex items-start p-4 bg-slate-50 rounded-xl">
            <svg class="w-5 h-5 mr-3 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
              <p class="text-sm font-semibold text-slate-600">Waktu</p>
              <p class="text-lg font-bold text-slate-800">
                <?= htmlspecialchars(substr($booking->waktu_mulai, 0, 5)) ?> - <?= htmlspecialchars(substr($booking->waktu_selesai, 0, 5)) ?>
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Members Section -->
      <div class="bg-white rounded-2xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-bold text-slate-800 flex items-center">
            <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Daftar Anggota
          </h3>
          <div class="text-sm font-semibold px-3 py-1 rounded-full bg-emerald-100 text-emerald-700">
            <?= (int)$currentMembers ?> / <?= isset($maximumMembers) && $maximumMembers > 0 ? (int)$maximumMembers : '∞' ?> peserta
            <?php if (isset($requiredMembers) && $requiredMembers > 0): ?>
              · Min <?= (int)$requiredMembers ?>
            <?php endif; ?>
          </div>
        </div>

        <?php if (isset($maximumMembers) && $maximumMembers > 0 && $currentMembers >= $maximumMembers): ?>
          <div class="mb-4 bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
            <div class="flex items-start">
              <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <div class="ml-3">
                <p class="text-sm font-semibold text-red-800">Kapasitas Penuh</p>
                <p class="text-sm text-red-700 mt-1">
                  Ruangan sudah mencapai kapasitas maksimum. Anda tidak bisa menambah anggota lagi.
                </p>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <?php $members = $booking->getMembers(); ?>
        <?php if (empty($members)): ?>
          <div class="text-center py-8">
            <svg class="w-16 h-16 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <p class="text-slate-600">Belum ada anggota yang bergabung</p>
          </div>
        <?php else: ?>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-6">
            <?php foreach ($members as $member): ?>
              <div class="flex items-center p-3 bg-slate-50 rounded-xl">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center mr-3">
                  <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="font-semibold text-slate-800 truncate"><?= htmlspecialchars($member['nama'] ?? 'Unknown') ?></p>
                  <p class="text-sm text-slate-500 truncate"><?= htmlspecialchars($member['email']) ?></p>
                  <?php if (!empty($member['is_owner'])): ?>
                    <span class="text-xs text-emerald-600 font-semibold flex items-center mt-1">
                      <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                      PIC
                    </span>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if ($isOwner): ?>
          <!-- Add Member Form -->
          <form action="/bookings/member" method="post" class="border-t pt-6">
            <?= Csrf::field() ?>
            <input type="hidden" name="booking_id" value="<?= (int)$booking->id_booking ?>">
            <label class="block text-sm font-semibold text-slate-700 mb-2">Tambah Anggota</label>
            <div class="flex gap-3">
              <input type="email" name="member_email" required placeholder="Email anggota" class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
              <button type="submit" class="bg-emerald-600 text-white px-6 py-3 rounded-xl hover:bg-emerald-700 transition-all font-semibold whitespace-nowrap">
                <svg class="inline-block w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah
              </button>
            </div>
          </form>

          <!-- Requirement Warning -->
          <?php if (!$canSubmit): ?>
            <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4">
              <div class="flex items-start">
                <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="ml-3">
                  <p class="text-sm font-semibold text-yellow-800">Anggota Belum Mencukupi</p>
                  <p class="text-sm text-yellow-700 mt-1">
                    Minimal <?= (int)$requiredMembers ?> anggota diperlukan. Saat ini: <?= (int)$currentMembers ?> anggota.
                  </p>
                </div>
              </div>
            </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>

      <!-- Submit Form -->
      <?php if ($isOwner): ?>
        <div class="bg-white rounded-2xl shadow-lg p-8">
          <form action="/bookings/submit" method="post">
            <?= CSRF::field() ?>
            <input type="hidden" name="booking_id" value="<?= (int)$booking->id_booking ?>">
            
            <button type="submit" 
              <?= ($booking->status !== 'draft' || !$canSubmit) ? 'disabled' : '' ?>
              class="w-full bg-primary text-white px-8 py-4 rounded-xl hover:bg-emerald-700 transition-all font-semibold shadow-lg hover:shadow-xl disabled:bg-gray-300 disabled:cursor-not-allowed disabled:hover:shadow-lg flex items-center justify-center">
              <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
              </svg>
              <?= $canSubmit ? 'Kirim ke Admin' : 'Lengkapi Anggota Terlebih Dahulu' ?>
            </button>
          </form>
        </div>
      <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
      <!-- Invite Link Card -->
      <?php if (!empty($booking->invite_token)): ?>
        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-2xl shadow-lg p-6 border-2 border-emerald-200">
          <h3 class="font-bold text-emerald-800 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
            </svg>
            Link Undangan
          </h3>
          <p class="text-sm text-emerald-700 mb-3">Bagikan link ini kepada anggota untuk bergabung</p>
          <div class="bg-white rounded-lg p-3 mb-3 border border-emerald-200">
            <p class="text-xs font-mono text-slate-600 break-all">
              <?= htmlspecialchars($booking->invite_token) ?>
            </p>
          </div>
          <button type="button" 
            onclick="navigator.clipboard.writeText(window.location.origin + '/bookings/join?token=<?= rawurlencode($booking->invite_token) ?>'); alert('Link berhasil disalin!');"
            class="w-full bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors font-semibold text-sm flex items-center justify-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
            Salin Link Undangan
          </button>
        </div>
      <?php endif; ?>

      <!-- Help Card -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <h3 class="font-bold text-slate-800 mb-3 flex items-center">
          <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          Langkah Selanjutnya
        </h3>
        <ol class="space-y-3 text-sm text-slate-600">
          <li class="flex items-start">
            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 font-semibold text-xs flex items-center justify-center mr-2">1</span>
            <span>Tambahkan anggota minimal <?= (int)$requiredMembers ?> orang</span>
          </li>
          <li class="flex items-start">
            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 font-semibold text-xs flex items-center justify-center mr-2">2</span>
            <span>Bagikan link undangan ke anggota</span>
          </li>
          <li class="flex items-start">
            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 font-semibold text-xs flex items-center justify-center mr-2">3</span>
            <span>Kirim booking ke admin untuk disetujui</span>
          </li>
        </ol>
      </div>
    </div>
  </div>
</div>