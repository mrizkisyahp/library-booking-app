<body>
  <h1>Edit User</h1>
  <p><a href="/admin/users">Back to list</a></p>

  <?php if ($message = session('success')): ?>
    <p style="color: green;"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <?php if ($message = session('error')): ?>
    <p style="color: red;"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <section>
    <h2>Quick Actions</h2>
    <form method="post" action="/admin/users/reset-password">
      <?= csrf_field() ?>
      <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
      <button type="submit">Reset Password</button>
    </form>
    <?php if ($user->status !== 'suspended'): ?>
      <form method="post" action="/admin/users/suspend">
        <?= csrf_field() ?>
        <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
        <button type="submit">Suspend User</button>
      </form>
    <?php else: ?>
      <form method="post" action="/admin/users/unsuspend">
        <?= csrf_field() ?>
        <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
        <button type="submit">Unsuspend User</button>
      </form>
    <?php endif; ?>
    <?php if ($user->status === 'pending kubaca'): ?>
      <form method="post" action="/admin/users/approve-kubaca">
        <?= csrf_field() ?>
        <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
        <button type="submit">Approve KuBaca</button>
      </form>
      <form method="post" action="/admin/users/reject-kubaca">
        <?= csrf_field() ?>
        <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
        <label>
          Reason (optional)
          <input type="text" name="reason">
        </label>
        <button type="submit">Reject KuBaca</button>
      </form>
    <?php endif; ?>
  </section>

  <hr>

  <form action="/admin/users/update" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="id_user" value="<?= $user->id_user ?>">

    <fieldset>
      <legend>Identity</legend>
      <label>
        Nama
        <input type="text" name="nama" value="<?= htmlspecialchars($user->nama ?? '') ?>">
      </label>

      <label>
        Email
        <input type="email" name="email" value="<?= htmlspecialchars($user->email ?? '') ?>">
      </label>

      <label>
        Phone Number
        <input type="text" name="nomor_hp" value="<?= htmlspecialchars($user->nomor_hp ?? '') ?>">
      </label>

      <label>
        Jurusan
        <input type="text" name="jurusan" value="<?= htmlspecialchars($user->jurusan ?? '') ?>">
      </label>
    </fieldset>

    <fieldset>
      <legend>Academic Identifier</legend>
      <label>
        NIM (Mahasiswa)
        <input type="text" name="nim" value="<?= htmlspecialchars($user->nim ?? '') ?>">
      </label>

      <label>
        NIP (Dosen)
        <input type="text" name="nip" value="<?= htmlspecialchars($user->nip ?? '') ?>">
      </label>
    </fieldset>

    <fieldset>
      <legend>Access & Status</legend>
      <label>
        Role
        <select name="id_role">
          <option value="">Select role</option>
          <?php foreach ($roles as $role): ?>
            <option value="<?= htmlspecialchars($role->id_role) ?>" <?= (int) ($user->id_role ?? 0) === (int) $role->id_role ? 'selected' : '' ?>>
              <?= htmlspecialchars($role->nama_role) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>
        Status
        <select name="status">
          <?php foreach ($statuses as $status): ?>
            <option value="<?= htmlspecialchars($status) ?>" <?= ($user->status ?? '') === $status ? 'selected' : '' ?>>
              <?= htmlspecialchars(ucwords($status)) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>
        Peringatan
        <input type="number" name="peringatan" min="0"
          value="<?= htmlspecialchars((string) ($user->peringatan ?? 0)) ?>">
      </label>
    </fieldset>

    <fieldset>
      <legend>Security</legend>
      <p>Leave the password fields empty if you do not want to change the current password.</p>
      <label>
        Password
        <input type="password" name="password">
      </label>

      <label>
        Confirm Password
        <input type="password" name="confirm_password">
      </label>
    </fieldset>

    <?php if ($user->kubaca_img): ?>
      <p>Current KuBaca: <?= htmlspecialchars($user->kubaca_img) ?></p>
    <?php else: ?>
      <p>KuBaca has not been uploaded.</p>
    <?php endif; ?>

    <button type="submit">Save Changes</button>
  </form>