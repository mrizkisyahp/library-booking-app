<body>
  <h1>Create User</h1>
  <p><a href="/admin/users">Back to list</a></p>

  <?php if ($message = session('success')): ?>
    <p style="color: green;"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <?php if ($message = session('error')): ?>
    <p style="color: red;"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <form action="/admin/users" method="post">
    <?= csrf_field() ?>
    <fieldset>
      <legend>Identity</legend>
      <label>
        Nama
        <input type="text" name="nama" value="" required>
      </label>

      <label>
        Email
        <input type="email" name="email" value="" required>
      </label>

      <label>
        Phone Number
        <input type="text" name="nomor_hp" value="">
      </label>

      <label>
        Jurusan
        <input type="text" name="jurusan" value="">
      </label>
    </fieldset>

    <fieldset>
      <legend>Academic Identifier</legend>
      <label>
        NIM (Mahasiswa)
        <input type="text" name="nim" value="">
      </label>

      <label>
        NIP (Dosen)
        <input type="text" name="nip" value="">
      </label>
    </fieldset>

    <fieldset>
      <legend>Access & Status</legend>
      <label>
        Role
        <select name="id_role" required>
          <option value="">Select role</option>
          <?php foreach ($roles as $role): ?>
            <option value="<?= htmlspecialchars($role->id_role) ?>">
              <?= htmlspecialchars($role->nama_role) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>
        Status
        <select name="status">
          <?php foreach ($statuses as $status): ?>
            <option value="<?= htmlspecialchars($status) ?>" <?= $status === 'active' ? 'selected' : '' ?>>
              <?= htmlspecialchars(ucwords($status)) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>
        Peringatan
        <input type="number" name="peringatan" min="0" value="0">
      </label>
    </fieldset>

    <fieldset>
      <legend>Security</legend>
      <label>
        Password
        <input type="password" name="password" required>
      </label>

      <label>
        Confirm Password
        <input type="password" name="confirm_password" required>
      </label>
    </fieldset>

    <button type="submit">Save User</button>
  </form>