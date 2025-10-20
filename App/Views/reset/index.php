<?php
/** @var \App\Models\PasswordResetModel $model */
use App\Core\App;
use App\Core\Csrf;
?>

<!-- Disini za buat styling css sama atur2 margin lah -->

<h2>Reset Password</h2>

<?php if ($m = App::$app->session->getFlash('success')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<?php if ($m = App::$app->session->getFlash('error')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<?php if ($devOtp = App::$app->session->get('dev_otp_display')): ?>
  <div style="border: 2px solid orange; padding: 20px; margin: 20px 0; background: #fff3cd;">
    <h3 style="color: #856404;">DEVELOPMENT MODE - OTP Code</h3>
    <p><strong>User:</strong> <?= htmlspecialchars($devOtp['user']) ?> (<?= htmlspecialchars($devOtp['email']) ?>)</p>
    <p><strong>OTP Code:</strong> <span style="font-size: 32px; color: #d63384; font-weight: bold; letter-spacing: 5px;"><?= htmlspecialchars($devOtp['otp']) ?></span></p>
    <p><strong>Purpose:</strong> <?= $devOtp['purpose'] === 'reset_password' ? 'Password Reset' : 'Account Verification' ?></p>
    <p style="color: #856404;"><em>In production mode, this will be sent via email.</em></p>
  </div>
  <?php App::$app->session->remove('dev_otp_display'); ?>
<?php endif; ?>

<p>Enter the verification code and your new password.</p>

<form action="/reset" method="post">
  <?= Csrf::field() ?>
  
  <div class="mb-4">
    <label class="block text-sm font-medium <?= $model->hasError('code') ? 'text-red-700' : 'text-gray-700' ?> mb-2" for="code">Verification Code</label>
    <input id="code" type="text" name="code" value="<?= htmlspecialchars($model->code ?? '') ?>" class="w-full px-3 py-2 rounded-lg border shadow-sm focus:outline-none focus:ring-2 <?= $model->hasError('code') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500' ?>" />
    <?php if ($model->hasError('code')): ?>
      <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($model->getFirstError('code')) ?></p>
    <?php endif; ?>
  </div>

  <div class="mb-4">
    <label class="block text-sm font-medium <?= $model->hasError('password') ? 'text-red-700' : 'text-gray-700' ?> mb-2" for="password">New Password</label>
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

  <button type="submit">Reset Password</button>
</form>

<p><a href="/login">Back to login</a></p>
