<?php
/** @var \App\Models\User $model */
use App\Core\App;
use App\Core\Csrf;
?>

<div class="flex items-center justify-center min-h-dvh mx-4">
  <div>
    <div class="px-4 md:px-0 py-8">
      <p class="text-4xl md:text-2xl text-white font-semibold capitalize">Buat Akun Dosen</p>
    </div>

    <div class="container w-fit mx-auto align-middle px-12 py-6 bg-gray-100 rounded-md shadow border border-gray-300">
      <div class="text-left py-2">
        <h2 class="text-2xl font-semibold capitalize">Registrasi Dosen</h2>
      </div>

      <?php if ($m = App::$app->session->getFlash('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-3">
          <p><?= htmlspecialchars($m) ?></p>
        </div>
      <?php endif; ?>

      <?php if ($m = App::$app->session->getFlash('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-3">
          <p><?= htmlspecialchars($m) ?></p>
        </div>
      <?php endif; ?>

      <div class="mt-6">
        <form action="/register/dosen" method="post">
          <?= Csrf::field() ?>

          <div class="mb-4">
            <label for="nama"
              class="block text-sm font-medium <?= $model->hasError('nama') ? 'text-red-700' : 'text-gray-700' ?> mb-2">
              Nama Lengkap
            </label>
            <input id="nama" type="text" name="nama" value="<?= htmlspecialchars($model->nama ?? '') ?>"
              class="w-full px-3 py-2 rounded-lg border shadow-sm bg-white focus:outline-none focus:ring-2 <?= $model->hasError('nama') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-emerald-500 focus:ring-offset-2 focus:border-emerald-500 transition-all' ?>" />
            <?php if ($model->hasError('nama')): ?>
              <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($model->getFirstError('nama')) ?></p>
            <?php endif; ?>
          </div>

          <div class="mb-4">
            <label for="nip"
              class="block text-sm font-medium <?= $model->hasError('nip') ? 'text-red-700' : 'text-gray-700' ?> mb-2">
              NIP (18 digits)
            </label>
            <input id="nip" type="text" name="nip" value="<?= htmlspecialchars($model->nip ?? '') ?>"
              class="w-full px-3 py-2 rounded-lg border shadow-sm bg-white focus:outline-none focus:ring-2 <?= $model->hasError('nip') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-emerald-500 focus:ring-offset-2 focus:border-emerald-500 transition-all' ?>" />
            <?php if ($model->hasError('nip')): ?>
              <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($model->getFirstError('nip')) ?></p>
            <?php endif; ?>
          </div>

          <div class="mb-4">
            <label for="email"
              class="block text-sm font-medium <?= $model->hasError('email') ? 'text-red-700' : 'text-gray-700' ?> mb-2">
              Email PNJ
            </label>
            <input id="email" type="email" name="email" value="<?= htmlspecialchars($model->email ?? '') ?>"
              class="w-full px-3 py-2 rounded-lg border shadow-sm bg-white focus:outline-none focus:ring-2 <?= $model->hasError('email') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-emerald-500 focus:ring-offset-2 focus:border-emerald-500 transition-all' ?>" />
            <?php if ($model->hasError('email')): ?>
              <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($model->getFirstError('email')) ?></p>
            <?php endif; ?>
          </div>

          <div class="mb-4">
            <label for="password"
              class="block text-sm font-medium <?= $model->hasError('password') ? 'text-red-700' : 'text-gray-700' ?> mb-2">
              Password
            </label>
            <input id="password" type="password" name="password" value=""
              class="w-full px-3 py-2 rounded-lg border shadow-sm bg-white focus:outline-none focus:ring-2 <?= $model->hasError('password') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-emerald-500 focus:ring-offset-2 focus:border-emerald-500 transition-all' ?>" />
            <?php if ($model->hasError('password')): ?>
              <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($model->getFirstError('password')) ?></p>
            <?php endif; ?>
          </div>

          <div class="mb-4">
            <label for="confirm_password"
              class="block text-sm font-medium <?= $model->hasError('confirm_password') ? 'text-red-700' : 'text-gray-700' ?> mb-2">
              Konfirmasi Password
            </label>
            <input id="confirm_password" type="password" name="confirm_password" value=""
              class="w-full px-3 py-2 rounded-lg border shadow-sm bg-white focus:outline-none focus:ring-2 <?= $model->hasError('confirm_password') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-emerald-500 focus:ring-offset-2 focus:border-emerald-500 transition-all' ?>" />
            <?php if ($model->hasError('confirm_password')): ?>
              <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($model->getFirstError('confirm_password')) ?></p>
            <?php endif; ?>
          </div>

          <div class="cf-turnstile mt-4 p-2 flex justify-center" data-sitekey="<?= $_ENV['TURNSTILE_SITE']; ?>"
            data-theme="light" data-size="normal" data-callback="onSuccess"></div>

          <div class="mt-6">
            <button type="submit"
              class="px-8 py-2 w-full bg-primary text-white capitalize text-lg font-medium rounded-md shadow cursor-pointer hover:bg-emerald-700 hover:ring-2 hover:ring-emerald-500 hover:ring-offset-2 active:bg-emerald-700 active:ring-2 active:ring-emerald-500 active:ring-offset-2 transition-all">
              Daftar Sekarang
            </button>
          </div>
        </form>

        <div class="mt-6 text-center text-sm">
          <p class="font-medium md:font-normal">Sudah punya akun?</p>
          <a href="/login" class="italic capitalize text-gray-700 hover:underline active:underline">Masuk di sini</a>
          <p class="mt-2">Daftar sebagai Mahasiswa? <a href="/register/mahasiswa" class="hover:underline text-gray-700 font-medium">Klik di sini</a></p>
        </div>
      </div>

      <p class="mt-4 text-sm">© 2025 Politeknik Negeri Jakarta. All rights reserved.</p>
    </div>
  </div>
</div>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
