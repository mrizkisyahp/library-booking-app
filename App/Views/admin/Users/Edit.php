<?php
/** @var \App\Models\User $model */
use App\Core\App;
use App\Core\Csrf;
/** @var array $roles */
?>

<div class="max-w-7xl mx-auto">
  <!-- Header -->
  <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
    <div class="flex items-center gap-4">
      <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
        </svg>
      </div>
      <div>
        <h1 class="text-3xl font-bold text-slate-800">Edit Pengguna</h1>
      </div>
    </div>
  </div>

  <!-- Flash Messages -->
  <?php if ($m = App::$app->session->getFlash('success')): ?>
    <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-4 mb-6 shadow">
      <p class="text-green-700 font-medium"><?= htmlspecialchars($m) ?></p>
    </div>
  <?php endif; ?>

  <?php if ($m = App::$app->session->getFlash('error')): ?>
    <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 mb-6 shadow">
      <p class="text-red-700 font-medium"><?= htmlspecialchars($m) ?></p>
    </div>
  <?php endif; ?>

  <form action="/admin/users/update" method="post" enctype="multipart/form-data">
    <?= Csrf::field() ?>
    <input type="hidden" name="id_user" value="<?= (int)$model->id_user ?>">

    <!-- CARD 1 — USER INFORMATION -->
    <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
      <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
        <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        Informasi Pengguna
      </h2>

      <?php
        $selectedRole = '';
        foreach ($roles as $roleItem) {
            if ((int)$roleItem['id_role'] === (int)$model->id_role) {
                $selectedRole = $roleItem['nama_role'];
                break;
            }
        }
      ?>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- ROLE -->
        <div>
          <label for="role" class="block text-sm font-semibold text-slate-700 mb-2">Role</label>
          <select id="role" name="role"
            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
            <?php foreach ($roles as $role): ?>
              <option value="<?= htmlspecialchars($role['nama_role']) ?>" <?= $role['nama_role'] === $selectedRole ? 'selected' : '' ?>>
                <?= htmlspecialchars(ucfirst($role['nama_role'])) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- STATUS -->
        <div>
          <label for="status" class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
          <select id="status" name="status"
            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
            <option value="active" <?= ($model->status ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="pending kubaca" <?= ($model->status ?? '') === 'pending kubaca' ? 'selected' : '' ?>>Pending</option>
            <option value="suspended" <?= ($model->status ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
          </select>
        </div>

        <!-- NAME -->
        <div class="md:col-span-2">
          <label for="nama" class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap</label>
          <input id="nama" type="text" name="nama" value="<?= htmlspecialchars($model->nama ?? '') ?>" required
            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
          <?php if ($model->hasError('nama')): ?>
            <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($model->getFirstError('nama')) ?></p>
          <?php endif; ?>
        </div>

        <!-- NIM -->
        <div>
          <label for="nim" class="block text-sm font-semibold text-slate-700 mb-2">NIM (Opsional)</label>
          <input id="nim" type="text" name="nim" 
          value="<?= htmlspecialchars($model->nim ?? '') ?>"
          class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
          <?php if ($model->hasError('nim')): ?>
            <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($model->getFirstError('nim')) ?></p>
          <?php endif; ?>
        </div>

        <!-- NIP -->
        <div>
          <label for="nip" class="block text-sm font-semibold text-slate-700 mb-2">NIP (Opsional)</label>
          <input id="nip" type="text" name="nip" value="<?= htmlspecialchars($model->nip ?? '') ?>"
            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
          <?php if ($model->hasError('nip')): ?>
            <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($model->getFirstError('nip')) ?></p>
          <?php endif; ?>
        </div>

        <!-- EMAIL -->
        <div>
          <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
          <input id="email" type="email" name="email" value="<?= htmlspecialchars($model->email ?? '') ?>" required
            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
          <?php if ($model->hasError('email')): ?>
            <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($model->getFirstError('email')) ?></p>
          <?php endif; ?>
        </div>

        <!-- JURUSAN -->
        <div>
          <?php
          $Jurusan = [
            'Teknik Informatika dan Komputer',
            'Teknik Elektro',
            'Teknik Mesin',
            'Teknik Sipil',
            'Akuntansi',
            'Administrasi Niaga',
            'Teknik Grafika dan Penerbitan',
          ];
          ?>
          <label for="jurusan" class="block text-sm font-semibold text-slate-700 mb-2">Jurusan</label>
          <select id="jurusan" name="jurusan"
            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
            <?php foreach ($Jurusan as $option): ?>
              <option value="<?= htmlspecialchars($option) ?>" <?= ($model->jurusan ?? '') === $option ? 'selected' : '' ?>>
                <?= htmlspecialchars($option) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?php if ($model->hasError('jurusan')): ?>
            <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($model->getFirstError('jurusan')) ?></p>
          <?php endif; ?>
        </div>

        <!-- PHONE -->
        <div>
          <label for="nomor_hp" class="block text-sm font-semibold text-slate-700 mb-2">Nomor HP</label>
          <input id="nomor_hp" type="tel" name="nomor_hp" value="<?= htmlspecialchars($model->nomor_hp ?? '') ?>"
            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
        </div>

        <!-- PASSWORD -->
        <div>
          <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Password (Opsional)</label>
          <input id="password" type="password" name="password"
            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all"
            placeholder="Biarkan kosong jika tidak diganti">
        </div>
      </div>
    </div>

    <!-- CARD 2 — FOTO + ACTIONS -->
    <div class="bg-white rounded-2xl shadow-lg p-8 mb-10">
      <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center">
        <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        Foto Kubaca (Opsional)
      </h2>

      <?php if (!empty($model->kubaca_img)): ?>
        <div class="mb-6">
          <p class="text-sm text-slate-600 mb-2">Foto KuBaca saat ini:</p>
          <img src="/uploads/kubaca/<?= htmlspecialchars($model->kubaca_img) ?>" alt="Kubaca"
            class="h-40 rounded-xl border border-slate-200 object-cover">
        </div>
      <?php endif; ?>

      <div class="flex items-center justify-center w-full mb-8">
        <label for="foto_kubaca"
          class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition">
          <div class="flex flex-col items-center justify-center pt-5 pb-6">
            <svg class="w-12 h-12 mb-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            <p class="text-slate-600"><span class="font-semibold">Klik untuk upload</span> atau drag & drop</p>
            <p class="text-xs text-slate-500 mt-1">PNG, JPG, WEBP (max 2MB)</p>
          </div>
          <input id="foto_kubaca" name="foto_kubaca" type="file" class="hidden" accept=".jpg,.jpeg,.png,.webp">
        </label>
      </div>

      <div class="flex justify-end gap-4">
        <a href="/admin/users"
          class="inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 rounded-xl text-slate-700 font-semibold hover:bg-gray-50 transition">
          Batal
        </a>
        <button type="submit"
          class="px-8 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl hover:from-emerald-700 hover:to-emerald-800 transition">
          Simpan Perubahan
        </button>
      </div>
    </div>
  </form>
</div>

<script>
document.getElementById('foto_kubaca')?.addEventListener('change', function (e) {
  const fileName = e.target.files[0]?.name;
  if (fileName) {
    const label = e.target.parentElement;
    const span = label.querySelector("p span.font-semibold");
    if (span) span.textContent = "Dipilih:";
    label.querySelector("p").innerHTML =
      `<span class="font-semibold text-emerald-600">Dipilih:</span> ${fileName}`;
  }
});
</script>
