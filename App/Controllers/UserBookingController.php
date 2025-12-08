<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Services\BookingServices;
use Exception;

class UserBookingController extends Controller
{
    public function __construct(
        private BookingServices $bookingServices
    ) {
    }

    public function createDraft(Request $request)
    {
        try {
            $user = auth()->user();
            $data = $request->all();
            $data['user_id'] = $user->id_user;
            $data['ruangan_id'] = (int) trim($data['ruangan_id']);

            $this->bookingServices->validateBookingRules($data, $user);
            $this->bookingServices->validateNoTimeConflicts($data, $user->id_user);

            $booking = $this->bookingServices->createDraft($data);

            flash('success', 'Draft booking berhasil dibuat');
            redirect('/bookings/draft?id=' . $booking->id_booking);
        } catch (Exception $e) {
            foreach ($request->all() as $key => $value) {
                flash('old_' . $key, $value);
            }
            flash('error', $e->getMessage());
            back();
        }
    }
    public function showDraft(Request $request)
    {
        try {
            $user = auth()->user();
            $bookingId = (int) $request->query()['id'];

            $data = $this->bookingServices->getBookingForUser(
                $bookingId,
                $user->id_user,
                $user->id_role === 1,
            );

            return view('User/Bookings/Draft', [
                'booking' => $data['booking'],
                'pic' => $data['pic'],
                'members' => $data['members'],
                'allMembers' => $data['allMembers'],
                'isPic' => $data['isPic'],
                'isMember' => $data['isMember'],
                'canSubmit' => $data['canSubmit'],
            ]);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function submitDraft(Request $request)
    {
        try {
            $user = auth()->user();
            $bookingId = (int) $request->all()['booking_id'];

            $this->bookingServices->submitForApproval($bookingId, $user->id_user);

            flash('success', 'Booking berhasil diajukan untuk persetujuan admin');
            redirect('/my-bookings');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function addMember(Request $request)
    {
        try {
            $user = auth()->user();
            $bookingId = (int) $request->all()['booking_id'];
            $memberUserId = (int) $request->all()['member_user_id'];

            $this->bookingServices->addMember($bookingId, $memberUserId, $user->id_user);

            flash('success', 'Anggota berhasil ditambahkan');
            back();
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function showJoinForm(Request $request)
    {
        $prefill = $request->query()['code'] ?? '';
        return view('User/Bookings/Join', ['prefill' => $prefill]);
    }

    public function joinByLink(Request $request)
    {
        try {
            $user = auth()->user();
            $token = $request->all()['invite_token'];

            $bookingId = $this->bookingServices->joinViaInviteToken($token, $user->id_user);

            flash('success', 'Berhasil bergabung dengan booking');
            redirect('/bookings/draft?id=' . $bookingId);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function showMyBooking(Request $request)
    {
        $user = auth()->user();
        $page = (int) ($request->query()['page'] ?? 1);

        $filters = [
            'nama_ruangan' => $request->input('nama_ruangan') ?? '',
            'tanggal' => $request->input('tanggal') ?? '',
            'waktu_mulai' => $request->input('waktu_mulai') ?? '',
            'kapasitas_min' => $request->input('kapasitas_min') ?? '',
            'jenis_ruangan' => $request->input('jenis_ruangan') ?? [],
        ];

        $bookings = $this->bookingServices->getBookingsByUser($user->id_user, $filters, 15, $page);
        return view('User/Bookings/Index', [
            'bookings' => $bookings->items,
            'pagination' => $bookings,
            'filters' => $filters,
        ]);
    }

    public function cancelBooking(Request $request)
    {
        try {
            $user = auth()->user();
            $bookingId = (int) $request->all()['booking_id'];
            $reason = $request->all()['reason'] ?? 'Dibatalkan oleh user';

            $this->bookingServices->cancelBooking($bookingId, $user->id_user, $reason);

            flash('success', 'Booking berhasil dibatalkan');
            redirect('/my-bookings');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function leaveBooking(Request $request)
    {
        try {
            $user = auth()->user();
            $bookingId = (int) $request->all()['booking_id'];

            $this->bookingServices->leaveBooking($bookingId, $user->id_user);

            flash('success', 'Berhasil meninggalkan booking');
            redirect('/my-bookings');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function kickMember(Request $request)
    {
        try {
            $user = auth()->user();
            $bookingId = (int) $request->all()['booking_id'];
            $memberId = (int) $request->all()['user_id'];

            $this->bookingServices->kickMember($bookingId, $memberId, $user->id_user);

            flash('success', 'Anggota berhasil dikeluarkan');
            redirect('/bookings/draft?id=' . $bookingId);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function detail(Request $request)
    {
        try {
            $user = auth()->user();
            $bookingId = (int) $request->query('id');

            $data = $this->bookingServices->getBookingForUser(
                $bookingId,
                $user->id_user,
                $user->id_role === 1,
            );

            return view('User/Bookings/Detail', [
                'booking' => $data['booking'],
                'pic' => $data['pic'],
                'members' => $data['members'],
                'allMembers' => $data['allMembers'],
                'canSubmit' => $data['canSubmit'],
                'isPic' => $data['isPic'],
                'isMember' => $data['isMember'],
            ]);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            redirect('/my-bookings');
        }
    }

    public function showRescheduleForm(Request $request)
    {
        try {
            $user = auth()->user();
            $bookingId = (int) $request->query('id');

            $data = $this->bookingServices->getBookingForUser(
                $bookingId,
                $user->id_user,
                $user->id_role === 1,
            );

            $booking = $data['booking'];

            // Only PIC can reschedule
            if (!$data['isPic']) {
                flash('error', 'Hanya PIC yang dapat melakukan reschedule');
                redirect('/bookings/detail?id=' . $bookingId);
            }

            // Only verified bookings can be rescheduled
            if ($booking->status !== 'verified') {
                flash('error', 'Hanya booking dengan status verified yang dapat di-reschedule');
                redirect('/bookings/detail?id=' . $bookingId);
            }

            return view('User/Bookings/Reschedule', [
                'booking' => $booking,
            ]);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            redirect('/my-bookings');
        }
    }

    public function confirmReschedule(Request $request)
    {
        try {
            $user = auth()->user();
            $bookingId = (int) $request->all()['booking_id'];
            $newDate = $request->all()['tanggal_penggunaan_ruang'];
            $newStart = $request->all()['waktu_mulai'];
            $newEnd = $request->all()['waktu_selesai'];

            $data = $this->bookingServices->getBookingForUser(
                $bookingId,
                $user->id_user,
                $user->id_role === 1,
            );

            $booking = $data['booking'];

            if (!$data['isPic']) {
                flash('error', 'Hanya PIC yang dapat melakukan reschedule');
                redirect('/bookings/detail?id=' . $bookingId);
            }

            if ($booking->status !== 'verified') {
                flash('error', 'Hanya booking dengan status verified yang dapat di-reschedule');
                redirect('/bookings/detail?id=' . $bookingId);
            }

            return view('User/Bookings/RescheduleConfirm', [
                'booking' => $booking,
                'newDate' => $newDate,
                'newStart' => $newStart,
                'newEnd' => $newEnd,
            ]);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            redirect('/my-bookings');
        }
    }

    public function reschedule(Request $request)
    {
        try {
            $user = auth()->user();
            $bookingId = (int) $request->all()['booking_id'];

            $newData = [
                'tanggal_penggunaan_ruang' => $request->all()['tanggal_penggunaan_ruang'],
                'waktu_mulai' => $request->all()['waktu_mulai'],
                'waktu_selesai' => $request->all()['waktu_selesai'],
            ];

            $this->bookingServices->rescheduleBooking($bookingId, $newData, $user->id_user);

            flash('success', 'Booking berhasil di-reschedule. Status kembali ke pending.');
            redirect('/bookings/detail?id=' . $bookingId);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }
}
