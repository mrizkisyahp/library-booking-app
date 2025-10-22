<?php
/** @var \App\Models\LoginForm $model */
use App\Core\App;
use App\Core\Csrf;
?>

<!-- Disini za buat edit2 - Add your styling here -->

<div class="flex items-center justify-center min-h-dvh">
  <div class="container w-fit mx-auto align-middle px-12 py-4 bg-gray-100 rounded-md shadow border border-gray-300">
    <div class="text-center py-2">
      <h2 class="text-xl font-bold capitalize">Library Booking System</h2>
      <p class="text-sm font-light">Sign in to your account</p>
    </div>

    <?php if ($m = App::$app->session->getFlash('success')): ?>
    <div>
      <p>
        <?= htmlspecialchars($m) ?>
      </p>
    </div>
    <?php endif; ?>

    <?php if ($m = App::$app->session->getFlash('error')): ?>
    <div>
      <p>
        <?= htmlspecialchars($m) ?>
      </p>
    </div>
    <?php endif; ?>

    <div>
      <form action="/login" method="post">
        <?= Csrf::field() ?>

        <div class="mt-2">
          <div class="mb-4">
            <label
              class="block text-sm font-medium <?= $model->hasError('identifier') ? 'text-red-700' : 'text-gray-700' ?> mb-2"
              for="identifier">Email / NIM / NIP</label>
            <input id="identifier" type="text" name="identifier"
              value="<?= htmlspecialchars($model->identifier ?? '') ?>"
              class="bg-white w-full px-3 py-2 rounded-lg border shadow-sm focus:outline-none focus:ring-2 <?= $model->hasError('identifier') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500' ?>" />
            <?php if ($model->hasError('identifier')): ?>
            <p class="mt-1 text-sm text-red-600">
              <?= htmlspecialchars($model->getFirstError('identifier')) ?>
            </p>
            <?php endif; ?>
          </div>

          <div class="mb-4">
            <label
              class="block text-sm font-medium <?= $model->hasError('password') ? 'text-red-700' : 'text-gray-700' ?> mb-2"
              for="password">Password</label>
            <input id="password" type="password" name="password" value=""
              class="w-full px-3 py-2 rounded-lg border shadow-sm bg-white focus:outline-none focus:ring-2 <?= $model->hasError('password') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500' ?>" />
            <?php if ($model->hasError('password')): ?>
            <p class="mt-1 text-sm text-red-600">
              <?= htmlspecialchars($model->getFirstError('password')) ?>
            </p>
            <?php endif; ?>
          </div>

          <div class="flex justify-between items-center mx-4">
            <div class="flex items-center gap-2">
              <input id="remember" name="remember" type="checkbox" value="1">
              <label for="remember" class="text-sm">Remember me</label>
            </div>

            <div>
              <a href="/forgot" class="text-sm">Forgot password?</a>
            </div>
          </div>

          <div class="cf-turnstile mt-4 p-2 flex justify-center" data-sitekey="0x4AAAAAAB7hTzPz5mNjJs1V"
            data-theme="light" data-size="normal" data-callback="onSuccess"></div>
          <div class="mt-4">
            <button type="submit"
              class="px-8 py-2 w-full bg-blue-600 text-white capitalize text-lg font-medium rounded-md shadow cursor-pointer hover:bg-blue-700 hover:ring-2 hover:ring-blue-500 hover:ring-offset-2 transition-all">
              Sign in
            </button>
          </div>
        </div>
      </form>

      <div class="mt-4 text-center text-sm">
        <p class="font-medium">New to the system?</p>
        <div>
          <a href="/register" class="italic text-gray-700">Create new account</a>
        </div>
      </div>
    </div>

    <p class="mt-4 text-sm">© 2025 Politeknik Negeri Jakarta. All rights reserved.</p>
  </div>
</div>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>