<?php
/** @var \App\Models\PasswordResetForm $model */
use App\Core\App;
use App\Core\Csrf;
?>

<!-- Disini za buat styling css sama atur2 margin lah -->

<h2>Forgot Password</h2>

<?php if ($m = App::$app->session->getFlash('success')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<?php if ($m = App::$app->session->getFlash('error')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<p>Enter your email to receive a reset code.</p>

<form action="/forgot" method="post">
  <?= Csrf::field() ?>
  
  <div class="mb-4">
    <label class="block text-sm font-medium <?= $model->hasError('email') ? 'text-red-700' : 'text-gray-700' ?> mb-2" for="email">Email</label>
    <input id="email" type="email" name="email" value="<?= htmlspecialchars($model->email ?? '') ?>" class="w-full px-3 py-2 rounded-lg border shadow-sm focus:outline-none focus:ring-2 <?= $model->hasError('email') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500' ?>" />
    <?php if ($model->hasError('email')): ?>
      <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($model->getFirstError('email')) ?></p>
    <?php endif; ?>
  </div>

  <button type="submit">Send Reset Code</button>
</form>

<p><a href="/login">Back to login</a></p>
