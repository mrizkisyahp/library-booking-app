<!-- Back Button -->
<div class="p-4 bg-white shadow-md w-full">
    <div class="flex items-center gap-4 py-4">
        <div class="flex items-center gap-4 ">
            <a href="/profile">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-chevron-left-icon lucide-chevron-left size-9">
                    <path d="m15 18-6-6 6-6" />
                </svg>
            </a>
            <span class="text-black font-bold text-4xl">
                Atur Ulang Kata Sandi
            </span>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto p-6">

    <!-- Header -->
        <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
      <div class="text-left py-2">
        <h2 class="text-2xl font-semibold capitalize">Melupakan Password?</h2>
      </div>

        <p class="text-gray-600">Ubah kata sandi akun Anda. Masukkan email untuk mendapatkan kode pengubahan password</p>

      <div class="mt-6">
        <form action="/forgot" method="post" id="forgotForm">
          <?= csrf_field() ?>

          <div>

            <div class="mt-9">
              <input id="email" type="email" name="email"
                class=" w-full px-3 py-2 rounded-lg border shadow-sm focus:outline-none focus:ring-2 <?= $validator?->hasError('email') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 focus:ring-offset-2 transition-all' ?> bg-gray-100" />
              <?php if ($validator?->hasError('email')): ?>
                <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($validator->getFirstError('email')) ?></p>
              <?php endif; ?>
            </div>

            <div class="cf-turnstile mt-4 p-2 flex justify-center" data-sitekey="<?= config('TURNSTILE_SITE') ?>"
              data-theme="light" data-size="normal" data-callback="onSuccess"></div>
            <div class="mt-6"></div>
            <button type="submit" id="submitBtn"
              class="px-8 py-2 w-full mt-4 bg-primary text-white capitalize text-lg font-medium rounded-md shadow cursor-pointer hover:bg-emerald-700 hover:ring-2 hover:ring-emerald-500 hover:ring-offset-2 active:bg-emerald-700 active:ring-2 active:ring-emerald-500 active:ring-offset-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-primary">
              <span id="btnText">Kirim Kode</span>
              <span id="btnLoading" class="hidden">Mengirim...</span>
            </button>
          </div>
      </div>
      </form>

</div>
