<?php
$validator = $validator ?? null;
$cookie = $rememberedIdentifier;
?>

<div class="flex items-center justify-center min-h-dvh mx-4 ">
  <div>
    <div class="px-4 md:px-0 py-8">
      <p class="text-4xl md:text-2xl text-white font-semibold capitalize">Selamat datang di PinRuPus</p>
    </div>
    <div class="container w-fit mx-auto align-middle px-12 py-4 bg-gray-100 rounded-md shadow border border-gray-300">
      <div class="text-left py-2">
        <h2 class="text-4xl font-bold capitalize">Masuk</h2>
      </div>

      <!-- Flash Messages -->
      <?php if ($m = flash('success')): ?>
        <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
          <?= htmlspecialchars($m) ?>
        </div>
      <?php endif; ?>

      <?php if ($m = flash('error')): ?>
        <div class="mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg">
          <?= htmlspecialchars($m) ?>
        </div>
      <?php endif; ?>

      <div class="mt-6">
        <form action="/login" method="post">
          <?= csrf_field() ?>
          <div class="mt-2">
            <div class="mb-4">
              <label
                class="block text-regular font-medium <?= $validator?->hasError('identifier') ? 'text-red-700' : 'text-gray-700' ?> mb-2"
                for="identifier">Email atau Nomor Induk</label>
              <input id="identifier" type="text" name="identifier" placeholder="Masukkan Email atau Nomor Induk"
                value="<?= htmlspecialchars(old('identifier') ?? $cookie ?? '') ?>"
                class="bg-white w-full px-3 py-2 rounded-lg border shadow-sm focus:outline-none focus:ring-2 placeholder-gray-400 <?= $validator?->hasError('identifier') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-emerald-500 focus:ring-offset-2 focus:border-emerald-500 transition-all' ?>" />
              <?php if ($validator?->hasError('identifier')): ?>
                <p class="mt-1 text-sm text-red-600">
                  <?= htmlspecialchars($validator->getFirstError('identifier')) ?>
                </p>
              <?php endif; ?>
            </div>

            <div class="mb-4 relative">
              <label
                class="block text-sm font-medium <?= $validator?->hasError('password') ? 'text-red-700' : 'text-gray-700' ?> mb-2"
                for="password">Kata Sandi</label>

              <input id="password" type="password" name="password" placeholder="Masukkan Kata Sandi" value=""
                class="w-full px-3 py-2 rounded-lg border shadow-sm bg-white focus:outline-none focus:ring-2 placeholder-gray-400 
                <?= $validator?->hasError('password')
                  ? 'border-red-500 focus:ring-red-500 focus:border-red-500'
                  : 'border-gray-300 focus:ring-emerald-500 focus:ring-offset-2 focus:border-emerald-500 transition-all' ?>" />

              <button type="button"
                class="absolute right-3 items-center top-12 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                onclick="togglePassword('password')">

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

              <?php if ($validator?->hasError('password')): ?>
                <p class="mt-1 text-sm text-red-600">
                  <?= htmlspecialchars($validator->getFirstError('password')) ?>
                </p>
              <?php endif; ?>
            </div>

            <div class="flex justify-between items-center mx-6">
              <div class="flex items-center gap-2">
                <input id="remember" name="remember" type="checkbox" value="1"
                  class="size-4 accent-primary checked:bg-emerald-600 transition-colors cursor-pointer">
                <label for="remember" class="text-sm capitalize">ingat saya</label>
              </div>

              <div class="hover:underline hover:font-medium active:underline">
                <a href="/forgot" class="text-sm capitalize">lupa password?</a>
              </div>
            </div>

            <div class="cf-turnstile mt-4 p-2 flex justify-center" data-sitekey="<?= $_ENV['TURNSTILE_SITE']; ?>"
              data-theme="light" data-size="normal" data-callback="onSuccess"></div>
            <div class="mt-6">
              <button type="submit"
                class="px-8 py-2 w-full bg-primary text-white capitalize text-lg font-medium rounded-md shadow cursor-pointer hover:bg-emerald-700 hover:ring-2 hover:ring-emerald-500 hover:ring-offset-2 active:bg-emerald-700 active:ring-2 active:ring-emerald-500 active:ring-offset-2 transition-all">
                Masuk ke akun
              </button>
            </div>
          </div>
        </form>

        <div class="mt-6 text-center text-sm">
          <p class="font-medium md:font-normal">Belum Punya Akun?</p>
          <div>
            <a href="/register" class="italic capitalize text-gray-700 hover:underline active:underline">
              Daftar Sekarang!
            </a>
          </div>
        </div>
      </div>

      <p class="mt-4 text-sm">© 2025 Politeknik Negeri Jakarta. All rights reserved.</p>
    </div>
  </div>
</div>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>