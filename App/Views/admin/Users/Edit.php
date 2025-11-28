<?php
use App\Core\App;
use App\Core\Csrf;
use App\Models\User;
/** @var User $model */

$roles = $roles ?? [];
$statuses = $statuses ?? [];
?>

<body>
  <h1>Edit User</h1>
  <p><a href="/admin/users">Back to list</a></p>

  <?php if ($message = App::$app->session->getFlash('success')): ?>
    <p style="color: green;"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <?php if ($message = App::$app->session->getFlash('error')): ?>
    <p style="color: red;"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <section>
    <h2>Quick Actions</h2>
    <form method="post" action="/admin/users/reset-password">
      <?= csrf_field() ?>
      <input type="hidden" name="id_user" value="<?= $model->id_user ?>">
      <button type="submit">Reset Password</button>
    </form>
    <?php if ($model->status !== 'suspended'): ?>
      <form method="post" action="/admin/users/suspend">
        <?= csrf_field() ?>
        <input type="hidden" name="id_user" value="<?= $model->id_user ?>">
        <button type="submit">Suspend User</button>
      </form>
    <?php else: ?>
      <form method="post" action="/admin/users/unsuspend">
        <?= csrf_field() ?>
        <input type="hidden" name="id_user" value="<?= $model->id_user ?>">
        <button type="submit">Unsuspend User</button>
      </form>
    <?php endif; ?>
    <form method="post" action="/admin/users/approve-kubaca">
      <?= csrf_field() ?>
      <input type="hidden" name="id_user" value="<?= $model->id_user ?>">
      <button type="submit">Approve KuBaca</button>
    </form>
    <form method="post" action="/admin/users/reject-kubaca">
      <?= csrf_field() ?>
      <input type="hidden" name="id_user" value="<?= $model->id_user ?>">
      <label>
        Reason (optional)
        <input type="text" name="reason">
      </label>
      <button type="submit">Reject KuBaca</button>
    </form>
  </section>

  <hr>

  <form action="/admin/users/update" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="id_user" value="<?= $model->id_user ?>">

    <fieldset>
      <legend>Identity</legend>
      <label>
        Nama
        <input type="text" name="nama" value="<?= htmlspecialchars($model->nama ?? '') ?>">
      </label>
      <?php if ($model->hasError('nama')): ?>
        <p style="color: red;"><?= htmlspecialchars($model->getFirstError('nama')) ?></p>
      <?php endif; ?>

      <label>
        Email
        <input type="email" name="email" value="<?= htmlspecialchars($model->email ?? '') ?>">
      </label>
      <?php if ($model->hasError('email')): ?>
        <p style="color: red;"><?= htmlspecialchars($model->getFirstError('email')) ?></p>
      <?php endif; ?>

      <label>
        Phone Number
        <input type="text" name="nomor_hp" value="<?= htmlspecialchars($model->nomor_hp ?? '') ?>">
      </label>
      <?php if ($model->hasError('nomor_hp')): ?>
        <p style="color: red;"><?= htmlspecialchars($model->getFirstError('nomor_hp')) ?></p>
      <?php endif; ?>

      <label>
        Jurusan
        <input type="text" name="jurusan" value="<?= htmlspecialchars($model->jurusan ?? '') ?>">
      </label>
      <?php if ($model->hasError('jurusan')): ?>
        <p style="color: red;"><?= htmlspecialchars($model->getFirstError('jurusan')) ?></p>
      <?php endif; ?>
    </fieldset>

    <fieldset>
      <legend>Academic Identifier</legend>
      <label>
        NIM (Mahasiswa)
        <input type="text" name="nim" value="<?= htmlspecialchars($model->nim ?? '') ?>">
      </label>
      <?php if ($model->hasError('nim')): ?>
        <p style="color: red;"><?= htmlspecialchars($model->getFirstError('nim')) ?></p>
      <?php endif; ?>

      <label>
        NIP (Dosen)
        <input type="text" name="nip" value="<?= htmlspecialchars($model->nip ?? '') ?>">
      </label>
      <?php if ($model->hasError('nip')): ?>
        <p style="color: red;"><?= htmlspecialchars($model->getFirstError('nip')) ?></p>
      <?php endif; ?>
    </fieldset>

    <fieldset>
      <legend>Access & Status</legend>
      <label>
        Role
        <select name="id_role">
          <option value="">Select role</option>
          <?php foreach ($roles as $role): ?>
            <?php $value = (string) ($role['id_role'] ?? ''); ?>
            <option value="<?= htmlspecialchars($value) ?>" <?= (string) ($model->id_role ?? '') === $value ? 'selected' : '' ?>>
              <?= htmlspecialchars($role['nama_role'] ?? 'Role') ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>
      <?php if ($model->hasError('id_role')): ?>
        <p style="color: red;"><?= htmlspecialchars($model->getFirstError('id_role')) ?></p>
      <?php endif; ?>

      <label>
        Status
        <select name="status">
          <?php foreach ($statuses as $status): ?>
            <option value="<?= htmlspecialchars($status) ?>" <?= ($model->status ?? '') === $status ? 'selected' : '' ?>>
              <?= htmlspecialchars(ucwords($status)) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>
        Peringatan
        <input type="number" name="peringatan" min="0"
          value="<?= htmlspecialchars((string) ($model->peringatan ?? 0)) ?>">
      </label>
    </fieldset>

    <fieldset>
      <legend>Security</legend>
      <p>Leave the password fields empty if you do not want to change the current password.</p>
      <label>
        Password
        <input type="password" name="password">
      </label>
      <?php if ($model->hasError('password')): ?>
        <p style="color: red;"><?= htmlspecialchars($model->getFirstError('password')) ?></p>
      <?php endif; ?>

      <label>
        Confirm Password
        <input type="password" name="confirm_password">
      </label>
      <?php if ($model->hasError('confirm_password')): ?>
        <p style="color: red;"><?= htmlspecialchars($model->getFirstError('confirm_password')) ?></p>
      <?php endif; ?>
    </fieldset>

    <?php if ($model->kubaca_img): ?>
      <p>Current KuBaca: <?= htmlspecialchars($model->kubaca_img) ?></p>
    <?php else: ?>
      <p>KuBaca has not been uploaded.</p>
    <?php endif; ?>

    <button type="submit">Save Changes</button>
  </form>