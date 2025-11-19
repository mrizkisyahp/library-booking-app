<?php
use App\Core\App;
use App\Core\Csrf;
use App\Models\User;
/** @var User[] $users */

$filters = $filters ?? [];
$stats = $stats ?? [];
$roles = $roles ?? [];
$statuses = $statuses ?? [];
?>
<div class="p-6">
  <div class="mb-8 flex flex-col md:flex-row justify-between items-center">
    <div>
      <h2 class="text-3xl font-bold text-gray-900 mb-2">Manajemen User</h2>
      <p class="text-gray-600">Kelola dan monitor User yang terdaftar</p>
    </div>
    <p>
      <a href="/admin/users/create"
        class="flex gap-4 bg-primary shadow text-white mt-4 md:mt-0 px-8 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
          class="lucide lucide-plus-icon lucide-plus">
          <path d="M5 12h14" />
          <path d="M12 5v14" />
        </svg>
        Tambah User
      </a>
    </p>
  </div>

  <?php if ($message = App::$app->session->getFlash('success')): ?>
    <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <?php if ($message = App::$app->session->getFlash('error')): ?>
    <div class="mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <section class="bg-white shadow rounded-lg p-6 mb-8 border border-gray-100">
    <h2 class="text-lg font-semibold flex items-center gap-2 mb-4">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
        class="lucide lucide-sliders-horizontal-icon lucide-sliders-horizontal">
        <path d="M10 5H3" />
        <path d="M12 19H3" />
        <path d="M14 3v4" />
        <path d="M16 17v4" />
        <path d="M21 12h-9" />
        <path d="M21 19h-5" />
        <path d="M21 5h-7" />
        <path d="M8 10v4" />
        <path d="M8 12H3" />
      </svg>
      Pencarian & Filter
    </h2>
    <hr class="h-1 mx-auto py-2 w-full text-gray-400">
    <form method="get" action="/admin/users"
      class="flex flex-col justify-between items-start md:items-center gap-6 px-4 md:px-8">
      <div class="md:items-center gap-4 md:gap-8">
        <div class="flex flex-col md:flex-row gap-4 md:gap-8">
          <!-- Keyword -->
          <label class="flex flex-col md:flex-row items-center capitalize">
            <div class="items-center gap-4 hidden md:flex w-full">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-search-icon lucide-search size-5">
                <path d="m21 21-4.34-4.34" />
                <circle cx="11" cy="11" r="8" />
              </svg>
              <span class="text-sm">Kata kunci:</span>
            </div>
            <input type="text" name="keyword"
              class="w-full flex grow border border-gray-300 rounded-lg px-4 py-1 text-sm accent-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all"
              value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>" placeholder="Kata Kunci">
          </label>

          <!-- Room Type -->
          <label class="flex items-center gap-4">
            <div class="hidden md:flex">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-building-icon size-5">
                <rect width="16" height="20" x="4" y="2" rx="2" ry="2" />
                <path d="M9 22v-4h6v4" />
                <path d="M8 6h.01" />
                <path d="M16 6h.01" />
                <path d="M12 6h.01" />
                <path d="M12 10h.01" />
                <path d="M12 14h.01" />
                <path d="M16 10h.01" />
                <path d="M16 14h.01" />
                <path d="M8 10h.01" />
                <path d="M8 14h.01" />
              </svg>
              <span>
                Jenis Role:
              </span>
            </div>
            <div class="relative">
              <button id="dropdownButton" type="button"
                class="flex items-center  justify-center gap-4 bg-primary text-white border border-transparent shadow font-medium leading-5 px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition">
                <span class="capitalize">
                  <?= ($filters['nama_role'] ?? '') !== '' ? htmlspecialchars($filters['nama_role']) : 'Semua Role' ?>
                </span>

                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor"
                  stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="lucide lucide-chevron-down mt-0.5">
                  <path d="m6 9 6 6 6-6" />
                </svg>
              </button>

              <div id="dropdown"
                class="absolute mt-2 origin-top transition-all duration-150 ease-out scale-95 opacity-0 z-10 hidden bg-gray-100 border border-gray-200 rounded shadow">

                <ul class="p-2 text-sm font-medium space-y-1" aria-labelledby="dropdownButton">

                  <li class="px-4 py-2 rounded cursor-pointer hover:bg-gray-200 transition
                    <?php if (($filters['nama_role'] ?? '') === ''): ?> bg-gray-300 <?php endif; ?>" data-value="">
                    Semua Role
                  </li>

                  <?php foreach ($roles as $role): ?>
                    <?php $value = (string) ($role['nama_role'] ?? ''); ?>
                    <li class="px-4 py-2 rounded cursor-pointer hover:bg-gray-200 transition
                      <?php if (($filters['nama_role'] ?? '') === $value): ?> bg-gray-300 <?php endif; ?>"
                      data-value="<?= htmlspecialchars($value) ?>">
                      <?= htmlspecialchars($value) ?>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
              <input type="hidden" id="statusSelected" name="nama_role" value="<?= $filters['nama_role'] ?? '' ?>">
            </div>
          </label>


          <!-- Status -->
          <label class="flex items-center gap-4">
            <div class="hidden md:flex">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-chart-column-decreasing-icon lucide-chart-column-decreasing size-5">
                <path d="M13 17V9" />
                <path d="M18 17v-3" />
                <path d="M3 3v16a2 2 0 0 0 2 2h16" />
                <path d="M8 17V5" />
              </svg>
              <span>
                Status:
              </span>
            </div>

            <div class="relative">
              <button id="dropdownButton2" type="button" data-dropdown-toggle="dropdown"
                class="flex items-center justify-center gap-4 bg-primary text-white box-border border border-transparent shadow font-medium leading-5 px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition">
                <span
                  class="capitalize"><?= htmlspecialchars(($filters['status'] ?? '') !== '' ? ucwords(str_replace('_', ' ', $filters['status'])) : 'Semua Status') ?></span>
                <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" width="24" height="24" viewBox="0 0 24 24"
                  fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="lucide lucide-chevron-down-icon lucide-chevron-down mt-0.5">
                  <path d="m6 9 6 6 6-6" />
                </svg>
              </button>
              <div id="dropdown2"
                class="absolute mt-2 origin-top transition-all duration-150 ease-out scale-95 opacity-0 z-10 hidden bg-gray-100 border border-gray-200 rounded shadow">
                <ul class="p-2 text-sm font-medium space-y-1" aria-labelledby="dropdownButton">
                  <li class="px-4 py-2 rounded cursor-pointer hover:bg-gray-200 transition
                  <?php if (($filters['status'] ?? '') === ''): ?> bg-gray-300 <?php endif; ?>" data-value="">
                    Semua
                  </li>
                  <?php foreach ($statuses as $status): ?>
                    <li class="px-4 py-2 rounded cursor-pointer hover:bg-gray-200 transition
                    <?php if (($filters['status'] ?? '') === $status): ?> bg-gray-300 <?php endif; ?>"
                      data-value="<?= htmlspecialchars($status) ?>">
                      <?= htmlspecialchars(ucwords(str_replace('_', ' ', $status))) ?>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
              <input type="hidden" id="statusSelected2" name="status" value="<?= $filters['status'] ?? '' ?>">
            </div>
          </label>
        </div>
      </div>
      <div class="flex gap-4">

        <div class="flex items-center gap-8 text-sm">
          <button type="submit"
            class="cursor-pointer px-4 py-2 border-2 border-emerald-300 rounded-lg font-medium text-emerald-700 hover:bg-emerald-50 transition-colors">Terapkan</button>
          <a href="/admin/users"
            class="cursor-pointer px-4 py-2 border-2 border-rose-300 rounded-lg font-medium text-rose-700 hover:bg-rose-50 transition-colors">Bersihkan</a>
        </div>

      </div>
    </form>
  </section>

  <section class="mb-12">
    <h2 class="text-2xl font-semibold">Summary</h2>
    <div class="mt-4 flex justify-between items-center px-8 gap-4 md:gap-8">
      <p class="p-6 bg-gray-200 border-l-2 border-gray-400 rounded-xl shadow class flex flex-col grow">
        Total Users:
        <span class="text-4xl font-bold">
          <?= htmlspecialchars((string) ($stats['total'] ?? count($users))) ?>
        </span>
      </p>
      <p class="p-6 bg-gray-200 border-l-2 border-gray-400 rounded-xl shadow class flex flex-col grow">
        Active:
        <span class="text-4xl font-bold">
          <?= htmlspecialchars((string) ($stats['active'] ?? 0)) ?>
        </span>
      </p>
      <p class="p-6 bg-gray-200 border-l-2 border-gray-400 rounded-xl shadow class flex flex-col grow">
        Pending KuBaca:
        <span class="text-4xl font-bold">
          <?= htmlspecialchars((string) ($stats['pending'] ?? 0)) ?>
        </span>
      </p>
      <p class="p-6 bg-gray-200 border-l-2 border-gray-400 rounded-xl shadow class flex flex-col grow">
        Suspended:
        <span class="text-4xl font-bold">
          <?= htmlspecialchars((string) ($stats['suspended'] ?? 0)) ?>
        </span>
      </p>
    </div>
  </section>

  <section class="mt-6">
    <?php if (empty($users)): ?>
      <!-- Empty State -->
      <div class="bg-white rounded-2xl shadow-lg p-12 text-center border border-gray-100">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak Ada User</h3>
        <p class="text-gray-600">Belum ada user yang terdaftar dalam sistem.</p>
      </div>
    <?php else: ?>
      <div class="bg-white rounded-md shadow-md overflow-x-auto border border-gray-200">
        <table class="w-full text-sm text-left">
          <thead class="bg-linear-to-r bg-primary">
            <tr
              class=" *:px-6 *:py-3  *:text-left *:text-regular *:font-semibold *:text-gray-50 *:capitalize *:tracking-wider *:whitespace-nowrap">
              <th scope="col">ID</th>
              <th scope="col">Name & Email</th>
              <th scope="col">Role</th>
              <th scope="col">NIM / NIP</th>
              <th scope="col">Jurusan</th>
              <th scope="col">Peringatan</th>
              <th scope="col">KuBaca</th>
              <th scope="col">Status</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php foreach ($users as $user): ?>
              <tr class="hover:bg-gray-200 odd:bg-gray-50 even:bg-gray-100 transition-colors border-b border-gray-200">
                <th scope="row" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  #<?= htmlspecialchars((string) $user->id_user) ?></th>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                  <strong><?= htmlspecialchars($user->nama) ?></strong><br>
                  <small><?= htmlspecialchars($user->email) ?></small><br>
                  <small>HP: <?= htmlspecialchars($user->nomor_hp ?? '-') ?></small>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                  <?= htmlspecialchars($user->nama_role ?? 'Unknown') ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                  <?php if ($user->nim): ?>
                    NIM: <?= htmlspecialchars($user->nim) ?><br>
                  <?php endif; ?>
                  <?php if ($user->nip): ?>
                    NIP: <?= htmlspecialchars($user->nip) ?><br>
                  <?php endif; ?>
                  <?php if (!$user->nim && !$user->nip): ?>
                    -
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($user->jurusan ?? '-') ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                  <?= htmlspecialchars((string) ($user->peringatan ?? 0)) ?></td>
                <!-- Foto Kubaca -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                  <?php if ($user->kubaca_img): ?>
                    <button
                      class="view-button text-emerald-600 hover:text-emerald-700 text-sm font-semibold flex items-center"
                      data-img="uploads/kubaca/<?= htmlspecialchars($user->kubaca_img) ?>"
                      data-nim="<?= htmlspecialchars($user->nim ?? $user->nip) ?>"
                      data-nama="<?= htmlspecialchars($user->nama) ?>">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      View
                    </button>
                  <?php else: ?>
                    <span class="text-xs text-slate-400">Missing</span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($user->status) ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                  <!-- <div>
                  <a href="/admin/users/show?id=<?= $user->id_user ?>">View</a> |
                  <a href="/admin/users/edit?id=<?= $user->id_user ?>">Edit</a>
                </div>
                <form method="post" action="/admin/users/delete">
                  <?= Csrf::field() ?>
                  <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
                  <button type="submit" onclick="return confirm('Delete this user?')">Delete</button>
                </form> -->
                  <?php if ($user->status !== 'suspended'): ?>
                    <form method="post" action="/admin/users/suspend">
                      <?= Csrf::field() ?>
                      <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
                      <button type="submit">Suspend</button>
                    </form>
                  <?php else: ?>
                    <form method="post" action="/admin/users/unsuspend">
                      <?= Csrf::field() ?>
                      <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
                      <button type="submit">Unsuspend</button>
                    </form>
                  <?php endif; ?>
                  <!-- <form method="post" action="/admin/users/reset-password">
                  <?= Csrf::field() ?>
                  <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
                  <button type="submit">Reset Password</button>
                </form> -->
                  <!-- <form method="post" action="/admin/users/approve-kubaca">
                  <?= Csrf::field() ?>
                  <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
                  <button type="submit">Approve KuBaca</button>
                </form> -->
                  <!-- <form method="post" action="/admin/users/reject-kubaca">
                  <?= Csrf::field() ?>
                  <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
                  <label>
                    Reason (optional)
                    <input type="text" name="reason" value="">
                  </label>
                  <button type="submit">Reject KuBaca</button>
                </form> -->
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </section>

  <!-- Pagination -->
  <div class="bg-slate-50 px-6 py-4 border-t border-slate-200">
    <div class="flex items-center justify-between">
      <p class="text-sm text-slate-600">
        Showing <span class="font-semibold"><?= (($currentPage - 1) * $perPage) + 1 ?></span>
        to <span class="font-semibold"><?= min($currentPage * $perPage, $totalUsers) ?></span>
        of <span class="font-semibold"><?= $totalUsers ?></span> results
      </p>

      <div class="flex gap-2">
        <?php if ($currentPage > 1): ?>
          <a href="/admin/users?page=<?= $currentPage - 1 ?>"
            class="px-3 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">
            Previous
          </a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= ceil($totalUsers / $perPage); $i++): ?>
          <a href="/admin/users?page=<?= $i ?>"
            class="px-3 py-2 rounded-lg text-sm font-medium transition-colors
                  <?= $i === $currentPage ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'border border-slate-300 text-slate-700 hover:bg-slate-100' ?>">
            <?= $i ?>
          </a>
        <?php endfor; ?>

        <?php if ($currentPage < ceil($totalUsers / $perPage)): ?>
          <a href="/admin/users?page=<?= $currentPage + 1 ?>"
            class="px-3 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">
            Next
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div id="imagePopUp" class="hidden fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-md z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-sm w-full p-4 transition-all scale-95 opacity-0 duration-300">

      <!-- HELP OVER HERE -->

      <img id="popUpImage" src="<?= !empty($user->kubaca_img) ? $user->kubaca_img : '' ?> " alt="Pop-up Image"
        class="w-full h-64 object-cover rounded-md mb-4">
      <div class="flex items-center justify-start gap-4">
        <p><?php if ($user->nim ?? $user->nip): ?>
            NIM:
          <?php else: ?>
            NIP:
          <?php endif; ?>
        </p>
        <p id="popUpId" class="text-sm font-semibold text-gray-600"><?= htmlspecialchars($user->nim ?? $user->nip) ?>
        </p>
      </div>
      <div class="flex items-center justify-start gap-4">
        <p>
          Nama:
        </p>
        <p id="popUpNama" class="text-sm font-semibold text-gray-600 capitalize"><?= htmlspecialchars($user->nama) ?>
        </p>
      </div>
      <button id="closePopUp"
        class="mt-4 bg-emerald-600 text-white px-4 py-2 rounded-md hover:bg-emerald-800 w-full transition-all">
        Tutup
      </button>
    </div>
  </div>
</div>