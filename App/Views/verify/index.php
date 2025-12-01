<?php
/** @var \App\Core\Validator\Validator|null $validator */
use App\Core\App;

$validator = $validator ?? null;
?>

<!-- Disini za buat styling css sama atur2 margin lah -->

<div class="flex items-center justify-center min-h-dvh mx-4 ">
  <div>
    <div class="container w-fit mx-auto align-middle px-12 py-4 bg-gray-100 rounded-md shadow border border-gray-300">

      <div>
        <div class="text-center">
          <h2 class="text-2xl font-semibold capitalize">Verifikasi Akun</h2>
          <p class="text-gray-700 text-sm mt-2">
            Buka email yang didaftarkan untuk mendapatkan kode verifikasi
          </p>
        </div>
        <?php if ($m = App::$app->session->getFlash('success')): ?>
          <p><?= htmlspecialchars($m) ?></p>
        <?php endif; ?>

        <?php if ($m = App::$app->session->getFlash('error')): ?>
          <p><?= htmlspecialchars($m) ?></p>
        <?php endif; ?>

        <form action="/verify" method="post">
          <?= csrf_field() ?>

          <div class="mb-4 mt-6">
            <label
              class="block text-sm font-medium capitalize <?= $validator?->hasError('code') ? 'text-red-700' : 'text-gray-700' ?> mb-2"
              for="code">Kode Verifikasi:</label>
            <input id="code" type="text" name="code" value="<?= htmlspecialchars($validator?->code ?? '') ?>"
              placeholder="Masukkan kode 6 digit"
              class="w-full text-sm text-gray-500 px-3 py-2 rounded-lg border shadow-sm focus:outline-none focus:ring-2 <?= $validator?->hasError('code') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500' ?>" />
            <?php if ($validator?->hasError('code')): ?>
              <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($validator->getFirstError('code')) ?></p>
            <?php endif; ?>
          </div>

          <div class="cf-turnstile mt-4 p-2 flex justify-center" data-sitekey="<?= $_ENV['TURNSTILE_SITE']; ?>"
            data-theme="light" data-size="normal" data-callback="onSuccess"></div>
          <div class="mt-6"></div>
          <button type="submit"
            class="px-8 py-2 w-full text-center mt-6 bg-primary text-white capitalize text-md font-medium rounded-md shadow cursor-pointer hover:bg-emerald-700 hover:ring-2 hover:ring-emerald-500 hover:ring-offset-2 active:bg-emerald-700 active:ring-2 active:ring-emerald-500 active:ring-offset-2 transition-all">
            Verifikasi
          </button>
        </form>

        <p class="mt-6 text-center text-sm">
          Tidak mendapatkan kodenya?
          <a href="/resend" class="italic capitalize text-gray-700 hover:underline active:underline mt-6">
            Kirim ulang kode
          </a>
        </p>

      </div>
    </div>

    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>