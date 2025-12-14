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

        $paginator = $this->userService->getAllUsers($filters, self::PER_PAGE, $page);

        return view('Admin/Users/Index', [
            'users' => $paginator->items,
            'paginator' => $paginator,
            'filters' => $filters,
            'stats' => $this->userService->getStats(),
            'roles' => $this->userService->getAllRoles(),
            'statuses' => $this->userService->getAllStatuses(),
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
}
