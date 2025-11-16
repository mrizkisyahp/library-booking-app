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

  <h1>Admin User Management</h1>

  <?php if ($message = App::$app->session->getFlash('success')): ?>
    <p style="color: green;"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <?php if ($message = App::$app->session->getFlash('error')): ?>
    <p style="color: red;"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <section>
    <h2>Search and Filters</h2>
    <form method="get" action="/admin/users">
      <fieldset>
        <legend>Filter Users</legend>
        <label>
          Keyword
          <input type="text" name="keyword" value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>">
        </label>
        <label>
          Role
          <select name="role">
            <option value="">All Roles</option>
            <?php foreach ($roles as $role): ?>
              <?php $value = (string)($role['id_role'] ?? ''); ?>
              <option value="<?= htmlspecialchars($value) ?>" <?= ($filters['role'] ?? '') === $value ? 'selected' : '' ?>>
                <?= htmlspecialchars($role['nama_role'] ?? 'Role') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>
          Status
          <select name="status">
            <option value="">All Statuses</option>
            <?php foreach ($statuses as $status): ?>
              <option value="<?= htmlspecialchars($status) ?>" <?= ($filters['status'] ?? '') === $status ? 'selected' : '' ?>>
                <?= htmlspecialchars(ucwords($status)) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </label>
        <button type="submit">Apply</button>
        <a href="/admin/users">Reset</a>
      </fieldset>
    </form>
  </section>

  <section>
    <h2>Summary</h2>
    <p>Total Users: <?= htmlspecialchars((string)($stats['total'] ?? count($users))) ?></p>
    <p>Active: <?= htmlspecialchars((string)($stats['active'] ?? 0)) ?></p>
    <p>Pending KuBaca: <?= htmlspecialchars((string)($stats['pending'] ?? 0)) ?></p>
    <p>Suspended: <?= htmlspecialchars((string)($stats['suspended'] ?? 0)) ?></p>
    <p><a href="/admin/users/create">Create New User</a></p>
  </section>

  <section>
    <h2>Users</h2>
    <?php if (empty($users)): ?>
      <p>No users found.</p>
    <?php else: ?>
      <table border="1" cellpadding="6" cellspacing="0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name & Email</th>
            <th>Role</th>
            <th>NIM / NIP</th>
            <th>Jurusan</th>
            <th>Peringatan</th>
            <th>KuBaca</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?= htmlspecialchars((string)$user->id_user) ?></td>
              <td>
                <strong><?= htmlspecialchars($user->nama) ?></strong><br>
                <small><?= htmlspecialchars($user->email) ?></small><br>
                <small>HP: <?= htmlspecialchars($user->nomor_hp ?? '-') ?></small>
              </td>
              <td><?= htmlspecialchars($user->nama_role ?? 'Unknown') ?></td>
              <td>
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
              <td><?= htmlspecialchars($user->jurusan ?? '-') ?></td>
              <td><?= htmlspecialchars((string)($user->peringatan ?? 0)) ?></td>
              <!-- Foto Kubaca -->
              <td class="px-6 py-4">
                <?php if ($user->kubaca_img): ?>
                  <button class="view-button text-emerald-600 hover:text-emerald-700 text-sm font-semibold flex items-center"
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
              <td><?= htmlspecialchars($user->status) ?></td>
              <td>
                <div>
                  <a href="/admin/users/show?id=<?= $user->id_user ?>">View</a> |
                  <a href="/admin/users/edit?id=<?= $user->id_user ?>">Edit</a>
                </div>
                <form method="post" action="/admin/users/delete">
                  <?= Csrf::field() ?>
                  <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
                  <button type="submit" onclick="return confirm('Delete this user?')">Delete</button>
                </form>
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
                <form method="post" action="/admin/users/reset-password">
                  <?= Csrf::field() ?>
                  <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
                  <button type="submit">Reset Password</button>
                </form>
                <form method="post" action="/admin/users/approve-kubaca">
                  <?= Csrf::field() ?>
                  <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
                  <button type="submit">Approve KuBaca</button>
                </form>
                <form method="post" action="/admin/users/reject-kubaca">
                  <?= Csrf::field() ?>
                  <input type="hidden" name="id_user" value="<?= $user->id_user ?>">
                  <label>
                    Reason (optional)
                    <input type="text" name="reason" value="">
                  </label>
                  <button type="submit">Reject KuBaca</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
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

        <img id="popUpImage" src="<?= !empty($user->kubaca_img) ? $user->kubaca_img : '' ?> " alt="Pop-up Image" class="w-full h-64 object-cover rounded-md mb-4">
        <div class="flex items-center justify-start gap-4">
          <p><?php if ($user->nim ?? $user->nip): ?>
              NIM:
            <?php else: ?>
              NIP:
            <?php endif; ?>
          </p>
          <p id="popUpId" class="text-sm font-semibold text-gray-600"><?= htmlspecialchars($user->nim ?? $user->nip) ?></p>
        </div>
        <div class="flex items-center justify-start gap-4">
          <p>
            Nama: 
          </p>
          <p id="popUpNama" class="text-sm font-semibold text-gray-600 capitalize"><?= htmlspecialchars($user->nama) ?></p>
        </div>
        <button id="closePopUp" class="mt-4 bg-emerald-600 text-white px-4 py-2 rounded-md hover:bg-emerald-800 w-full transition-all">
          Tutup
        </button>
      </div>
    </div>
