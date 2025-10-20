<?php
/** @var array $users */
use App\Core\App;
use App\Core\Csrf;
?>

<!-- Disini za buat styling css sama atur2 margin lah -->

<h2>Manage Users</h2>

<?php if ($m = App::$app->session->getFlash('success')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<?php if ($m = App::$app->session->getFlash('error')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<table border="1">
  <thead>
    <tr>
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
      <tr>
        <td><?= htmlspecialchars($user->id) ?></td>
        <td><?= htmlspecialchars($user->nama) ?></td>
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

<p><a href="/admin">Back to Admin Dashboard</a></p>
