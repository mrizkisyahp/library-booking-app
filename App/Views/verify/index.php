<?php
/** @var \App\Models\VerificationForm $model */
use App\Core\App;
use App\Core\Csrf;
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

        <?php if ($devOtp = App::$app->session->get('dev_otp_display')): ?>
          <div style="border: 2px solid orange; padding: 20px; margin: 20px 0; background: #fff3cd;">
            <h3 style="color: #856404;">DEVELOPMENT MODE - OTP Code</h3>
            <p><strong>User:</strong> <?= htmlspecialchars($devOtp['user']) ?> (<?= htmlspecialchars($devOtp['email']) ?>)
            </p>
            <p><strong>OTP Code:</strong> <span
                style="font-size: 32px; color: #d63384; font-weight: bold; letter-spacing: 5px;"><?= htmlspecialchars($devOtp['otp']) ?></span>
            </p>
            <p><strong>Purpose:</strong>
              <?= $devOtp['purpose'] === 'reset_password' ? 'Password Reset' : 'Account Verification' ?></p>
            <p style="color: #856404;"><em>In production mode, this will be sent via email.</em></p>
          </div>
          <?php App::$app->session->remove('dev_otp_display'); ?>
        <?php endif; ?>

        <form action="/verify" method="post">
          <?= Csrf::field() ?>

          <div class="mb-4 mt-6">
            <label
              class="block text-sm font-medium capitalize <?= $model->hasError('code') ? 'text-red-700' : 'text-gray-700' ?> mb-2"
              for="code">Kode Verifikasi:</label>
            <input id="code" type="text" name="code" value="<?= htmlspecialchars($model->code ?? '') ?>"
              placeholder="Masukkan kode 6 digit"
              class="w-full text-sm text-gray-500 px-3 py-2 rounded-lg border shadow-sm focus:outline-none focus:ring-2 <?= $model->hasError('code') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500' ?>" />
            <?php if ($model->hasError('code')): ?>
              <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($model->getFirstError('code')) ?></p>
            <?php endif; ?>
          </div>

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