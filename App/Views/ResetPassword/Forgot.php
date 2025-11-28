<?php
/** @var \App\Models\User $model */
use App\Core\App;
use App\Core\Csrf;
?>

<!-- Disini za buat styling css sama atur2 margin lah -->

<div class="flex items-center justify-center min-h-dvh mx-4 ">
  <div>
    <div class="container w-fit mx-auto align-middle px-12 py-4 bg-gray-100 rounded-md shadow border border-gray-300">
      <div class="text-left py-2">
        <h2 class="text-2xl font-semibold capitalize">Melupakan Password?</h2>
      </div>

      <?php if ($m = App::$app->session->getFlash('success')): ?>
        <p><?= htmlspecialchars($m) ?></p>
      <?php endif; ?>

      <?php if ($m = App::$app->session->getFlash('error')): ?>
        <p><?= htmlspecialchars($m) ?></p>
      <?php endif; ?>

      <p>Masukkan email untuk mendapatkan kode perubahan password</p>

      <div class="mt-6">
        <form action="/forgot" method="post">
          <?= csrf_field() ?>

          <div>

            <div class="mt-9">
              <input id="email" type="email" name="email" value="<?= htmlspecialchars($model->email ?? '') ?>"
                class="bg-white w-full px-3 py-2 rounded-lg border shadow-sm focus:outline-none focus:ring-2 <?= $model->hasError('email') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 focus:ring-offset-2 transition-all' ?>" />
              <?php if ($model->hasError('email')): ?>
                <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($model->getFirstError('email')) ?></p>
              <?php endif; ?>
            </div>

            <button type="submit"
              class="px-8 py-2 w-full mt-4 bg-primary text-white capitalize text-lg font-medium rounded-md shadow cursor-pointer hover:bg-emerald-700 hover:ring-2 hover:ring-emerald-500 hover:ring-offset-2 active:bg-emerald-700 active:ring-2 active:ring-emerald-500 active:ring-offset-2 transition-all">
              Kirim Kode
            </button>
          </div>
      </div>
      </form>

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