<div class="max-w-xl mx-auto bg-white rounded-2xl shadow-lg p-8">
  <h2 class="text-2xl font-bold text-slate-800 mb-4">Masukkan Code Undangan</h2>
  <p class="text-slate-600 mb-6">Tempel code yang dibagikan oleh ketua booking untuk bergabung.</p>

  <form method="post" action="/bookings/join" class="space-y-4">
    <?= csrf_field() ?>
    <div>
      <label class="block text-sm font-semibold text-slate-700 mb-2">Code</label>
      <input type="text" name="invite_token" value="<?= htmlspecialchars($prefill ?? '') ?>"
        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200"
        placeholder="Contoh: 9f1c2e3a4b5d6f78..." required>
    </div>
    <button type="submit"
      class="w-full bg-primary text-white px-6 py-3 rounded-xl hover:bg-emerald-700 transition-all font-semibold shadow-lg">
      Gabung Sekarang
    </button>
  </form>
</div>