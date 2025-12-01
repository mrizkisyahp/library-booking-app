<?php
/** @var \App\Core\Validator\Validator|null $validator */
use App\Core\App;

$validator = $validator ?? null;
?>

<!-- Disini za buat styling css sama atur2 margin lah -->

<div class="flex items-center justify-center min-h-dvh mx-4 ">
  <div>
    <div class="container w-fit mx-auto align-middle px-12 py-4 bg-gray-100 rounded-md shadow border border-gray-300">
      <div class="text-left py-2">
        <h2 class="text-2xl font-semibold capitalize">Reset Password</h2>
      </div>

      <?php if ($m = App::$app->session->getFlash('success')): ?>
        <p><?= htmlspecialchars($m) ?></p>
      <?php endif; ?>

      <?php if ($m = App::$app->session->getFlash('error')): ?>
        <p><?= htmlspecialchars($m) ?></p>
      <?php endif; ?>

      <p>Masukkan kode verifikasi dan atur password barumu.</p>

      <div class="mt-6">
        <form action="/reset" method="post">
          <?= csrf_field() ?>

          <div>
            <div class="mt-6">
              <input id="code" type="text" name="code" placeholder="Kode verifikasi"
                class="bg-white w-full px-3 py-2 rounded-lg border shadow-sm focus:outline-none focus:ring-2 <?= $validator?->hasError('code') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 focus:ring-offset-2 transition-all' ?>" />
              <?php if ($validator?->hasError('code')): ?>
                <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($validator->getFirstError('code')) ?></p>
              <?php endif; ?>
            </div>

            <div class="mt-6">
              <input id="password" type="password" name="new_password" placeholder="Password baru"
                class="bg-white w-full px-3 py-2 rounded-lg border shadow-sm focus:outline-none focus:ring-2 <?= $validator?->hasError('new_password') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 focus:ring-offset-2 transition-all' ?>" />
              <?php if ($validator?->hasError('new_password')): ?>
                <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($validator->getFirstError('new_password')) ?>
                </p>
              <?php endif; ?>
            </div>

            <div class="mt-6">
              <input id="confirm_password" type="password" name="confirm_new_password" placeholder="Konfirmasi password"
                class="bg-white w-full px-3 py-2 rounded-lg border shadow-sm focus:outline-none focus:ring-2 <?= $validator?->hasError('confirm_new_password') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 focus:ring-offset-2 transition-all' ?>" />
              <?php if ($validator?->hasError('confirm_new_password')): ?>
                <p class="mt-1 text-sm text-red-600">
                  <?= htmlspecialchars($validator->getFirstError('confirm_new_password')) ?>
                </p>
              <?php endif; ?>
            </div>

            <div class="cf-turnstile mt-4 p-2 flex justify-center" data-sitekey="<?= $_ENV['TURNSTILE_SITE']; ?>"
              data-theme="light" data-size="normal" data-callback="onSuccess"></div>
            <div class="mt-6"></div>
            <button type="submit"
              class="px-8 py-2 w-full mt-6 bg-primary text-white capitalize text-lg font-medium rounded-md shadow cursor-pointer hover:bg-emerald-700 hover:ring-2 hover:ring-emerald-500 hover:ring-offset-2 active:bg-emerald-700 active:ring-2 active:ring-emerald-500 active:ring-offset-2 transition-all">
              Reset Password
            </button>
          </div>
        </form>
      </div>

      <div class="mt-6 text-center text-sm">
        <div>
          <a href="/login" class="italic capitalize text-gray-700 hover:underline active:underline">
            ← Kembali ke halaman awal
          </a>
        </div>
      </div>

      <p class="mt-6 text-sm text-center">© 2025 Politeknik Negeri Jakarta. All rights reserved.</p>
    </div>
  </div>
</div>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>