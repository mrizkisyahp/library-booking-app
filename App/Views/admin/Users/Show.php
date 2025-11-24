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

  <section class="bg-white shadow rounded-lg p-6 mb-8 border border-gray-100 grow h-max">
    <h2>Profile</h2>
    <p>ID: <?= htmlspecialchars((string)$user->id_user) ?></p>
    <p>Nama: <?= htmlspecialchars($user->nama) ?></p>
    <p>Email: <?= htmlspecialchars($user->email) ?></p>
    <p>Nomor HP: <?= htmlspecialchars($user->nomor_hp ?? '-') ?></p>
    <p>Role: <?= htmlspecialchars($user->nama_role ?? (string)$user->id_role) ?></p>
    <p>Jurusan: <?= htmlspecialchars($user->jurusan ?? '-') ?></p>
    <p>NIM: <?= htmlspecialchars($user->nim ?? '-') ?></p>
    <p>NIP: <?= htmlspecialchars($user->nip ?? '-') ?></p>
    <p>Status: <?= htmlspecialchars($user->status) ?></p>
    <p>Peringatan: <?= htmlspecialchars((string)($user->peringatan ?? 0)) ?></p>
    <p>KuBaca: <?= $user->kubaca_img ? 'Uploaded: ' . htmlspecialchars($user->kubaca_img) : 'Not uploaded' ?></p>
  </section>

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