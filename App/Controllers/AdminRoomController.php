<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\RoomService;
use App\Core\Exceptions\ValidationException;
use Exception;

class AdminRoomController extends Controller
{
    private const PER_PAGE = 15;

    public function __construct(private RoomService $roomService)
    {
    }

    public function index(Request $request)
    {
        $this->setLayout('main');
        $this->setTitle('Kelola Ruangan | Library Booking App');

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

        $paginatedRooms = $this->roomService->getAllRooms($filters, self::PER_PAGE, $page);

        return view('Admin/Rooms/Index', [
            'rooms' => $paginatedRooms->items,
            'pagination' => $paginatedRooms,
            'filters' => $filters,
        ]);
    }

    public function create(Request $request)
    {
        $this->setLayout('main');
        $this->setTitle('Tambah Ruangan | Library Booking App');

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
        $this->setLayout('main');
        $this->setTitle('Edit Ruangan | Library Booking App');

        $data = $request->validate([
            'id' => 'required|integer',
        ]);

        $id = (int) $data['id'];
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
        $this->setLayout('main');
        $this->setTitle('Detail Ruangan | Library Booking App');

        $data = $request->validate([
            'id' => 'required|integer',
        ]);

        $id = (int) $data['id'];
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
                'nama_ruangan' => 'required|string|max:100',
                'jenis_ruangan' => 'required|string',
                'kapasitas_min' => 'required|numeric|min:1',
                'kapasitas_max' => 'required|numeric|min:1',
                'deskripsi_ruangan' => 'required|string',
                'status_ruangan' => 'required|string|in:available,unavailable,adminOnly',
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
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            redirect('/admin/rooms');
        }
    }

    public function delete(Request $request)
    {
        if (!$request->isPost()) {
            redirect('/admin/rooms');
        }

        try {
            $data = $request->validate([
                'id_ruangan' => 'required|integer',
            ]);

            $id = (int) $data['id_ruangan'];
            $this->roomService->deleteRoom($id);
            flash('success', 'Ruangan berhasil dihapus');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
        }

        redirect('/admin/rooms');
    }

    public function activate(Request $request)
    {
        try {
            $data = $request->validate([
                'id_ruangan' => 'required|integer',
            ]);

            $id = (int) $data['id_ruangan'];
            $this->roomService->setRoomAvailable($id);
            flash('success', 'Ruangan berhasil diaktifkan');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
        }

        redirect('/admin/rooms');
    }

    public function deactivate(Request $request)
    {
        try {
            $data = $request->validate([
                'id_ruangan' => 'required|integer',
            ]);

            $id = (int) $data['id_ruangan'];
            $this->roomService->setRoomUnavailable($id);
            flash('success', 'Ruangan berhasil dinonaktifkan');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
        }

        redirect('/admin/rooms');
    }

    public function setAdminOnly(Request $request)
    {
        try {
            $data = $request->validate([
                'id_ruangan' => 'required|integer',
            ]);

            $id = (int) $data['id_ruangan'];
            $this->roomService->setRoomAdminOnly($id);
            flash('success', 'Ruangan berhasil diset sebagai AdminOnly');
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
            flash('error', $e->getMessage());
        }

        redirect('/admin/rooms');
    }
}
