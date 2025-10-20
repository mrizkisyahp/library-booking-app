<?php
/** @var \App\Models\LoginForm $model */
use App\Core\App;
use App\Core\Csrf;
?>

<!-- Disini za buat edit2 - Add your styling here -->

<div>
  <div>
    <h2>Library Booking System</h2>
    <p>Sign in to your account</p>
  </div>

  <?php if ($m = App::$app->session->getFlash('success')): ?>
    <div>
      <p><?= htmlspecialchars($m) ?></p>
    </div>
  <?php endif; ?>

  <?php if ($m = App::$app->session->getFlash('error')): ?>
    <div>
      <p><?= htmlspecialchars($m) ?></p>
    </div>
  <?php endif; ?>

  <div>
    <form action="/login" method="post">
      <?= Csrf::field() ?>
      
      <div>
        <div class="mb-4">
          <label class="block text-sm font-medium <?= $model->hasError('identifier') ? 'text-red-700' : 'text-gray-700' ?> mb-2" for="identifier">Email / NIM / NIP</label>
          <input id="identifier" type="text" name="identifier" value="<?= htmlspecialchars($model->identifier ?? '') ?>" class="w-full px-3 py-2 rounded-lg border shadow-sm focus:outline-none focus:ring-2 <?= $model->hasError('identifier') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500' ?>" />
          <?php if ($model->hasError('identifier')): ?>
            <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($model->getFirstError('identifier')) ?></p>
          <?php endif; ?>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium <?= $model->hasError('password') ? 'text-red-700' : 'text-gray-700' ?> mb-2" for="password">Password</label>
          <input id="password" type="password" name="password" value="" class="w-full px-3 py-2 rounded-lg border shadow-sm focus:outline-none focus:ring-2 <?= $model->hasError('password') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500' ?>" />
          <?php if ($model->hasError('password')): ?>
            <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($model->getFirstError('password')) ?></p>
          <?php endif; ?>
        </div>

        <div>
          <div>
            <input id="remember" name="remember" type="checkbox" value="1">
            <label for="remember">Remember me</label>
          </div>

          <div>
            <a href="/forgot">Forgot password?</a>
          </div>
        </div>

        <div
          class="cf-turnstile"
          data-sitekey="0x4AAAAAAB7hTzPz5mNjJs1V"
          data-theme="light"
          data-size="normal"
          data-callback="onSuccess"
        ></div>
        <div>
          <button type="submit">Sign in</button>
        </div>
      </div>
    </form>

    <div>
      <p>New to the system?</p>
      <div>
        <a href="/register">Create new account</a>
      </div>
    </div>
  </div>

  <p>© 2025 Politeknik Negeri Jakarta. All rights reserved.</p>
</div>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>