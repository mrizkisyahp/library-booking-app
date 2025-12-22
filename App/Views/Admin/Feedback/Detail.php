<div class="p-4 bg-white shadow-md w-full mb-6">
    <div class="flex items-center gap-4 py-4">
        <div class="flex items-center gap-4 ">
            <a href="/admin/feedback">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-chevron-left-icon lucide-chevron-left size-9">
                    <path d="m15 18-6-6 6-6" />
                </svg>
            </a>
            <span class="text-black font-bold text-xl md:text-4xl">
                Kembali ke daftar feedback
            </span>
        </div>
    </div>
</div>

<div class="max-w-5xl mx-auto space-y-6">

  <div class="bg-white rounded-2xl shadow-lg p-8">
    <div class="flex items-center justify-between mb-2">
      <div>
        <p class="text-sm text-slate-500 uppercase">Detail Feedback</p>
        <h1 class="text-3xl font-bold text-slate-800">#<?= htmlspecialchars((string) $feedback->id_feedback) ?></h1>
      </div>
      <div class="flex items-center gap-2">
        <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
          <path
            d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z" />
        </svg>
        <span class="text-2xl font-bold text-slate-800"><?= htmlspecialchars((string) $feedback->rating) ?>/5</span>
      </div>
    </div>
    <p class="text-slate-600">Feedback dari: <strong><?= htmlspecialchars($feedback->nama ?? 'Unknown') ?></strong></p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Feedback Info -->
    <div class="bg-white rounded-2xl shadow-lg p-8 space-y-4">
      <h2 class="text-xl font-bold text-slate-800 flex items-center">
        <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
        </svg>
        Komentar
      </h2>
      <div class="p-4 bg-slate-50 rounded-xl">
        <p class="text-slate-800"><?= nl2br(htmlspecialchars($feedback->komentar ?? '-')) ?></p>
      </div>
      <p class="text-sm text-slate-500">
        Dikirim pada: <?= htmlspecialchars($feedback->created_at ?? '-') ?>
      </p>
    </div>

    <!-- User Info -->
    <div class="bg-white rounded-2xl shadow-lg p-8 space-y-4">
      <h2 class="text-xl font-bold text-slate-800 flex items-center">
        <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        Pengguna
      </h2>
      <div class="space-y-3">
        <div class="p-4 bg-slate-50 rounded-xl">
          <p class="text-xs font-semibold text-slate-500 uppercase">Nama</p>
          <p class="text-lg font-bold text-slate-800 capitalize"><?= htmlspecialchars($feedback->nama ?? '-') ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Booking Info -->
  <div class="bg-white rounded-2xl shadow-lg p-8 space-y-4">
    <h2 class="text-xl font-bold text-slate-800 flex items-center">
      <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
      </svg>
      Detail Booking
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="p-4 bg-slate-50 rounded-xl">
        <p class="text-xs font-semibold text-slate-500 uppercase">Ruangan</p>
        <p class="text-lg font-bold text-slate-800"><?= htmlspecialchars($feedback->nama_ruangan ?? '-') ?></p>
      </div>

      <div class="p-4 bg-slate-50 rounded-xl">
        <p class="text-xs font-semibold text-slate-500 uppercase">Tanggal Booking</p>
        <p class="text-lg font-bold text-slate-800"><?= htmlspecialchars($feedback->tanggal_booking ?? '-') ?></p>
      </div>

      <div class="p-4 bg-slate-50 rounded-xl">
        <p class="text-xs font-semibold text-slate-500 uppercase">Tanggal Penggunaan</p>
        <p class="text-lg font-bold text-slate-800"><?= htmlspecialchars($feedback->tanggal_penggunaan_ruang ?? '-') ?>
        </p>
      </div>

      <div class="p-4 bg-slate-50 rounded-xl">
        <p class="text-xs font-semibold text-slate-500 uppercase">Waktu</p>
        <p class="text-lg font-bold text-slate-800">
          <?= htmlspecialchars(substr($feedback->waktu_mulai ?? '', 0, 5)) ?> -
          <?= htmlspecialchars(substr($feedback->waktu_selesai ?? '', 0, 5)) ?>
        </p>
      </div>
    </div>

    <div class="p-4 bg-slate-50 rounded-xl">
      <p class="text-xs font-semibold text-slate-500 uppercase">Tujuan Penggunaan</p>
      <p class="text-slate-800"><?= htmlspecialchars($feedback->tujuan ?? '-') ?></p>
    </div>
  </div>
</div>