<?php
use App\Core\App;
use App\Core\Csrf;
use App\Models\User;
/** @var User $user */

?>

<body>
  <div class="p-6">
  <div class="mb-8 flex flex-col md:flex-row justify-between items-center">
    <div class="flex gap-4 items-center">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
        class="lucide lucide-move-left-icon lucide-move-left">
        <path d="M6 8L2 12L6 16" />
        <path d="M2 12H22" />
      </svg>
      <a href="/admin/feedback">Kembali ke daftar</a>
    </div>
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Detail User</h1>
    <!-- empty div -->
    <div></div>
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