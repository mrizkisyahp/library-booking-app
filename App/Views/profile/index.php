<?php
use App\Core\App;
use App\Core\Csrf;
$user = App::$app->user;
?>

<!-- Disini za buat styling css sama atur2 margin lah -->

<h2>Profile</h2>

<?php if ($m = App::$app->session->getFlash('success')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<?php if ($m = App::$app->session->getFlash('error')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<?php if ($user): ?>
  <div>
    <p>Name: <?= htmlspecialchars($user->nama) ?></p>
    <p>Email: <?= htmlspecialchars($user->email) ?></p>
    <p>Role: <?= htmlspecialchars($user->role) ?></p>
    <?php if ($user->nim): ?>
      <p>NIM: <?= htmlspecialchars($user->nim) ?></p>
    <?php endif; ?>
    <?php if ($user->nip): ?>
      <p>NIP: <?= htmlspecialchars($user->nip) ?></p>
    <?php endif; ?>
    <p>Status: <?= htmlspecialchars($user->status) ?></p>
    <p>Warnings: <?= htmlspecialchars($user->peringatan) ?></p>
  </div>

  <?php if ($user->role === 'mahasiswa'): ?>
    <?php if ($user->status === 'active' && !$user->kubaca_img): ?>
      <div>
        <h3>Upload KuBaca PNJ</h3>
        <p>Please upload your KuBaca image to complete verification</p>
        <form action="/upload-kubaca" method="post" enctype="multipart/form-data">
          <?= Csrf::field() ?>
          <div>
            <label for="kubaca_img">KuBaca Image</label>
            <input type="file" id="kubaca_img" name="kubaca_img" accept="image/png,image/jpeg,image/webp" required>
          </div>
          <button type="submit">Upload</button>
        </form>
      </div>
    <?php elseif ($user->kubaca_img && $user->status === 'active'): ?>
      <p style="color: orange;">KuBaca image uploaded. Waiting for admin verification.</p>
    <?php elseif ($user->status === 'verified'): ?>
      <p style="color: green;">Your account is fully verified!</p>
    <?php endif; ?>
  <?php elseif ($user->role === 'dosen' && $user->status === 'verified'): ?>
    <p style="color: green;">Your account is fully verified!</p>
  <?php endif; ?>
<?php endif; ?>
