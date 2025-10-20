<?php
/** @var \App\Models\User $model */
use App\Core\App;
use App\Core\Csrf;
use App\Core\Form\Form;
?>

<!-- Disini za buat styling css sama atur2 margin lah -->

<h2>Register Dosen</h2>

<?php if ($m = App::$app->session->getFlash('success')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<?php if ($m = App::$app->session->getFlash('error')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<?php $form = Form::begin('/register/dosen', 'post'); ?>
  <?= Csrf::field() ?>
  
  <?= $form->field($model, 'nama')->label('Nama Lengkap') ?>
  <?= $form->field($model, 'nip')->label('NIP (18 digits)')->type('text') ?>
  <?= $form->field($model, 'email')->label('Email PNJ') ?>
  <?= $form->field($model, 'password') ?>
  <?= $form->field($model, 'confirm_password')->label('Confirm Password') ?>
  <div class="cf-turnstile" data-sitekey="<?= $_ENV['TURNSTILE_SITE']; ?>"></div>
  <?= Form::button('Register') ?>
<?php Form::end(); ?>

<p>Already have an account? <a href="/login">Login</a></p>
<p>Register as Mahasiswa? <a href="/register/mahasiswa">Click here</a></p>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>