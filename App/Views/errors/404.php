<?php
/** @var string|null $message */
use App\Core\App;
?>

<div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 p-6 text-center">
  <div class="bg-white shadow-xl rounded-2xl p-10 max-w-lg w-full border border-gray-100">
    <div class="flex flex-col items-center space-y-3 mb-6">
      <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white text-3xl font-bold shadow-lg">
        404
      </div>
      <h1 class="text-3xl font-extrabold text-gray-800">Page Not Found</h1>
      <p class="text-gray-600">We don't seem to find what you're looking for.</p>
    </div>

    <?php if (!empty($message)): ?>
      <div class="bg-red-50 border-l-4 border-red-500 text-left p-4 rounded-lg mb-6">
        <p class="text-sm text-red-700 font-medium">
          <strong>Error Details:</strong> <?= htmlspecialchars($message) ?>
        </p>
      </div>
    <?php endif; ?>

    <a href="/login" class="inline-block px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
      Go to Login
    </a>
  </div>
</div>
