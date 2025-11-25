<?php
use App\Core\App;
use App\Core\Csrf;
use App\Models\User;
/** @var User $user */

?>

<div class="max-w-5xl mx-auto space-y-6">
  <div class="mb-2">
    <a href="/admin/users" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold group">
      <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none"
        stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      Kembali ke daftar user
    </a>
  </div>

  <!-- Flash Messages -->
  <?php if ($message = App::$app->session->getFlash('success')): ?>
    <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <?php if ($message = App::$app->session->getFlash('error')): ?>
    <div class="mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <div class="bg-white rounded-2xl shadow-lg p-8">
    <div class="flex items-center justify-between mb-2">
      <div>
        <p class="text-sm text-slate-500 uppercase">Detail User</p>
        <h1 class="text-3xl font-bold text-slate-800">#<?= htmlspecialchars((string) $user->id_user) ?></h1>
      </div>
      <span
        class="inline-flex px-4 py-2 rounded-lg font-semibold text-sm border <?= $statusColors[$user->status] ?? 'bg-slate-100 text-slate-700 border-slate-200' ?>">
        <?= htmlspecialchars(ucfirst($user->status)) ?>
      </span>
    </div>
    <p class="text-slate-600">Periksa detail booking sebelum melakukan verifikasi.</p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="lg:col-span-2 space-y-2">
      <div class="bg-white rounded-2xl shadow-lg p-8 space-y-4">
        <h2 class="text-xl font-bold text-slate-800 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-file-icon lucide-file mr-2 text-emerald-600">
            <path
              d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z" />
            <path d="M14 2v5a1 1 0 0 0 1 1h5" />
          </svg>
          Detail User
        </h2>

        <div class="space-y-4">
          <div class="p-4 bg-slate-50 rounded-xl">
            <p class="text-xs font-semibold text-slate-500 uppercase">Nama User</p>
            <p class="text-lg font-bold text-slate-800 capitalize">
              <?= htmlspecialchars($user?->nama ?? 'Tidak diketahui') ?>
            </p>
            <p class="text-sm text-slate-600">
              Email: <?= nl2br(htmlspecialchars($user->email)) ?>
            </p>
            <p class="text-sm text-slate-600 capitalize">
              Nomor telepon: <?= nl2br(htmlspecialchars($user->nomor_hp ?? '-')) ?>
            </p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-4">
            <div class="p-4 bg-slate-50 rounded-xl">
              <p class="text-xs font-semibold text-slate-500 uppercase">Role User</p>
              <p class="text-lg font-bold text-slate-800">
                <?= htmlspecialchars($user->nama_role ?? (string) $user->id_role) ?>
              </p>
            </div>
          </div>

          <div class="space-y-4">
            <div class="p-4 bg-slate-50 rounded-xl">
              <p class="text-xs font-semibold text-slate-500 uppercase">
                <?= $user->nim ? 'NIM' : 'NIP' ?>
                User
              </p>
              <p class="text-lg font-bold text-slate-800">
                <?= htmlspecialchars($user->nim ?? $user->nip ?? '-') ?>
              </p>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-4">
            <div class="p-4 bg-slate-50 rounded-xl">
              <p class="text-xs font-semibold text-slate-500 uppercase">Jurusan</p>
              <p class="text-lg font-bold text-slate-800">
                <?= htmlspecialchars($user->jurusan ?? '-') ?>
              </p>
            </div>
          </div>

          <div class="space-y-4">
            <div class="p-4 bg-slate-50 rounded-xl">
              <p class="text-xs font-semibold text-slate-500 uppercase">Jumlah Peringatan</p>
              <p class="text-lg font-bold text-slate-800">
                <?= htmlspecialchars($user->peringatan ?? '-') ?>
              </p>
            </div>
          </div>

        </div>

        <div class="space-y-4">
          <div class="p-4 bg-slate-50 rounded-xl">
            <p class="text-xs font-semibold text-slate-500 uppercase">Status User</p>
            <p class="text-lg font-bold text-slate-800 capitalize">
              <?= htmlspecialchars($user?->status ?? 'Tidak diketahui') ?>
            </p>
            <p class="text-sm text-slate-600 capitalize">
              Bukti KuBaca PNJ:
              <?= $user->kubaca_img ? 'Uploaded: ' . htmlspecialchars($user->kubaca_img) : 'Not uploaded' ?>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

      <div class="lg:col-span-2 space-y-2">
      <div class="bg-white rounded-2xl shadow-lg p-8 space-y-4">
        <h2 class="text-xl font-bold text-slate-800 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-file-icon lucide-file mr-2 text-emerald-600">
            <path
              d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z" />
            <path d="M14 2v5a1 1 0 0 0 1 1h5" />
          </svg>
          Actions
        </h2>
        <div class="space-y-4">
          <div class="flex items-center justify-around mt-6">
            <form method="post" action="/admin/users/delete" onsubmit="return confirm('Delete this room?');">
              <?= Csrf::field() ?>
                <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
                <button type="submit"
              class="px-8 py-2 font-medium bg-red-200 rounded-lg border border-red-400 text-red-900 hover:bg-red-400 transition-all focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 cursor-pointer"
              >
                Delete User
              </button>
            </form>

            <form method="post" action="/admin/users/reset-password" onsubmit="return confirm('Delete this room?');">
              <?= Csrf::field() ?>
                <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
                <button type="submit"
              class="px-8 py-2 font-medium bg-red-200 rounded-lg border border-red-400 text-red-900 hover:bg-red-400 transition-all focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 cursor-pointer"
              >
                Reset Password
              </button>
            </form>
            
            <?php if ($user->status !== 'suspended'): ?>
              <form method="post" action="/admin/users/suspend">
                <?= Csrf::field() ?>
                <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
                <button type="submit"
                class="px-8 py-2 font-medium bg-yellow-200 rounded-lg border border-yellow-400 text-yellow-900 hover:bg-yellow-400 transition-all focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 cursor-pointer"
                >
                  Suspend User
                </button>
              </form>
            <?php else: ?>

              <form method="post" action="/admin/users/unsuspend">
                <?= Csrf::field() ?>
                <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
                <button type="submit"
                  class="px-8 py-2 font-medium bg-emerald-200 rounded-lg border border-emerald-400 text-emerald-900 hover:bg-emerald-400 transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 cursor-pointer"
                >
                  Unsuspend User
                </button>
              </form>
            <?php endif; ?>
          </div>

        </div>
      </div>
    </div>


  <section class="bg-white shadow rounded-lg p-6 mb-8 border border-gray-100 grow h-max">
    <h2>Actions</h2>
    <form method="post" action="/admin/users/reset-password">
      <?= Csrf::field() ?>
      <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
      <button type="submit">Reset Password</button>
    </form>
    <?php if ($user->status !== 'suspended'): ?>
      <form method="post" action="/admin/users/suspend">
        <?= Csrf::field() ?>
        <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
        <button type="submit">Suspend User</button>
      </form>
    <?php else: ?>
      <form method="post" action="/admin/users/unsuspend">
        <?= Csrf::field() ?>
        <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
        <button type="submit">Unsuspend User</button>
      </form>
    <?php endif; ?>
    <form method="post" action="/admin/users/approve-kubaca">
      <?= Csrf::field() ?>
      <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
      <button type="submit">Approve KuBaca</button>
    </form>
    <form method="post" action="/admin/users/reject-kubaca">
      <?= Csrf::field() ?>
      <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
      <label>
        Reason (optional)
        <input type="text" name="reason">
      </label>
      <button type="submit">Reject KuBaca</button>
    </form>
    <form method="post" action="/admin/users/delete" onsubmit="return confirm('Delete this user?');">
      <?= Csrf::field() ?>
      <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
      <button type="submit">Delete User</button>
    </form>
  </section>
</div>