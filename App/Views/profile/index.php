<?php
use App\Core\App;
use App\Core\Csrf;
/** @var \App\Models\User $user */
?>

<div class="max-w-4xl mx-auto p-6">
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Profile</h2>
        <p class="text-gray-600">Kelola informasi akun Anda</p>
    </div>

    <?php if ($user): ?>
        <!-- Profile Information Card -->
        <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100 mb-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-6">Informasi Akun</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Nama</label>
                    <p class="text-gray-900 font-medium"><?= htmlspecialchars($user->nama) ?></p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Email</label>
                    <p class="text-gray-900 font-medium"><?= htmlspecialchars($user->email) ?></p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Role</label>
                    <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 text-sm font-semibold rounded-full">
                        <?= htmlspecialchars($roleLabel ?? 'Unknown') ?>
                    </span>
                </div>
                <?php if ($user->nim): ?>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">NIM</label>
                        <p class="text-gray-900 font-medium"><?= htmlspecialchars($user->nim) ?></p>
                    </div>
                <?php endif; ?>
                <?php if ($user->nip): ?>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">NIP</label>
                        <p class="text-gray-900 font-medium"><?= htmlspecialchars($user->nip) ?></p>
                    </div>
                <?php endif; ?>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Status</label>
                    <span
                        class="inline-block px-3 py-1 <?= $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?> text-sm font-semibold rounded-full">
                        <?= htmlspecialchars(ucfirst($user->status)) ?>
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Peringatan</label>
                    <span
                        class="inline-block px-3 py-1 <?= $user->peringatan > 0 ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' ?> text-sm font-semibold rounded-full">
                        <?= htmlspecialchars($user->peringatan) ?>
                    </span>
                </div>
            </div>
        </div>

        <?php if ($user->isMahasiswa()): ?>
            <?php if ($user->status === 'rejected'): ?>
                <!-- Rejected state - Show upload form again -->
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                    <div class="flex items-start mb-6">
                        <svg class="w-6 h-6 text-red-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">KuBaca Rejected</h3>
                            <p class="text-gray-600">Your KuBaca image was rejected. Please upload a new, clearer image.</p>
                        </div>
                    </div>
                    <form action="/upload-kubaca" method="post" enctype="multipart/form-data" class="space-y-6">
                        <?= Csrf::field() ?>
                        <div>
                            <label for="kubaca_img" class="block text-sm font-semibold text-gray-700 mb-2">
                                New KuBaca Image
                            </label>
                            <input type="file" id="kubaca_img" name="kubaca_img" accept="image/png,image/jpeg,image/webp" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                            <p class="mt-2 text-sm text-gray-500">Format: PNG, JPEG, atau WebP (Max 5MB)</p>
                        </div>
                        <button type="submit"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                Re-upload KuBaca
                            </span>
                        </button>
                    </form>
                </div>
            <?php elseif ($user->status === 'pending kubaca' && !$user->kubaca_img): ?>
                <!-- Initial KuBaca Upload Card -->
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                    <div class="flex items-start mb-6">
                        <svg class="w-6 h-6 text-blue-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Upload KuBaca PNJ</h3>
                            <p class="text-gray-600">Silakan upload foto KuBaca Anda untuk menyelesaikan verifikasi</p>
                        </div>
                    </div>
                    <form action="/upload-kubaca" method="post" enctype="multipart/form-data" class="space-y-6">
                        <?= Csrf::field() ?>
                        <div>
                            <label for="kubaca_img" class="block text-sm font-semibold text-gray-700 mb-2">
                                KuBaca Image
                            </label>
                            <input type="file" id="kubaca_img" name="kubaca_img" accept="image/png,image/jpeg,image/webp" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                            <p class="mt-2 text-sm text-gray-500">Format: PNG, JPEG, atau WebP (Max 5MB)</p>
                        </div>
                        <button type="submit"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                Upload KuBaca
                            </span>
                        </button>
                    </form>
                </div>
            <?php elseif ($user->kubaca_img && $user->status === 'pending kubaca'): ?>
                <!-- Waiting for admin verification -->
                <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-orange-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="text-orange-800 font-medium">KuBaca image uploaded. Waiting for admin verification.</p>
                    </div>
                </div>
            <?php elseif ($user->status === 'active'): ?>
                <!-- Active/Verified state -->
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="text-green-800 font-medium">Your account is fully verified!</p>
                    </div>
                </div>
            <?php endif; ?>
        <?php elseif ($user->isDosen() && $user->status === 'active'): ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <p class="text-green-800 font-medium">Your account is fully verified!</p>
                </div>
            </div>
        <?php endif; ?>

        <!-- logout module -->
        <?php if (App::$app->auth->isGuest()): ?>
            <!-- Empty -->
        <?php else: ?>
            <form action="/logout" method="post">
                <?= Csrf::field() ?>
                <button type="submit"
                    class="flex md:hidden items-center w-full text-lg gap-4 justify-center px-8 py-4 rounded-lg hover:bg-rose-700 bg-primary shadow-md text-white transition-colors cursor-pointer focus:outline-none focus:ring-2 focus:bg-rose-600 focus:ring-rose-500 focus:ring-offset-2">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">  
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                </button>
            </form>
        <?php endif; ?>

    <?php endif; ?>
</div>