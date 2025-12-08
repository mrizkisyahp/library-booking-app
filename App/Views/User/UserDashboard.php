<?php
?>
<!-- Welcome Header -->
<div class="min-h-dvh">
  <div class="rounded-2xl p-4 mx-auto max-w-7xl">
    <div class="flex items-center mt-6 px-6 mb-4 md:bg-primary md:rounded-2xl md:p-6 ">
      <div>
        <h1 class="text-4xl font-bold text-white mb-2">
          Selamat Datang, <span
            class="text-emerald-100 md:text-emerald-200 capitalize"><?= htmlspecialchars($user->nama) ?></span>!
          👋
        </h1>
        <p class="text-white">Kelola booking ruangan Anda dengan mudah</p>
      </div>
    </div>

    <!-- Kubaca warning -->
    <?php if (auth()->user()->status === 'pending kubaca' || auth()->user()->status === 'rejected'): ?>
      <div class="w-full mb-4">
        <div
          class="inline-block bg-yellow-300 border-2 border-yellow-600 font-regular text-sm text-black w-full px-6 py-4 rounded-3xl text-left mb-4 font-regular tracking-wide">
          <p class="text-2xl font-bold uppercase my-4">
            Peringatan!
          </p>
          <p class="mb-4">
            <?php if (auth()->user()->status === 'pending kubaca'): ?>
              <?php if (!empty(auth()->user()->kubaca_img)): ?>
                Akun Anda sedang menunggu verifikasi admin. Terima kasih telah mengupload KuBaca!
              <?php else: ?>
                Untuk membuat booking, kamu harus verifikasi akun dengan mengunggah bukti KuBacaPNJ di <a href="/profile"
                  class="font-semibold underline hover:text-gray-900">Profil</a>!
              <?php endif; ?>
            <?php else: ?>
              Verifikasi KuBaca Anda ditolak. Silakan upload ulang di <a href="/profile"
                class="font-semibold underline hover:text-gray-900">Profil</a>!
            <?php endif; ?>
          </p>
        </div>
      </div>
    <?php endif; ?>

    <!-- Pending Feedback Warning -->
    <?php if (!empty($feedbacks ?? [])): ?>
      <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-5 shadow-md">
        <div class="flex items-start">
          <svg class="w-6 h-6 text-yellow-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <div class="ml-4">
            <h3 class="font-semibold text-yellow-800 mb-2">Perhatian: Feedback Belum Diisi</h3>
            <p class="text-yellow-700 mb-3">Anda memiliki booking yang sudah selesai namun belum mengisi
              feedback. Harap
              lengkapi feedback sebelum membuat booking baru.</p>
            <ul class="space-y-2">
              <?php foreach ($feedbacks as $feedback): ?>
                <li class="flex items-center justify-between bg-yellow-100 px-4 py-2 rounded-lg">
                  <span class="text-yellow-800 text-sm">
                    <strong><?= htmlspecialchars($feedback['nama_ruangan']) ?></strong> –
                    <?= htmlspecialchars($feedback['tanggal_penggunaan_ruang']) ?>
                    <?= htmlspecialchars($feedback['waktu_mulai']) ?>
                  </span>
                  <a href="/feedback/create?booking=<?= (int) $feedback['id_booking'] ?>"
                    class="text-emerald-600 hover:text-emerald-700 font-semibold text-sm underline">
                    Isi Feedback
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Booking Invitation Notifications (Static Design) -->
    <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 rounded-lg p-5 shadow-md">
      <div class="flex items-start">
        <svg class="w-6 h-6 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
        <div class="ml-4 flex-1">
          <h3 class="font-semibold text-blue-800 mb-3">Undangan Booking</h3>

          <!-- Invitation Item 1 -->
          <div class="bg-white rounded-xl p-4 mb-3 border border-blue-200 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
              <div>
                <p class="font-semibold text-slate-800 mb-1">
                  <span class="text-blue-600">Ahmad Fauzi</span> mengundang Anda untuk bergabung
                </p>
                <div class="text-sm text-slate-600 space-y-1">
                  <p class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <strong>Lentera Edukasi</strong> - Bimbingan & Konseling
                  </p>
                  <p class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Selasa, 10 Desember 2025 • 08:15 - 10:55
                  </p>
                </div>
              </div>
              <div class="flex gap-2">
                <button type="button"
                  class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition-colors flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  Terima
                </button>
                <button type="button"
                  class="px-4 py-2 bg-red-100 text-red-700 text-sm font-semibold rounded-lg hover:bg-red-200 transition-colors flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                  Tolak
                </button>
              </div>
            </div>
          </div>
          <!-- Invitation Item 2 -->
          <div class="bg-white rounded-xl p-4 border border-blue-200 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
              <div>
                <p class="font-semibold text-slate-800 mb-1">
                  <span class="text-blue-600">Siti Nurhaliza</span> mengundang Anda untuk bergabung
                </p>
                <div class="text-sm text-slate-600 space-y-1">
                  <p class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <strong>Ruang Audio Visual</strong> - Audio Visual
                  </p>
                  <p class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Rabu, 11 Desember 2025 • 13:15 - 16:00
                  </p>
                </div>
              </div>
              <div class="flex gap-2">
                <button type="button"
                  class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition-colors flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  Terima
                </button>
                <button type="button"
                  class="px-4 py-2 bg-red-100 text-red-700 text-sm font-semibold rounded-lg hover:bg-red-200 transition-colors flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                  Tolak
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Main Content -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Statistics -->
        <div class="bg-gray-100 rounded-t-3xl md:rounded-3xl shadow-lg md:shadow-none p-6 md:p-0 ">
          <div class="bg-white rounded-3xl p-6 mb-6">
            <h2 class="text-4xl font-bold text-slate-800 mb-6 flex items-center">
              <svg class="size-9 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
              Booking Saya
            </h2>

            <!-- Flash Messages -->
            <?php if ($m = flash('success')): ?>
              <div class="mb-6 bg-green-50 border-l-4 border-emerald-500 rounded-lg p-4 shadow-sm">
                <div class="flex items-center gap-3">
                  <svg class="w-6 h-6 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <p class="text-emerald-800 font-medium"><?= nl2br(htmlspecialchars($m)) ?></p>
                </div>
              </div>
            <?php endif; ?>

            <?php if ($m = flash('error')): ?>
              <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm">
                <div class="flex items-center gap-3">
                  <svg class="w-6 h-6 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <p class="text-red-800 font-medium"><?= nl2br(htmlspecialchars($m)) ?></p>
                </div>
              </div>
            <?php endif; ?>

            <?php if ($m = flash('warning')): ?>
              <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4 shadow-sm">
                <div class="flex items-center gap-3">
                  <svg class="w-6 h-6 text-yellow-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                  <p class="text-yellow-800 font-medium"><?= nl2br(htmlspecialchars($m)) ?></p>
                </div>
              </div>
            <?php endif; ?>

            <?php if ($m = flash('info')): ?>
              <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4 shadow-sm">
                <div class="flex items-center gap-3">
                  <svg class="w-6 h-6 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <p class="text-blue-800 font-medium"><?= nl2br(htmlspecialchars($m)) ?></p>
                </div>
              </div>
            <?php endif; ?>

            <?php if (empty($bookings)): ?>
              <div class="text-center py-12 rounded-3xl border-2 border-gray-400 bg-gray-100 mb-4">
                <div class="flex justify-center mb-6 text-gray-600">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-frown-icon lucide-frown size-9">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M16 16s-1.5-2-4-2-4 2-4 2" />
                    <line x1="9" x2="9.01" y1="9" y2="9" />
                    <line x1="15" x2="15.01" y1="9" y2="9" />
                  </svg>
                </div>
                <p class="text-slate-600 mb-4">Sepertinya tidak ada booking yang aktif </p>
              </div>

              <a href="/rooms"
                class="inline-flex w-full justify-center items-center bg-primary text-white px-6 py-4 rounded-2xl hover:bg-emerald-700 active:bg-emerald-700 transition-all font-regular mb-4 shadow-md cursor-pointer focus:outline-none focus:ring-2 focus:bg-emerald-600 focus:ring-emerald-500 focus:ring-offset-2">
                Cari Ruangan
              </a>

              <!-- ini tombol pop up ya buat kode booking -->
              <a href="/dashboard#modal-invite"
                class="inline-flex w-full justify-center items-center bg-primary text-white px-6 py-4 rounded-2xl hover:bg-emerald-700 active:bg-emerald-700 transition-all font-regular shadow-md cursor-pointer focus:outline-none focus:ring-2 focus:bg-emerald-600 focus:ring-emerald-500 focus:ring-offset-2">
                Masukkan Kode Undangan
              </a>

              <!-- modal menu kode booking-->
              <div id="modal-invite"
                class="fixed inset-0 bg-black/50 opacity-0 pointer-events-none duration-300 transition-all target:opacity-100 target:pointer-events-auto flex justify-center items-center z-999">

                <div
                  class="bg-white p-6 rounded-2xl w-11/12 max-w-md shadow-lg scale-95 transition-all duration-300 target:scale-100 relative">
                  <a href="/dashboard"
                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-xl font-bold cursor-pointer">
                    &times;
                  </a>

                  <h1 class="text-lg font-bold text-slate-800 mb-2">
                    Gabung dengan Kode Undangan
                  </h1>
                  <p class="text-sm text-slate-600 mb-4">
                    Punya kode Undangan? Gabung di sini!
                  </p>

                  <form action="/bookings/join" method="post" class="space-y-3">
                    <?= csrf_field() ?>

                    <input type="text" name="invite_token" value="<?= htmlspecialchars($prefill ?? '') ?>"
                      placeholder="Masukkan kode di sini...." required
                      class="w-full px-3 py-2 text-sm border-2 border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500">

                    <button type="submit"
                      class="w-full bg-primary text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition-all font-semibold text-sm shadow cursor-pointer">
                      Gabung
                    </button>
                  </form>
                </div>
              </div>


            <?php else: ?>
              <?php foreach ($bookings as $booking): ?>
                <div class="rounded-3xl border-2 border-gray-400 bg-gray-100 mb-4">
                  <div class="flex flex-col justify-start p-6">
                    <p class="font-bold text-2xl mb-2">
                      <?= htmlspecialchars($booking->nama_ruangan) ?>
                    </p>
                    <p class="mb-2">
                      <?= htmlspecialchars($booking->jenis_ruangan) ?>
                    </p>
                    <div class="w-full">
                      <?php
                      $statusColors = [
                        'draft' => 'bg-gray-300 text-gray-800 border-gray-400',
                        'pending' => 'bg-yellow-300 text-yellow-800 border-yellow-400',
                        'verified' => 'bg-blue-100 text-blue-800 border-blue-400',
                        'active' => 'bg-emerald-100 text-emerald-800 border-emerald-400',
                        'completed' => 'bg-green-100 text-green-800 border-green-400',
                        'cancelled' => 'bg-red-100 text-red-800 border-red-400',
                        'expired' => 'bg-slate-100 text-slate-700 border-slate-400',
                        'no_show' => 'bg-orange-100 text-orange-800 border-orange-400',
                      ];
                      $statusKey = strtolower($booking->status);
                      $statusColor = $statusColors[$statusKey] ?? 'bg-gray-100 text-gray-800';
                      $statusLabel = ucwords(str_replace('_', ' ', $statusKey));
                      ?>
                      <div class="px-4 py-2 mb-4 rounded-3xl font-regular border text-sm <?= $statusColor ?>">
                        Status:
                        <?= htmlspecialchars($statusLabel) ?>
                        <!-- status state -->
                        <?php if ($booking->status === 'draft' && $booking->current_members < $booking->required_members): ?>
                          (Menunggu Anggota)
                        <?php elseif ($booking->status === 'draft' && $booking->current_members >= $booking->required_members): ?>
                          (Siap Dikirim)
                        <?php elseif ($booking->status === 'pending'): ?>
                          (Menunggu Konfirmasi)
                        <?php elseif ($booking->status === 'verified'): ?>
                          (Terkonfirmasi)
                        <?php elseif ($booking->status === 'active'): ?>
                          (Sedang berlangsung)
                        <?php elseif ($booking->status === 'completed'): ?>
                          (Selesai)
                        <?php endif ?>
                      </div>

                      <p class="mb-4 flex gap-2 items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          class="lucide lucide-calendar-days-icon lucide-calendar-days size-4">
                          <path d="M8 2v4" />
                          <path d="M16 2v4" />
                          <rect width="18" height="18" x="3" y="4" rx="2" />
                          <path d="M3 10h18" />
                          <path d="M8 14h.01" />
                          <path d="M12 14h.01" />
                          <path d="M16 14h.01" />
                          <path d="M8 18h.01" />
                          <path d="M12 18h.01" />
                          <path d="M16 18h.01" />
                        </svg>
                        <?= htmlspecialchars(formatTanggal($booking->tanggal_penggunaan_ruang)) ?>
                      </p>

                      <p class="mb-4 flex gap-2 items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          class="lucide lucide-clock3-icon lucide-clock-3 size-4">
                          <path d="M12 6v6h4" />
                          <circle cx="12" cy="12" r="10" />
                        </svg>
                        <?= htmlspecialchars(formatWaktu($booking->waktu_mulai)) ?>
                      </p>

                      <p class="mb-4 flex gap-2 items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          class="lucide lucide-users-round-icon lucide-users-round size-4">
                          <path d="M18 21a8 8 0 0 0-16 0" />
                          <circle cx="10" cy="8" r="5" />
                          <path d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3" />
                        </svg>
                        <?= (int) $booking->current_members ?> /
                        <?= isset($booking->maximum_members) && $booking->maximum_members > 0 ? (int) $booking->maximum_members : '∞' ?>
                        peserta
                        <?php if (isset($booking->required_members) && $booking->required_members > 0): ?>
                          · Min <?= (int) $booking->required_members ?>
                        <?php endif; ?>
                      </p>

                      <div class="w-full">
                        <?php if ($booking->status === 'draft'): ?>
                          <a href="/bookings/draft?id=<?= (int) $booking->id_booking ?>"
                            class="inline-block bg-emerald-600 hover:bg-emerald-700 font-regular text-sm text-white w-full px-4 py-2 rounded-xl text-center mb-4 font-regular tracking-wide focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all">
                            Lihat Detail
                          </a>
                        <?php else: ?>
                          <a href="/bookings/detail?id=<?= (int) $booking->id_booking ?>"
                            class="inline-block bg-emerald-600 hover:bg-emerald-700 font-regular text-sm text-white w-full px-4 py-2 rounded-xl text-center mb-4 font-regular tracking-wide focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all">
                            Lihat Detail
                          </a>
                        <?php endif; ?>
                        <?php if ($booking->status === 'completed' && empty($booking->feedback_submitted)): ?>
                          <a href="/feedback/create?booking=<?= (int) $booking->id_booking ?>"
                            class="inline-block text-emerald-600 hover:text-emerald-700 font-regular text-sm active:text-emerald-800 w-full px-4 py-2 rounded-xl text-center mb-4 font-regular tracking-wide underline focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all">
                            Isi Feedback
                          </a>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif ?>

          </div>
        </div>
      </div>
      <div>
        <!-- widget -->
        <div class="lg:col-span-2 space-y-6 mb-6">
          <!-- Statistics -->
          <div>
            <div class="grid grid-cols-1 gap-4 *:rounded-2xl">
              <div class="bg-linear-to-br from-purple-50 to-purple-100 p-4 border-2 border-purple-200">
                <p class="text-xs font-semibold text-purple-600 mb-1">Total</p>
                <p class="text-2xl font-bold text-purple-800">100</p>
              </div>
              <div class="bg-linear-to-br from-emerald-50 to-emerald-100 p-4 border-2 border-emerald-200">
                <p class="text-xs font-semibold text-emerald-600 mb-1">Available</p>
                <p class="text-2xl font-bold text-emerald-800">666</p>
              </div>
              <div class="bg-linear-to-br from-rose-50 to-rose-100 p-4 border-2 border-rose-200">
                <p class="text-xs font-semibold text-rose-600 mb-1">Unavailable</p>
                <p class="text-2xl font-bold text-rose-800">999</p>
              </div>
            </div>
          </div>
        </div>

        <!-- tambah widget -->
        <div class="mb-6">
          <a href=""
            class="flex gap-4 w-full border border-4xl border-dashed items-center justify-center rounded-3xl p-6 text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-plus-icon lucide-plus">
              <path d="M5 12h14" />
              <path d="M12 5v14" />
            </svg>
            <span>
              Tambah Widget
            </span>
          </a>
        </div>
      </div>
    </div>
  </div>
  <div>

  </div>
