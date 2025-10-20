<?php
/** @var \App\Models\User $model */
use App\Core\App;
use App\Core\Csrf;
?>

<!-- Disini za buat styling css sama atur2 margin lah -->

<h2>Register Dosen</h2>

<?php if ($m = App::$app->session->getFlash('success')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<?php if ($m = App::$app->session->getFlash('error')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<form action="/register/dosen" method="post">
  <?= Csrf::field() ?>
  
  <div class="mb-4">
    <label class="block text-sm font-medium <?= $model->hasError('nama') ? 'text-red-700' : 'text-gray-700' ?> mb-2" for="nama">Nama Lengkap</label>
    <input id="nama" type="text" name="nama" value="<?= htmlspecialchars($model->nama ?? '') ?>" class="w-full px-3 py-2 rounded-lg border shadow-sm focus:outline-none focus:ring-2 <?= $model->hasError('nama') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500' ?>" />
    <?php if ($model->hasError('nama')): ?>
      <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($model->getFirstError('nama')) ?></p>
    <?php endif; ?>
  </div>

  <div class="mb-4">
    <label class="block text-sm font-medium <?= $model->hasError('nip') ? 'text-red-700' : 'text-gray-700' ?> mb-2" for="nip">NIP (18 digits)</label>
    <input id="nip" type="text" name="nip" value="<?= htmlspecialchars($model->nip ?? '') ?>" class="w-full px-3 py-2 rounded-lg border shadow-sm focus:outline-none focus:ring-2 <?= $model->hasError('nip') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500' ?>" />
    <?php if ($model->hasError('nip')): ?>
      <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($model->getFirstError('nip')) ?></p>
    <?php endif; ?>
  </div>

  <div class="mb-4">
    <label class="block text-sm font-medium <?= $model->hasError('email') ? 'text-red-700' : 'text-gray-700' ?> mb-2" for="email">Email PNJ</label>
    <input id="email" type="email" name="email" value="<?= htmlspecialchars($model->email ?? '') ?>" class="w-full px-3 py-2 rounded-lg border shadow-sm focus:outline-none focus:ring-2 <?= $model->hasError('email') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500' ?>" />
    <?php if ($model->hasError('email')): ?>
      <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($model->getFirstError('email')) ?></p>
    <?php endif; ?>
  </div>

  <div class="mb-4">
    <label class="block text-sm font-medium <?= $model->hasError('password') ? 'text-red-700' : 'text-gray-700' ?> mb-2" for="password">Password</label>
    <input id="password" type="password" name="password" value="" class="w-full px-3 py-2 rounded-lg border shadow-sm focus:outline-none focus:ring-2 <?= $model->hasError('password') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500' ?>" />
    <?php if ($model->hasError('password')): ?>
      <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($model->getFirstError('password')) ?></p>
    <?php endif; ?>
  </div>

  <div class="mb-4">
    <label class="block text-sm font-medium <?= $model->hasError('confirm_password') ? 'text-red-700' : 'text-gray-700' ?> mb-2" for="confirm_password">Confirm Password</label>
    <input id="confirm_password" type="password" name="confirm_password" value="" class="w-full px-3 py-2 rounded-lg border shadow-sm focus:outline-none focus:ring-2 <?= $model->hasError('confirm_password') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500' ?>" />
    <?php if ($model->hasError('confirm_password')): ?>
      <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($model->getFirstError('confirm_password')) ?></p>
    <?php endif; ?>
  </div>

  <div class="cf-turnstile" data-sitekey="<?= $_ENV['TURNSTILE_SITE']; ?>"></div>
  <button type="submit">Register</button>
</form>

<p>Already have an account? <a href="/login">Login</a></p>
<p>Register as Mahasiswa? <a href="/register/mahasiswa">Click here</a></p>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>