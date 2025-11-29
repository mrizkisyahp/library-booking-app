<?php

use App\Core\App;
use App\Models\Booking;
use App\Core\Csrf;
use App\Models\User;

$currentUser = App::$app->user instanceof User ? App::$app->user : null;
$isOwner = $currentUser && (int) $currentUser->id_user === (int) $booking->user_id;

/** @var Booking $booking */
?>

<!-- Welcome Header -->
<div class="p-4 bg-white shadow w-full ">
  <div class="flex items-center gap-4 py-4">
    <div class="flex items-center gap-4 ">
      <a href="/dashboard">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
          class="lucide lucide-chevron-left-icon lucide-chevron-left size-9">
          <path d="m15 18-6-6 6-6" />
        </svg>
      </a>
      <span class="text-black font-bold text-4xl">
        Detail Booking
      </span>
    </div>
  </div>
</div>

<div class="bg-gray-100 shadow-lg p-6 md:p-0">
  <div class="bg-white rounded-3xl p-8 mb-6 ">
    <p class="font-bold text-4xl mb-4">
      [NAMA RUANGAN]
    </p>
    <p>
      [JENIS RUANGAN]
    </p>
    <div class="my-4">

      <div class="flex items-center gap-4 mb-2">
        <?= date('l, d F Y', strtotime($booking->tanggal_penggunaan_ruang)) ?>
      </div>

      <div class="flex items-center gap-4 mb-2">
        <?= htmlspecialchars(substr($booking->waktu_mulai, 0, 5)) ?>
      </div>

      <div class="flex items-center gap-4 mb-2">
        [MEMBERS (3/12. MIN 3)]
      </div>

      <div class="flex items-center gap-4 mb-2">
        <?= nl2br(htmlspecialchars($booking->tujuan ?? '-')) ?>
      </div>
    </div>

    <div>
      <?php
      // $statusColors = [
      //   'draft' => 'bg-gray-300 text-gray-800 border-gray-400',
      //   'pending' => 'bg-yellow-300 text-yellow-800 border-yellow-400',
      //   'verified' => 'bg-blue-100 text-blue-800 border-blue-400',
      //   'active' => 'bg-emerald-100 text-emerald-800 border-emerald-400',
      //   'completed' => 'bg-green-100 text-green-800 border-green-400',
      //   'cancelled' => 'bg-red-100 text-red-800 border-red-400',
      //   'expired' => 'bg-slate-100 text-slate-700 border-slate-400',
      //   'no_show' => 'bg-orange-100 text-orange-800 border-orange-400',
      // ];
      // $statusKey = strtolower($booking['status']);
      // $statusColor = $statusColors[$statusKey] ?? 'bg-gray-100 text-gray-800';
      // $statusLabel = ucwords(str_replace('_', ' ', $statusKey));
      ?>
      <div class="px-4 py-2 mb-4 rounded-3xl font-regular tracking-wide border 
                    <?php
                    // echo $statusColor 
                    ?>
                      ">
        Status:
        <?php
        // echo htmlspecialchars($statusLabel) 
        ?>
      </div>

    </div>
  </div>
</div>