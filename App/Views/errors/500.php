<?php
/** @var string|null $message */
/** @var string|null $trace */
use App\Core\App;
?>

<div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 p-6 text-center">
  <div class="bg-white shadow-xl rounded-2xl p-10 max-w-3xl w-full border border-gray-100">
    <div class="flex flex-col items-center space-y-3 mb-6">
      <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-yellow-500 to-orange-500 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
        500
      </div>
      <h1 class="text-3xl font-extrabold text-gray-800">Server Error</h1>
      <p class="text-gray-600">Something went wrong on our end.</p>
    </div>

    <?php if (!empty($message)): ?>
      <div class="bg-red-50 border-l-4 border-red-500 text-left p-4 rounded-lg mb-6">
        <p class="text-sm text-red-700 font-medium">
          <strong>Error:</strong> <?= htmlspecialchars($message) ?>
        </p>
      </div>
    <?php endif; ?>

    <?php if (!empty($trace)): ?>
      <pre class="bg-gray-100 border border-gray-200 rounded-lg text-left text-xs text-gray-800 p-4 overflow-auto max-h-80 whitespace-pre-wrap"><?= htmlspecialchars($trace) ?></pre>
    <?php endif; ?>

    <a href="/login" class="inline-block mt-8 px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-xl shadow transition-all">
      Go to Login
    </a>
  </div>
</div>
