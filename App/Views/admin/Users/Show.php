<?php
use App\Core\App;
use App\Core\Csrf;
use App\Models\User;
/** @var User $user */

?>

<body>
  <h1>User Detail</h1>
  <p><a href="/admin/users">Back to list</a></p>

  <?php if ($message = App::$app->session->getFlash('success')): ?>
    <p style="color: green;"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <?php if ($message = App::$app->session->getFlash('error')): ?>
    <p style="color: red;"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <section>
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

  <section>
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
