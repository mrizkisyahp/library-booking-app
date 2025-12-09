<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Services\BookingServices;
use App\Core\Services\InvitationService;
use Exception;

class UserBookingController extends Controller
{
    public function __construct(
        private BookingServices $bookingServices,
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

            $pendingInvitations = $this->bookingServices->getPendingInvitedByPic($bookingId, $data['booking']->user_id);
            $joinRequests = $this->bookingServices->getPendingJoinRequests($bookingId);

            return view('User/Bookings/Draft', [
                'booking' => $data['booking'],
                'pic' => $data['pic'],
                'members' => $data['members'],
                'allMembers' => $data['allMembers'],
                'isPic' => $data['isPic'],
                'isMember' => $data['isMember'],
                'canSubmit' => $data['canSubmit'],
                'pendingInvitations' => $pendingInvitations,
                'joinRequests' => $joinRequests,
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

            $result = $this->bookingServices->joinViaInviteToken($token, $user->id_user);

            if ($result['auto_joined']) {
                flash('success', 'Undangan diterima! Anda sekarang menjadi anggota booking');
                redirect('/bookings/draft?id=' . $result['booking_id']);
            } else {
                flash('success', 'Permintaan bergabung telah dikirim. Menunggu persetujuan PIC.');
                redirect('/dashboard');
            }
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

    public function showEditDraft(Request $request)
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

            if (!$data['isPic']) {
                flash('error', 'Hanya PIC yang dapat mengedit booking');
                redirect('/bookings/draft?id=' . $bookingId);
            }

            if ($booking->status !== 'draft') {
                flash('error', 'Hanya booking dengan status draft yang dapat diedit');
                redirect('/bookings/draft?id=' . $bookingId);
            }

            $rooms = $this->bookingServices->getAllRooms();
            return view('User/Bookings/EditDraft', [
                'booking' => $booking,
                'rooms' => $rooms,
            ]);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            redirect('/my-bookings');
        }
    }

    public function updateDraft(Request $request)
    {
        try {
            $user = auth()->user();
            $bookingId = (int) $request->all()['booking_id'];

            $data = [
                'ruangan_id' => (int) $request->all()['ruangan_id'],
                'tanggal_penggunaan_ruang' => $request->all()['tanggal_penggunaan_ruang'],
                'waktu_mulai' => $request->all()['waktu_mulai'],
                'waktu_selesai' => $request->all()['waktu_selesai'],
                'tujuan' => $request->all()['tujuan'],
            ];

            $this->bookingServices->validateUpdateDraftRules($data, $bookingId);

            $this->bookingServices->validateNoTimeConflicts($data, $user->id_user, $bookingId);

            $this->bookingServices->updateDraft($bookingId, $data, $user->id_user);

            flash('success', 'Draft booking berhasil diperbarui');
            redirect('/bookings/draft?id=' . $bookingId);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function deleteDraft(Request $request)
    {
        try {
            $user = auth()->user();
            $bookingId = (int) $request->all()['booking_id'];

            $this->bookingServices->deleteDraft($bookingId, $user->id_user);

            flash('success', 'Draft booking berhasil dihapus');
            redirect('/my-bookings');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function cancelPending(Request $request)
    {
        try {
            $user = auth()->user();
            $bookingId = (int) $request->all()['booking_id'];

            $this->bookingServices->cancelPending($bookingId, $user->id_user);

            flash('success', 'Booking berhasil dibatalkan');
            redirect('/my-bookings');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
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

    public function send(Request $request)
    {
        try {
            $user = auth()->user();
            $data = $request->all();

            $invitedUser = $this->bookingServices->findUserByIdentifier($data['identifier']);

            if (!$invitedUser) {
                throw new Exception('User tidak ditemukan');
            }

            $autoApproved = $this->bookingServices->sendInvitation(
                (int) $data['booking_id'],
                (int) $invitedUser->id_user,
                $user->id_user
            );

            if ($autoApproved) {
                flash('success', 'User sudah ditambahkan sebagai anggota (permintaan sebelumnya disetujui)');
            } else {
                flash('success', 'Undangan berhasil dikirim');
            }

            redirect('/bookings/draft?id=' . $data['booking_id']);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            redirect('/bookings/draft?id=' . $data['booking_id']);
        }
    }

    public function accept(Request $request)
    {
        try {
            $user = auth()->user();
            $invitationId = (int) $request->all()['invitation_id'];

            $bookingId = $this->bookingServices->acceptInvitation($invitationId, $user->id_user);

            flash('success', 'Undangan diterima! Anda sekarang menjadi anggota booking');
            redirect('/bookings/draft?id=' . $bookingId);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function reject(Request $request)
    {
        try {
            $user = auth()->user();
            $invitationId = (int) $request->all()['invitation_id'];

            $this->bookingServices->rejectInvitation($invitationId, $user->id_user);

            flash('success', 'Undangan ditolak');
            redirect('/dashboard');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function cancel(Request $request)
    {
        try {
            $user = auth()->user();
            $invitationId = (int) $request->all()['invitation_id'];
            $bookingId = (int) $request->all()['booking_id'];

            $this->bookingServices->cancelInvitation($invitationId, $user->id_user);

            flash('success', 'Undangan dibatalkan');
            redirect('/bookings/draft?id=' . $bookingId);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function approveJoinRequest(Request $request)
    {
        try {
            $user = auth()->user();
            $invitationId = (int) $request->all()['invitation_id'];

            $bookingId = (int) $request->all()['booking_id'];
            $this->bookingServices->approveJoinRequest($invitationId, $user->id_user);

            flash('success', 'Permintaan bergabung diterima');
            redirect('/bookings/draft?id=' . $bookingId);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function rejectJoinRequest(Request $request)
    {
        try {
            $user = auth()->user();
            $invitationId = (int) $request->all()['invitation_id'];
            $bookingId = (int) $request->all()['booking_id'];

            $this->bookingServices->rejectJoinRequest($invitationId, $user->id_user);

            flash('success', 'Permintaan bergabung ditolak');
            redirect('/bookings/draft?id=' . $bookingId);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function cancelJoinRequest(Request $request)
    {
        try {
            $user = auth()->user();
            $invitationId = (int) $request->all()['invitation_id'];

            $this->bookingServices->cancelJoinRequest($invitationId, $user->id_user);

            flash('success', 'Permintaan bergabung dibatalkan');
            redirect('/dashboard');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }
}
