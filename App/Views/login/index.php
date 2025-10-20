<?php
/** @var \App\Models\LoginForm $model */
use App\Core\App;
use App\Core\Csrf;
use App\Core\Form\Form;
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
    <?php $form = Form::begin('/login', 'post'); ?>
      <?= Csrf::field() ?>
      
      <div>
        <?= $form->field($model, 'identifier')->label('Email / NIM / NIP') ?>
        <?= $form->field($model, 'password') ?>

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
          <?= Form::button('Sign in') ?>
        </div>
      </div>
    <?php Form::end(); ?>

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