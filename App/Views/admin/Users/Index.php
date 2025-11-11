
<?php
/** @var array $users */
use App\Models\Role;
?>

<div class="max-w-7xl mx-auto">
  <!-- Header -->
  <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-slate-800 mb-2 flex items-center">
          <svg class="w-8 h-8 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
          User Management
        </h1>
        <p class="text-slate-600">Kelola pengguna, verifikasi akun, dan monitoring aktivitas</p>
      </div>
      <div class="flex gap-3">
        <button class="bg-emerald-600 text-white px-6 py-3 rounded-xl hover:bg-emerald-700 transition-all font-semibold shadow-lg hover:shadow-xl flex items-center">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Add User
        </button>
      </div>
    </div>
  </div>

  <!-- Filters & Search -->
  <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-slate-700 mb-2">Search</label>
        <input type="text" placeholder="Cari nama, email, NIM, atau NIP..." class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
      </div>
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-2">Role</label>
        <select class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
          <option value="">Semua Role</option>
          <option value="mahasiswa">Mahasiswa</option>
          <option value="dosen">Dosen</option>
          <option value="admin">Admin</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
        <select class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
          <option value="">Semua Status</option>
          <option value="active">Active</option>
          <option value="pending">Pending</option>
          <option value="suspended">Suspended</option>
        </select>
      </div>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-semibold text-slate-600">Total Users</p>
          <p class="text-3xl font-bold text-slate-800 mt-1"><?= $totalUsers ?></p>
        </div>
        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
          <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-semibold text-slate-600">Active</p>
          <p class="text-3xl font-bold text-slate-800 mt-1">
            <?= $totalActive ?>
          </p>
        </div>
        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
          <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-semibold text-slate-600">Pending</p>
          <p class="text-3xl font-bold text-slate-800 mt-1">
            <?= $totalPending ?>
          </p>
        </div>
        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
          <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-semibold text-slate-600">Suspended</p>
          <p class="text-3xl font-bold text-slate-800 mt-1">
            <?= $totalSuspended ?>
          </p>
        </div>
        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
          <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
          </svg>
        </div>
      </div>
    </div>
  </div>

  <!-- Users Table -->
  <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-slate-50 border-b-2 border-slate-200">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">User</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Role</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">NIM/NIP</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Jurusan</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Contact</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Warnings</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Foto Kubaca</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Status</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
          <?php foreach ($users as $user): ?>
            <tr class="hover:bg-slate-50 transition-colors">
              <!-- User Info -->
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center mr-3">
                    <span class="text-emerald-700 font-semibold text-sm">
                      <?= strtoupper(substr($user['nama'], 0, 2)) ?>
                    </span>
                  </div>
                  <div>
                    <p class="font-semibold text-slate-800"><?= htmlspecialchars(strtoupper($user['nama'])) ?></p>
                    <p class="text-sm text-slate-500"><?= htmlspecialchars(strtoupper($user['email'])) ?></p>
                  </div>
                </div>
              </td>

              <!-- Role -->
              <td class="px-6 py-4">
                <?php
                $roleColors = [
                  '3' => 'bg-blue-100 text-blue-800',
                  '2' => 'bg-purple-100 text-purple-800',
                  '1' => 'bg-red-100 text-red-800'
                ];

                $roleColor = $roleColors[$user['id_role']] ?? 'bg-gray-100 text-gray-800';
                ?>
                <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold <?= $roleColor ?>">
                  <?= htmlspecialchars(strtoupper(Role::getNameById($user['id_role']))) ?>
                </span>
              </td>

              <!-- NIM/NIP -->
              <td class="px-6 py-4">
                <p class="text-sm font-mono text-slate-700">
                  <?= htmlspecialchars($user['nim'] ?? $user['nip']) ?>
                </p>
              </td>

              <!-- Jurusan -->
              <td class="px-6 py-4">
                <p class="text-sm text-slate-700"><?= htmlspecialchars($user['jurusan']) ?></p>
              </td>

              <!-- Contact -->
              <td class="px-6 py-4">
                <p class="text-sm text-slate-700"><?= htmlspecialchars($user['nomor_hp']) ?></p>
              </td>

              <!-- Warnings -->
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <?php if ($user['peringatan'] > 0): ?>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                      <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                      </svg>
                      <?= (int)$user['peringatan'] ?>
                    </span>
                  <?php else: ?>
                    <span class="text-xs text-slate-400">No warnings</span>
                  <?php endif; ?>
                </div>
                <?php if ($user['suspensi_terakhir']): ?>
                  <p class="text-xs text-slate-500 mt-1">Last: <?= htmlspecialchars($user['suspensi_terakhir']) ?></p>
                <?php endif; ?>
              </td>

              <!-- Foto Kubaca -->
              <td class="px-6 py-4">
                <?php if ($user['kubaca_img']): ?>
                  <button class="text-emerald-600 hover:text-emerald-700 text-sm font-semibold flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    View
                  </button>
                <?php else: ?>
                  <span class="text-xs text-slate-400">No photo</span>
                <?php endif; ?>
              </td>

              <!-- Status -->
              <td class="px-6 py-4">
                <?php
                $statusColors = [
                  'active' => 'bg-green-100 text-green-800',
                  'verified' => 'bg-yellow-100 text-yellow-800',
                  'pending' => 'bg-yellow-100 text-yellow-800',
                  'suspended' => 'bg-red-100 text-red-800'
                ];

                $statusDisplay = [
                  'verified' => 'pending',
                  'suspended' => 'suspended',
                  'active' => 'active'
                ];

                $status = $user['status'];
                $statusText = $statusDisplay[$status] ?? strtoupper($status);
                $statusColor = $statusColors[$statusText] ?? 'bg-gray-100 text-gray-800';
                ?>
                <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold <?= $statusColor ?>">
                  <?= htmlspecialchars(strtoupper($statusText)) ?>
                </span>
              </td>

              <!-- Actions -->
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <?php if ($user['status'] === 'verified'): ?>
                    <!-- Approve/Reject for pending users -->
                    <button class="p-2 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg transition-colors" title="Approve">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </button>
                    <button class="p-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors" title="Reject">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </button>
                  <?php endif; ?>
                  
                  <!-- Edit -->
                  <button class="p-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-colors" title="Edit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  
                  <!-- Delete -->
                  <button class="p-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors" title="Delete">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="bg-slate-50 px-6 py-4 border-t border-slate-200">
      <div class="flex items-center justify-between">
        <p class="text-sm text-slate-600">
          Showing <span class="font-semibold"><?= (($currentPage - 1) * $perPage) + 1 ?></span> to <span class="font-semibold"><?= min($currentPage * $perPage, $totalUsers) ?></span> of <span class="font-semibold"><?= $totalUsers ?></span> results
        </p>
        <div class="flex gap-2">
          <?php if ($currentPage > 1): ?>
            <a href="/admin/users?page=<?= $currentPage - 1 ?>" class="px-3 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">
              Previous
            </a>
          <?php endif; ?>
          <?php for ($i = 1; $i <= ($totalUsers / $perPage); $i++): ?>
            <button class="px-3 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors<?= $i === $currentPage ? ' bg-emerald-700' : '' ?>">
              <?= $i ?>
            </button>
          <?php endfor; ?>
          <?php if ($currentPage < ($totalUsers / $perPage)): ?>
            <a href="/admin/users?page=<?= $currentPage + 1 ?>" class="px-3 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">
              Next
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>