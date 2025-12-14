<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\RoomService;

class UserRoomController extends Controller
{
    private const PER_PAGE = 15;

    public function __construct(
        private RoomService $roomService
    ) {
    }

    public function index(Request $request)
    {
        $this->setLayout('main');
        $this->setTitle('Daftar Ruangan | Library Booking App');

        $page = (int) ($request->input('page') ?? 1);
        $nama_ruangan = $request->input('nama_ruangan') ?? '';
        $tanggal = $request->input('tanggal') ?? '';
        $waktu_mulai = $request->input('waktu_mulai') ?? '';
        $jenis = $request->input('jenis_ruangan') ?? [];
        $status = $request->input('status_ruangan') ?? '';
        $kapasitasmin = $request->input('kapasitas_min') ?? '';
        $kapasitasmax = $request->input('kapasitas_max') ?? '';

        $filters = [
            'nama_ruangan' => $nama_ruangan,
            'tanggal' => $tanggal,
            'waktu_mulai' => $waktu_mulai,
            'jenis_ruangan' => $jenis,
            'status_ruangan' => $status,
            'kapasitas_min' => $kapasitasmin,
            'kapasitas_max' => $kapasitasmax,
        ];

        $paginatedRooms = $this->roomService->getAllRooms($filters, self::PER_PAGE, $page);

        return view('User/Rooms/Index', [
            'rooms' => $paginatedRooms->items,
            'pagination' => $paginatedRooms,
            'filters' => $filters,
        ]);
    }

    public function show(Request $request)
    {
        $this->setLayout('main');
        $this->setTitle('Detail Ruangan | Library Booking App');

        $data = $request->validate([
            'id_ruangan' => 'required|integer',
        ]);

        $id = (int) $data['id_ruangan'];
        $room = $this->roomService->getRoomById($id);

        if (!$room) {
            flash('error', 'Ruangan tidak ditemukan');
            redirect('/rooms');
        }

        // Block unavailable rooms for everyone
        if ($room->status_ruangan === 'unavailable') {
            flash('error', 'Ruangan tidak tersedia');
            redirect('/rooms');
        }

        // AdminOnly rooms: only allow Admin, Dosen, and Tendik
        if ($room->status_ruangan === 'adminOnly') {
            $user = auth()->user();
            if (!$user->isAdmin() && !$user->isDosen() && !$user->isTendik()) {
                flash('error', 'Ruangan ini hanya dapat diakses oleh Admin, Dosen, atau Tendik');
                redirect('/rooms');
            }
        }

        $photos = room_photos($room);
        $facilities = room_facilities($room);

        $availability = $this->roomService->getRoomAvailability($room->id_ruangan, 7);

        return view('User/Rooms/Show', [
            'room' => $room,
            'photos' => $photos,
            'facilities' => $facilities,
            'availability' => $availability,
        ]);
    }
}
