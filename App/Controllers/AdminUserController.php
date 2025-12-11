<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\UserService;
use Exception;

class AdminUserController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    public function index(Request $request)
    {
        $filters = [
            'keyword' => $request->query()['keyword'] ?? '',
            'status' => $request->query()['status'] ?? '',
            'role' => $request->query()['role'] ?? '',
        ];
        $page = (int) ($request->query()['page'] ?? 1);

        $paginator = $this->userService->getAllUsers($filters, 15, $page);

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
        return view('Admin/Users/Create', [
            'roles' => $this->userService->getAllRoles(),
        ]);
    }

    public function store(Request $request)
    {
        try {
            $this->userService->createUser($request->all());

            flash('success', 'User berhasil dibuat');
            redirect('/admin/users');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function show(Request $request)
    {
        try {
            $id = (int) $request->query()['id'];
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
        try {
            $id = (int) $request->query()['id_user'];
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
        try {
            $id = (int) $request->all()['id_user'];
            $data = $request->all();
            unset($data['id_user']);

            $this->userService->updateUser($id, $data);

            flash('success', 'User berhasil diupdate');
            redirect('/admin/users');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function delete(Request $request)
    {
        try {
            $id = (int) $request->all()['id_user'];
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
            $id = (int) $request->all()['id_user'];
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
            $id = (int) $request->all()['id_user'];
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
            $id = (int) $request->all()['id_user'];
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
            $id = (int) $request->all()['id_user'];
            $masa_aktif = $request->all()['masa_aktif'];
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
            $id = (int) $request->all()['id_user'];
            $reason = $request->all()['reason'] ?? '';

            $this->userService->rejectKubaca($id, $reason);

            flash('success', 'KuBaca ditolak');
            back();
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }
}
