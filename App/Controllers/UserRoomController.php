<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Services\RoomService;

class UserRoomController extends Controller
{
    public function __construct(
        private RoomService $roomService
    ) {
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
            'jenis_ruangan' => $jenis,
            'status_ruangan' => $status,
            'kapasitas_min' => $kapasitasmin,
            'kapasitas_max' => $kapasitasmax,
        ];

        $roomTypes = [
            'Audio Visual',
            'Telekonferensi',
            'Kreasi dan Rekreasi',
            'Baca Kelompok',
            'Koleksi Bahasa Prancis',
            'Bimbingan & Konseling',
            'Ruang Rapat',
        ];

        $paginatedRooms = $this->roomService->getAllRooms($filters, 15, $page);

        return view('User/Rooms/Index', [
            'rooms' => $paginatedRooms->items,
            'pagination' => $paginatedRooms,
            'filters' => $filters,
            'roomTypes' => $roomTypes,
        ]);
    }

    public function show(Request $request)
    {
        $id = (int) $request->query('id_ruangan');
        $room = $this->roomService->getRoomById($id);

        if (!$room) {
            flash('error', 'Ruangan tidak ditemukan');
            redirect('/rooms');
        }

        if ($room->status_ruangan === 'adminOnly' || $room->status_ruangan === 'unavailable') {
            flash('error', 'Ruangan tidak tersedia');
            redirect('/rooms');
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
