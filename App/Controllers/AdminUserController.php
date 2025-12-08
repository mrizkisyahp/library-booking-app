<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Services\UserServices;
use Exception;

class AdminUserController extends Controller
{
    public function __construct(private UserServices $userServices)
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

        $paginator = $this->userServices->getAllUsers($filters, 15, $page);

        return view('Admin/Users/Index', [
            'users' => $paginator->items,
            'paginator' => $paginator,
            'filters' => $filters,
            'stats' => $this->userServices->getStats(),
            'roles' => $this->userServices->getAllRoles(),
            'statuses' => $this->userServices->getAllStatuses(),
        ]);
    }

    public function create(Request $request)
    {
        return view('Admin/Users/Create', [
            'roles' => $this->userServices->getAllRoles(),
        ]);
    }

    public function store(Request $request)
    {
        try {
            $this->userServices->createUser($request->all());

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
            $user = $this->userServices->getUserById($id);

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
            $id = (int) $request->query()['id'];
            $user = $this->userServices->getUserById($id);

            return view('Admin/Users/Edit', [
                'user' => $user,
                'roles' => $this->userServices->getAllRoles(),
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

            $this->userServices->updateUser($id, $data);

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
            $this->userServices->deleteUser($id);

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
            $this->userServices->suspendUser($id);

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
            $this->userServices->unsuspendUser($id);

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
            $newPassword = $this->userServices->resetPassword($id);

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
            $this->userServices->approveKubaca($id);

            flash('success', 'KuBaca berhasil disetujui, user sekarang aktif');
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

            $this->userServices->rejectKubaca($id, $reason);

            flash('success', 'KuBaca ditolak');
            back();
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }
}
