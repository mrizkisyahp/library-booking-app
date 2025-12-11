<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\RoomService;
use App\Core\Exceptions\ValidationException;

class AdminRoomController extends Controller
{
    public function __construct(private RoomService $roomService)
    {
    }

    public function index(Request $request)
    {
        $page = (int) ($request->input('page') ?? 1);
        $keyword = $request->input('keyword') ?? '';
        $jenis = $request->input('jenis_ruangan') ?? '';
        $status = $request->input('status_ruangan') ?? '';
        $kapasitasmin = $request->input('kapasitas_min') ?? '';
        $kapasitasmax = $request->input('kapasitas_max') ?? '';

        $filters = [
            'keyword' => $keyword,
            'kapasitas_min' => $kapasitasmin,
            'kapasitas_max' => $kapasitasmax,
            'jenis_ruangan' => $jenis,
            'status_ruangan' => $status,
        ];

        $paginatedRooms = $this->roomService->getAllRooms($filters, 15, $page);

        return view('Admin/Rooms/Index', [
            'rooms' => $paginatedRooms->items,
            'pagination' => $paginatedRooms,
            'filters' => $filters,
        ]);
    }

    public function create(Request $request)
    {
        return view('Admin/Rooms/Create');
    }

    public function store(Request $request)
    {
        if (!$request->isPost()) {
            redirect('/admin/rooms');
        }

        try {
            $validated = $request->validate([
                'nama_ruangan' => ['required', 'string', 'max:100'],
                'jenis_ruangan' => ['required', 'string'],
                'kapasitas_min' => ['required', 'numeric', 'min:1'],
                'kapasitas_max' => ['required', 'numeric', 'min:1'],
                'deskripsi_ruangan' => ['required', 'string'],
                'status_ruangan' => ['required', 'string', 'in:available,unavailable,adminOnly'],
            ]);

            $this->roomService->createRoom($validated);

            flash('success', 'Ruangan berhasil ditambahkan!');
            redirect('/admin/rooms');
        } catch (ValidationException $e) {
            return view('Admin/Rooms/Create', [
                'validator' => $e->getValidator()
            ]);
        }
    }

    public function edit(Request $request)
    {
        $id = (int) $request->query('id');
        $room = $this->roomService->getRoomById($id);

        if (!$room) {
            flash('error', 'Ruangan tidak ditemukan');
            redirect('/admin/rooms');
        }

        return view('Admin/Rooms/Edit', [
            'room' => $room,
        ]);
    }

    public function show(Request $request)
    {
        $id = (int) $request->query('id');
        $room = $this->roomService->getRoomById($id);

        if (!$room) {
            flash('error', 'Ruangan tidak ditemukan');
            redirect('/admin/rooms');
        }

        return view('Admin/Rooms/Show', [
            'room' => $room,
        ]);
    }

    public function update(Request $request)
    {
        if (!$request->isPost()) {
            redirect('/admin/rooms');
        }

        $id = (int) $request->input('id_ruangan');

        try {
            $validated = $request->validate([
                'nama_ruangan' => ['required', 'string', 'max:100'],
                'jenis_ruangan' => ['required', 'string'],
                'kapasitas_min' => ['required', 'numeric', 'min:1'],
                'kapasitas_max' => ['required', 'numeric', 'min:1'],
                'deskripsi_ruangan' => ['required', 'string'],
                'status_ruangan' => ['required', 'string', 'in:available,unavailable,adminOnly'],
            ]);

            $this->roomService->updateRoom($id, $validated);

            flash('success', 'Ruangan berhasil diperbarui!');
            redirect('/admin/rooms');
        } catch (ValidationException $e) {
            $room = $this->roomService->getRoomById($id);
            return view('Admin/Rooms/Edit', [
                'room' => $room,
                'validator' => $e->getValidator()
            ]);
        }
    }

    public function delete(Request $request)
    {

        if (!$request->isPost()) {
            redirect('/admin/rooms');
        }

        $id = (int) $request->input('id_ruangan');

        try {
            $this->roomService->deleteRoom($id);
            flash('success', 'Ruangan berhasil dihapus');
        } catch (\Exception $e) {
            flash('error', $e->getMessage());
        }

        redirect('/admin/rooms');
    }

    public function activate(Request $request)
    {
        $id = (int) $request->input('id_ruangan');

        try {
            $this->roomService->setRoomAvailable($id);
            flash('success', 'Ruangan berhasil diaktifkan');
        } catch (\Exception $e) {
            flash('error', $e->getMessage());
        }

        redirect('/admin/rooms');
    }

    public function deactivate(Request $request)
    {
        $id = (int) $request->input('id_ruangan');

        try {
            $this->roomService->setRoomUnavailable($id);
            flash('success', 'Ruangan berhasil dinonaktifkan');
        } catch (\Exception $e) {
            flash('error', $e->getMessage());
        }

        redirect('/admin/rooms');
    }

    public function setAdminOnly(Request $request)
    {
        $id = (int) $request->input('id_ruangan');

        try {
            $this->roomService->setRoomAdminOnly($id);
            flash('success', 'Ruangan berhasil diset sebagai AdminOnly');
        } catch (\Exception $e) {
            flash('error', $e->getMessage());
        }

        redirect('/admin/rooms');
    }

    public function activateAll(Request $request)
    {
        if (!$request->isPost()) {
            redirect('/admin/rooms');
        }

        try {
            $count = $this->roomService->activateAllRooms();
            flash('success', "Berhasil mengaktifkan {$count} ruangan");
        } catch (\Exception $e) {
            flash('error', $e->getMessage());
        }

        redirect('/admin/rooms');
    }

    public function deactivateAll(Request $request)
    {
        if (!$request->isPost()) {
            redirect('/admin/rooms');
        }

        try {
            $count = $this->roomService->deactivateAllRooms();
            flash('success', "Berhasil mengnonaktifkan {$count} ruangan");
        } catch (\Exception $e) {
            flash('error', $e->getMessage());
        }

        redirect('/admin/rooms');
    }
}
