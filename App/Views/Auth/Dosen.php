<?php

$validator = $validator ?? null;
?>

<div class="flex items-center justify-center min-h-dvh mx-4">
  <div>
    <div class="px-4 md:px-0 py-8">
      <p class="text-4xl md:text-2xl text-white font-semibold capitalize">Buat Akun Dosen</p>
    </div>

    <div class="container w-fit mx-auto align-middle px-12 py-6 bg-gray-100 rounded-md shadow border border-gray-300">
      <div class="text-left py-2">
        <h2 class="text-4xl font-semibold capitalize">Registrasi Dosen</h2>
      </div>

      <?php if ($m = flash('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-3">
          <p><?= htmlspecialchars($m) ?></p>
        </div>
      <?php endif; ?>

      <?php if ($m = flash('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-3">
          <p><?= htmlspecialchars($m) ?></p>
        </div>
      <?php endif; ?>

      <div class="mt-6">
        <form action="/register/dosen" method="post">
          <?= csrf_field() ?>

          <?php
          $step2Errors = false;
          $step2FieldNames = ['nama', 'jurusan', 'nomor_hp', 'password', 'confirm_password'];
          foreach ($step2FieldNames as $field) {
            if ($validator?->hasError($field)) {
              $step2Errors = true;
              break;
            }
          }
          ?>
          <div id="step1" class="space-y-4" data-has-step2-errors="<?= $step2Errors ? 'true' : 'false' ?>">

            <div class="mb-4">
              <label for="nip"
                class="block text-sm font-medium <?= $validator?->hasError('nip') ? 'text-red-700' : 'text-gray-700' ?> mb-2">
                NIP (18 digits)
              </label>
              <input id="nip" type="text" name="nip" value=""
                class="w-full px-3 py-2 rounded-lg border shadow-sm bg-white focus:outline-none focus:ring-2 <?= $validator?->hasError('nip') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-emerald-500 focus:ring-offset-2 focus:border-emerald-500 transition-all' ?>" />
              <?php if ($validator?->hasError('nip')): ?>
                <p class="mt-1 text-sm text-red-600">
                  <?= htmlspecialchars($validator?->getFirstError('nip')) ?>
                </p>
              <?php endif; ?>
            </div>

            <div class="mb-4">
              <label for="email"
                class="block text-sm font-medium <?= $validator?->hasError('email') ? 'text-red-700' : 'text-gray-700' ?> mb-2">
                Email PNJ
              </label>
              <input id="email" type="email" name="email" value=""
                class="w-full px-3 py-2 rounded-lg border shadow-sm bg-white focus:outline-none focus:ring-2 <?= $validator?->hasError('email') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-emerald-500 focus:ring-offset-2 focus:border-emerald-500 transition-all' ?>" />
              <?php if ($validator?->hasError('email')): ?>
                <p class="mt-1 text-sm text-red-600">
                  <?= htmlspecialchars($validator?->getFirstError('email')) ?>
                </p>
              <?php endif; ?>
            </div>

            <div class="my-6 flex items-center gap-4">
              <label class=" text-sm font-medium text-gray-700 mb-2 cursor-pointer flex-1">
                <input type="radio" name="role" value="dosen" checked class="peer hidden" />
                <div
                  class="w-full text-center px-8 py-2 rounded-lg border border-gray-400 bg-gray-200 text-gray-600
                        peer-checked:bg-emerald-600 peer-checked:text-white hover:bg-emerald-700 hover:text-white active:text-white active:bg-emerald-800 transition-all">
                  Dosen
                </div>
              </label>

              <label class=" text-sm font-medium text-gray-700 mb-2 cursor-pointer flex-1">
                <input type="radio" name="role" value="tendik" class="peer hidden" />
                <div
                  class="w-full text-center px-8 py-2 rounded-lg border border-gray-400 bg-gray-200 text-gray-600
                        peer-checked:bg-emerald-600 peer-checked:text-white hover:bg-emerald-700 hover:text-white active:text-white active:bg-emerald-800 transition-all">
                  Tenaga Didik
                </div>
              </label>
            </div>

            <button type="button" onclick="nextStep()"
              class="w-full px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 active:bg-emerald-800 transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 cursor-pointer">
              Berikutnya
            </button>
          </div>

          <div id="step2" class="space-y-4 hidden">
            <div class="mb-4">
              <label for="nama"
                class="block text-sm font-medium <?= $validator?->hasError('nama') ? 'text-red-700' : 'text-gray-700' ?> mb-2">
                Nama Lengkap
              </label>
              <input id="nama" type="text" name="nama" value=""
                class="w-full px-3 py-2 rounded-lg border shadow-sm bg-white focus:outline-none focus:ring-2 <?= $validator?->hasError('nama') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-emerald-500 focus:ring-offset-2 focus:border-emerald-500 transition-all' ?>" />
              <?php if ($validator?->hasError('nama')): ?>
                <p class="mt-1 text-sm text-red-600">
                  <?= htmlspecialchars($validator?->getFirstError('nama')) ?>
                </p>
              <?php endif; ?>
            </div>

            <div class="mb-4">
              <?php
              $Jurusan = [
                'Teknik Elektro',
                'Teknik Mesin',
                'Teknik Sipil',
                'Akuntansi',
                'Administrasi Niaga',
                'Teknik Grafika dan Penerbitan',
              ];
              ?>
              <label for="jurusan"
                class="block text-sm font-medium <?= $validator?->hasError('jurusan') ? 'text-red-700' : 'text-gray-700' ?> mb-2">
                Jurusan
              </label>
              <select id="jurusan" name="jurusan"
                class="w-full px-3 py-2 rounded-lg border shadow-sm bg-white focus:outline-none focu:ring-2 <?= $validator?->hasError('jurusan') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-emerald-500 focus:ring-offset-2 focus:border-emerald-500 transition-all' ?>">
                <option value="Teknik Informatika dan Komputer">Teknik Informatika dan Komputer</option>
                <?php foreach ($Jurusan as $option): ?>
                  <option value="<?= htmlspecialchars($option) ?>" <?= ($model->jurusan ?? '') === $option ? 'selected' : '' ?>>
                    <?= htmlspecialchars($option) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <?php if ($validator?->hasError('jurusan')): ?>
                <p class="mt-1 text-sm text-red-600">
                  <?= htmlspecialchars($validator?->getFirstError('jurusan')) ?>
                </p>
              <?php endif; ?>
            </div>

            <div class="mb-4">
              <label for="nomor_hp"
                class="block text-sm font-medium <?= $validator?->hasError('nomor_hp') ? 'text-red-700' : 'text-gray-700' ?> mb-2">
                Nomor HP
              </label>
              <input id="nomor_hp" type="tel" name="nomor_hp" value=""
                class="w-full px-3 py-2 rounded-lg border shadow-sm bg-white focus:outline-none focus:ring-2 <?= $validator?->hasError('nomor_hp') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-emerald-500 focus:ring-offset-2 focus:border-emerald-500 transition-all' ?>" />
              <?php if ($validator?->hasError('nomor_hp')): ?>
                <p class="mt-1 text-sm text-red-600">
                  <?= htmlspecialchars($validator?->getFirstError('nomor_hp')) ?>
                </p>
              <?php endif; ?>
            </div>

            <div class="mb-4">
              <label for="password"
                class="block text-sm font-medium <?= $validator?->hasError('password') ? 'text-red-700' : 'text-gray-700' ?> mb-2">
                Password
              </label>

              <div class="flex items-center gap-2 w-full border rounded-lg px-3 py-2 bg-white shadow-sm
            <?= $validator?->hasError('password')
              ? 'border-red-500 focus-within:ring-red-500'
              : 'border-gray-300 focus-within:ring-emerald-500 focus:ring-offset-2 focus:border-emerald-500 transition-all'
              ?>"> <input id="password" type="password" name="password" value=""
                  class="w-full outline-none bg-transparent" />

                <button type="button" onclick="togglePassword('password')">

                  <svg id="eyesopen-password" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="lucide lucide-eye-icon lucide-eye text-emerald-600">
                    <path
                      d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                    <circle cx="12" cy="12" r="3" />
                  </svg>

                  <svg id="eyesclosed-password" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round"
                    class="lucide lucide-eye-closed-icon lucide-eye-closed hidden text-emerald-600">
                    <path d="m15 18-.722-3.25" />
                    <path d="M2 8a10.645 10.645 0 0 0 20 0" />
                    <path d="m20 15-1.726-2.05" />
                    <path d="m4 15 1.726-2.05" />
                    <path d="m9 18 .722-3.25" />
                  </svg>
                </button>
              </div>
              <?php if ($validator?->hasError('password')): ?>
                <p class="mt-1 text-sm text-red-600">
                  <?= htmlspecialchars($validator?->getFirstError('password')) ?>
                </p>
              <?php endif; ?>
            </div>

            <div class="mb-4">
              <label for="confirm_password"
                class="block text-sm font-medium <?= $validator?->hasError('confirm_password') ? 'text-red-700' : 'text-gray-700' ?> mb-2">
                Konfirmasi Password
              </label>

              <div
                class="flex items-center gap-2 w-full border rounded-lg px-3 py-2 shadow-sm bg-white <?= $validator?->hasError('confirm_password') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-emerald-500 focus:ring-offset-2 focus:border-emerald-500 transition-all' ?>">

                <input id="confirm_password" type="password" name="confirm_password" value=""
                  class="w-full outline-none bg-transparent " />

                <button type="button" onclick="togglePassword('confirm_password')">

                  <svg id="eyesopen-confirm_password" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="lucide lucide-eye-icon lucide-eye text-emerald-600">
                    <path
                      d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                    <circle cx="12" cy="12" r="3" />
                  </svg>

                  <svg id="eyesclosed-confirm_password" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round"
                    class="lucide lucide-eye-closed-icon lucide-eye-closed hidden text-emerald-600">
                    <path d="m15 18-.722-3.25" />
                    <path d="M2 8a10.645 10.645 0 0 0 20 0" />
                    <path d="m20 15-1.726-2.05" />
                    <path d="m4 15 1.726-2.05" />
                    <path d="m9 18 .722-3.25" />
                  </svg>
                </button>
              </div>
              <?php if ($validator?->hasError('confirm_password')): ?>
                <p class="mt-1 text-sm text-red-600">
                  <?= htmlspecialchars($validator?->getFirstError('confirm_password')) ?>
                </p>
              <?php endif; ?>

            </div>

            <div class="cf-turnstile mt-4 p-2 flex justify-center" data-sitekey="<?= $_ENV['TURNSTILE_SITE']; ?>"
              data-theme="light" data-size="normal" data-callback="onSuccess"></div>

            <div class="flex items-center gap-3 mt-6">
              <button type="button" onclick="prevStep()"
                class="px-8 py-2 w-full bg-gray-300 text-gray-700 capitalize text-lg font-medium rounded-md shadow cursor-pointer hover:bg-gray-400 hover:ring-2 hover:ring-gray-500 hover:ring-offset-2 active:bg-gray-400 active:ring-2 active:ring-gray-500 active:ring-offset-2 transition-all pointer-cursor">
                Sebelumnya
              </button>

              <button type="submit"
                class="px-8 py-2 w-full bg-primary text-white capitalize text-lg font-medium rounded-md shadow cursor-pointer hover:bg-emerald-700 hover:ring-2 hover:ring-emerald-500 hover:ring-offset-2 active:bg-emerald-700 active:ring-2 active:ring-emerald-500 active:ring-offset-2 transition-all">
                Daftar
              </button>
            </div>
          </div>
        </form>

        <div class="mt-6 text-center text-sm">
          <p class="font-medium md:font-normal">Sudah punya akun?</p>
          <a href="/login" class="italic capitalize text-gray-700 hover:underline active:underline">Masuk di sini</a>
          <p class="mt-2">Daftar sebagai Mahasiswa? <a href="/register/mahasiswa"
              class="hover:underline text-gray-700 font-medium">Klik di sini</a></p>
        </div>
      </div>

      <p class="mt-4 text-sm">© 2025 Politeknik Negeri Jakarta. All rights reserved.</p>
    </div>
  </div>
</div>