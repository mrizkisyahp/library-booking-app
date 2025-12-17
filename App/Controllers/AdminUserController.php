<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Exceptions\ValidationException;
use App\Core\Request;
use App\Services\UserService;
use Exception;

class AdminUserController extends Controller
{
    private const PER_PAGE = 15;

    public function __construct(private UserService $userService)
    {
    }

    public function index(Request $request)
    {
        $this->setLayout('main');
        $this->setTitle('Kelola User | Library Booking App');

        $filters = [
            'keyword' => $request->query()['keyword'] ?? '',
            'status' => $request->query()['status'] ?? '',
            'role' => $request->query()['role'] ?? '',
        ];
        $page = (int) ($request->query()['page'] ?? 1);
        $view = $request->query()['view'] ?? 'pending'; // 'pending' or 'all' - default is pending

        // Determine which method to call based on view type
        if ($view === 'pending') {
            $paginator = $this->userService->getPendingKubacaWithImage($filters, self::PER_PAGE, $page);
        } else {
            $paginator = $this->userService->getAllUsers($filters, self::PER_PAGE, $page);
        }

        // Get pending count for tab badge
        $pendingKubacaCount = $this->userService->getPendingKubacaWithImageCount();

        return view('Admin/Users/Index', [
            'users' => $paginator->items,
            'paginator' => $paginator,
            'filters' => $filters,
            'stats' => $this->userService->getStats(),
            'roles' => $this->userService->getAllRoles(),
            'statuses' => $this->userService->getAllStatuses(),
            'activeView' => $view,
            'pendingKubacaCount' => $pendingKubacaCount,
        ]);
    }

    public function create(Request $request)
    {
        $this->setLayout('main');
        $this->setTitle('Tambah User | Library Booking App');

        return view('Admin/Users/Create', [
            'roles' => $this->userService->getAllRoles(),
        ]);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'nama' => 'required|string|max:100',
                'email' => 'required|email',
                'id_role' => 'required|integer',
            ]);

            $this->userService->createUser($request->all());

            flash('success', 'User berhasil dibuat');
            redirect('/admin/users');
        } catch (ValidationException $e) {
            return view('Admin/Users/Create', [
                'roles' => $this->userService->getAllRoles(),
                'validator' => $e->getValidator(),
            ]);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function show(Request $request)
    {
        $this->setLayout('main');
        $this->setTitle('Detail User | Library Booking App');

        try {
            $data = $request->validate([
                'id' => 'required|integer',
            ]);

            $id = (int) $data['id'];
            $user = $this->userService->getUserById($id);

            return view('Admin/Users/Show', [
                'user' => $user,
            ]);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            redirect('/admin/users');
        }
    }

    public function edit(Request $request)
    {
        $this->setLayout('main');
        $this->setTitle('Edit User | Library Booking App');

        try {
            $data = $request->validate([
                'id_user' => 'required|integer',
            ]);

            $id = (int) $data['id_user'];
            $user = $this->userService->getUserById($id);

            return view('Admin/Users/Edit', [
                'user' => $user,
                'roles' => $this->userService->getAllRoles(),
            ]);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            redirect('/admin/users');
        }
    }

    public function update(Request $request)
    {
        $id = null;
        try {
            $data = $request->validate([
                'id_user' => 'required|integer',
            ]);

            $id = (int) $data['id_user'];
            $updateData = $request->all();
            unset($updateData['id_user']);

            $this->userService->updateUser($id, $updateData);

            flash('success', 'User berhasil diupdate');
            redirect('/admin/users');
        } catch (ValidationException $e) {
            $user = $id ? $this->userService->getUserById($id) : null;
            return view('Admin/Users/Edit', [
                'user' => $user,
                'roles' => $this->userService->getAllRoles(),
                'statuses' => $this->userService->getAllStatuses(),
                'validator' => $e->getValidator(),
            ]);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function delete(Request $request)
    {
        try {
            $data = $request->validate([
                'id_user' => 'required|integer',
            ]);

            $id = (int) $data['id_user'];
            $this->userService->deleteUser($id);

            flash('success', 'User berhasil dihapus');
            redirect('/admin/users');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function suspend(Request $request)
    {
        try {
            $data = $request->validate([
                'id_user' => 'required|integer',
            ]);

            $id = (int) $data['id_user'];
            $this->userService->suspendUser($id);

            flash('success', 'User berhasil disuspend');
            back();
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function unsuspend(Request $request)
    {
        try {
            $data = $request->validate([
                'id_user' => 'required|integer',
            ]);

            $id = (int) $data['id_user'];
            $this->userService->unsuspendUser($id);

            flash('success', 'User berhasil diaktifkan kembali');
            back();
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $data = $request->validate([
                'id_user' => 'required|integer',
            ]);

            $id = (int) $data['id_user'];
            $newPassword = $this->userService->resetPassword($id);

            flash('success', 'Password berhasil direset. Password baru: ' . $newPassword);
            back();
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function approveKubaca(Request $request)
    {
        try {
            $data = $request->validate([
                'id_user' => 'required|integer',
                'masa_aktif' => 'required|date',
            ]);

            $id = (int) $data['id_user'];
            $masa_aktif = $data['masa_aktif'];
            $this->userService->approveKubaca($id, $masa_aktif);

            flash('success', "KuBaca berhasil disetujui, user sekarang aktif sampai {$masa_aktif}");
            back();
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function rejectKubaca(Request $request)
    {
        try {
            $data = $request->validate([
                'id_user' => 'required|integer',
                'reason' => 'nullable|string|max:500',
            ]);

            $id = (int) $data['id_user'];
            $reason = $data['reason'] ?? '';

            $this->userService->rejectKubaca($id, $reason);

            flash('success', 'KuBaca ditolak');
            back();
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function warnings(Request $request)
    {
        $this->setLayout('main');
        $this->setTitle('Peringatan Pengguna | Library Booking App');

        $page = (int) ($request->query()['page'] ?? 1);
        $paginator = $this->userService->getAllWarningsPaginated(15, $page);
        $warningTypes = $this->userService->getWarningTypes();

        return view('Admin/Users/Warnings', [
            'warnings' => $paginator->items,
            'paginator' => $paginator,
            'warningTypes' => $warningTypes,
        ]);
    }

    public function suspensions(Request $request)
    {
        $this->setLayout('main');
        $this->setTitle('Suspensi Pengguna | Library Booking App');

        $page = (int) ($request->query()['page'] ?? 1);
        $paginator = $this->userService->getAllSuspensionsPaginated(15, $page);

        return view('Admin/Users/Suspensions', [
            'suspensions' => $paginator->items,
            'paginator' => $paginator,
        ]);
    }

    public function addWarning(Request $request)
    {
        try {
            $data = $request->validate([
                'user_id' => 'required|integer',
                'peringatan_id' => 'required|integer',
                'reason' => 'nullable|string|max:500',
            ]);

            $this->userService->addWarning(
                (int) $data['user_id'],
                (int) $data['peringatan_id'],
                $data['reason'] ?? null
            );

            flash('success', 'Peringatan berhasil ditambahkan');
            back();
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function removeWarning(Request $request)
    {
        try {
            $data = $request->validate([
                'warning_id' => 'required|integer',
            ]);

            $this->userService->removeWarning((int) $data['warning_id']);

            flash('success', 'Peringatan berhasil dihapus');
            back();
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    // ==================== WARNING TYPES CRUD ====================

    public function warningTypes(Request $request)
    {
        $this->setLayout('main');
        $this->setTitle('Jenis Peringatan | Library Booking App');

        $page = (int) ($request->query()['page'] ?? 1);
        $paginator = $this->userService->getWarningTypesPaginated(15, $page);

        return view('Admin/Users/WarningTypes', [
            'warningTypes' => $paginator->items,
            'paginator' => $paginator,
        ]);
    }

    public function storeWarningType(Request $request)
    {
        try {
            $data = $request->validate([
                'nama_peringatan' => 'required|string|max:100',
            ]);

            $this->userService->createWarningType($data['nama_peringatan']);

            flash('success', 'Jenis peringatan berhasil ditambahkan');
            redirect('/admin/users/warning-types');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function updateWarningType(Request $request)
    {
        try {
            $data = $request->validate([
                'id' => 'required|integer',
                'nama_peringatan' => 'required|string|max:100',
            ]);

            $this->userService->updateWarningType((int) $data['id'], $data['nama_peringatan']);

            flash('success', 'Jenis peringatan berhasil diupdate');
            redirect('/admin/users/warning-types');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function deleteWarningType(Request $request)
    {
        try {
            $data = $request->validate([
                'id' => 'required|integer',
            ]);

            $this->userService->deleteWarningType((int) $data['id']);

            flash('success', 'Jenis peringatan berhasil dihapus');
            redirect('/admin/users/warning-types');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }
}
