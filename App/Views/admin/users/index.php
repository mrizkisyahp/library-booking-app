<?php
/** @var array $users */
use App\Core\App;
use App\Core\Csrf;
?>

<!-- Disini za buat styling css sama atur2 margin lah -->

<h2 class="text-lg font-medium">Manage Users</h2>

<?php if ($m = App::$app->session->getFlash('success')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<?php if ($m = App::$app->session->getFlash('error')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<div class="mt-4 relative overflow-x-auto bg-gray-100 rounded-md shadow-md p-4">
  <table class="w-full text-sm text-left text-gray-800 ">
    <thead class="text-sm text-gray-700 uppercase">
      <tr class=" *:px-6 *:py-3 odd:bg-gray-50">
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>NIM/NIP</th>
        <th>Status</th>
        <th>KuBaca</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user): ?>
        <tr class="border-b border-gray-200 dark:border-gray-700 *:px-6 *:py-4 even:bg-gray-50">
          <td><?= htmlspecialchars($user->id) ?></td>
          <td class="font-medium text-gray-900 whitespace-nowrap"><?= htmlspecialchars($user->nama) ?></td>
          <td><?= htmlspecialchars($user->email) ?></td>
          <td><?= htmlspecialchars($user->role) ?></td>
          <td><?= htmlspecialchars($user->nim ?? $user->nip ?? '-') ?></td>
          <td><?= htmlspecialchars($user->status) ?></td>
          <td>
            <?php if ($user->kubaca_img): ?>
              <a href="/uploads/kubaca/<?= htmlspecialchars($user->kubaca_img) ?>" target="_blank">View Image</a>
            <?php else: ?>
              -
            <?php endif; ?>
          </td>
          <td>
            <?php if ($user->kubaca_img && $user->status === 'active'): ?>
              <form action="/admin/users/status" method="post" style="display:inline;">
                <?= Csrf::field() ?>
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user->id) ?>">
                <input type="hidden" name="action" value="verify_kubaca">
                <button type="submit">Verify KuBaca</button>
              </form>
              <form action="/admin/users/status" method="post" style="display:inline;">
                <?= Csrf::field() ?>
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user->id) ?>">
                <input type="hidden" name="action" value="reject_kubaca">
                <button type="submit">Reject KuBaca</button>
              </form>
            <?php endif; ?>

            <?php if ($user->status !== 'suspended'): ?>
              <form action="/admin/users/status" method="post" style="display:inline;">
                <?= Csrf::field() ?>
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user->id) ?>">
                <input type="hidden" name="action" value="suspend">
                <button type="submit">Suspend</button>
              </form>
            <?php endif; ?>

            <?php if ($user->status === 'suspended'): ?>
              <form action="/admin/users/status" method="post" style="display:inline;">
                <?= Csrf::field() ?>
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user->id) ?>">
                <input type="hidden" name="action" value="activate">
                <button type="submit">Activate</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="flex gap-2 mt-4 align-baseline items-center ">
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
    class="size-4">
    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
  </svg>

  <a href="/admin" class=" hover:font-medium hover:underline text-gray-700">
    Back to Admin Dashboard
  </a>
</div