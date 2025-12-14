<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Exceptions\ValidationException;
use App\Core\Request;
use App\Repositories\InvitationRepository;
use App\Services\BookingService;
use Exception;

class UserBookingController extends Controller
{
    private const PER_PAGE_MEMBERS = 6;
    private const PER_PAGE_BOOKINGS = 15;

    public function __construct(
        private BookingService $bookingService,
        private InvitationRepository $invitationRepo,
    ) {
    }

    public function createDraft(Request $request)
    {
        try {
            $user = auth()->user();

            // Prevent draft creation if library is closed (admins bypass)
            $isAdmin = $user->id_role === 1;
            if (!$isAdmin && isLibraryEffectivelyClosed()) {
                $reason = getClosureReason(date('Y-m-d')) ?? 'Semua ruangan diblokir';
                throw new Exception("Tidak dapat membuat booking: Perpustakaan sedang tutup. Alasan: $reason");
            }

            $data = $request->all();
            $data['user_id'] = $user->id_user;
            $data['ruangan_id'] = (int) trim($data['ruangan_id']);

            // Get room to check if requires special approval (document upload)
            $room = $this->bookingService->findRoomById($data['ruangan_id']);

            // Handle surat file upload for rooms that require special approval
            $suratPath = null;
            if ($room && $room->requires_special_approval && !$isAdmin) {
                $file = $request->file('pegawai_file');

                if (empty($file) || $file['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception('Surat wajib diunggah untuk ruangan ini');
                }

                // Validate file type
                $allowedTypes = ['application/pdf', 'image/png', 'image/jpeg'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                if (!in_array($mimeType, $allowedTypes)) {
                    throw new Exception('Format file harus PDF, JPG, atau PNG');
                }

                // Validate file size (2MB max)
                $maxSize = 2 * 1024 * 1024;
                if ($file['size'] > $maxSize) {
                    throw new Exception('Ukuran file maksimal 2MB');
                }

                // Generate filename and upload
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'surat_' . $user->id_user . '_' . time() . '.' . $extension;

                $uploadDir = dirname(__DIR__, 2) . '/Public/uploads/surat/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $destination = $uploadDir . $filename;
                if (!move_uploaded_file($file['tmp_name'], $destination)) {
                    throw new Exception('Gagal menyimpan file surat');
                }

                $suratPath = $filename;
            }

            $data['surat_path'] = $suratPath;

            $this->bookingService->validateBookingRules($data, $user);
            $this->bookingService->validateNoTimeConflicts($data, $user->id_user);

            $booking = $this->bookingService->createDraft($data);

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
        $this->setLayout('main');
        $this->setTitle('Draft Booking | Library Booking App');

        try {
            $data = $request->validate([
                'id' => 'required|integer',
            ]);

            $user = auth()->user();
            $bookingId = (int) $data['id'];
            $page = (int) $request->query('page', 1);

            $bookingData = $this->bookingService->getBookingForUser(
                $bookingId,
                $user->id_user,
                $user->id_role === 1,
                $page,
                self::PER_PAGE_MEMBERS
            );

            $pendingInvitations = $this->invitationRepo->getPendingForBooking($bookingId);
            $joinRequests = $this->invitationRepo->getPendingJoinRequests($bookingId);

            return view('User/Bookings/Draft', [
                'booking' => $bookingData['booking'],
                'allMembers' => $bookingData['allMembers'],
                'pagination' => $bookingData['allMembers'],
                'pic' => $bookingData['pic'],
                'isPic' => $bookingData['isPic'],
                'isMember' => $bookingData['isMember'],
                'canSubmit' => $bookingData['canSubmit'],
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
            $data = $request->validate([
                'booking_id' => 'required|integer',
            ]);

            $user = auth()->user();
            $bookingId = (int) $data['booking_id'];

            $this->bookingService->submitForApproval($bookingId, $user->id_user);

            flash('success', 'Booking berhasil diajukan untuk persetujuan admin');
            redirect('/bookings/detail?id=' . $bookingId);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function showJoinForm(Request $request)
    {
        $this->setLayout('main');
        $this->setTitle('Gabung Booking | Library Booking App');

        $prefill = $request->query()['code'] ?? '';
        return view('User/Bookings/Join', ['prefill' => $prefill]);
    }

    public function joinByLink(Request $request)
    {
        try {
            $data = $request->validate([
                'invite_token' => 'required|string',
            ]);

            $user = auth()->user();
            $token = $data['invite_token'];

            $result = $this->bookingService->joinViaInviteToken($token, $user->id_user);

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
        $this->setLayout('main');
        $this->setTitle('Booking Saya | Library Booking App');

        $user = auth()->user();
        $page = (int) ($request->query()['page'] ?? 1);

        $filters = [
            'nama_ruangan' => $request->input('nama_ruangan') ?? '',
            'tanggal' => $request->input('tanggal') ?? '',
            'waktu_mulai' => $request->input('waktu_mulai') ?? '',
            'kapasitas_min' => $request->input('kapasitas_min') ?? '',
            'jenis_ruangan' => $request->input('jenis_ruangan') ?? [],
        ];

        $bookings = $this->bookingService->getBookingsByUser($user->id_user, $filters, self::PER_PAGE_BOOKINGS, $page);
        return view('User/Bookings/Index', [
            'bookings' => $bookings->items,
            'pagination' => $bookings,
            'filters' => $filters,
        ]);
    }

    public function cancelBooking(Request $request)
    {
        try {
            $data = $request->validate([
                'booking_id' => 'required|integer',
                'reason' => 'nullable|string|max:500',
            ]);

            $user = auth()->user();
            $bookingId = (int) $data['booking_id'];
            $reason = $data['reason'] ?? 'Dibatalkan oleh user';

            $this->bookingService->cancelBooking($bookingId, $user->id_user, $reason);

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
            $data = $request->validate([
                'booking_id' => 'required|integer',
            ]);

            $user = auth()->user();
            $bookingId = (int) $data['booking_id'];

            $this->bookingService->leaveBooking($bookingId, $user->id_user);

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
            $data = $request->validate([
                'booking_id' => 'required|integer',
                'user_id' => 'required|integer',
            ]);

            $user = auth()->user();
            $bookingId = (int) $data['booking_id'];
            $memberId = (int) $data['user_id'];

            $this->bookingService->kickMember($bookingId, $memberId, $user->id_user);

            flash('success', 'Anggota berhasil dikeluarkan');
            redirect('/bookings/draft?id=' . $bookingId);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function detail(Request $request)
    {
        $this->setLayout('main');
        $this->setTitle('Detail Booking | Library Booking App');

        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ]);

            $user = auth()->user();
            $bookingId = (int) $validated['id'];
            $page = (int) $request->query('page', 1);

            $data = $this->bookingService->getBookingForUser(
                $bookingId,
                $user->id_user,
                $user->id_role === 1,
                $page,
                self::PER_PAGE_MEMBERS
            );

            $rescheduleRequest = $this->bookingService->getPendingRescheduleRequest($bookingId);

            return view('User/Bookings/Detail', [
                'booking' => $data['booking'],
                'allMembers' => $data['allMembers'],
                'pagination' => $data['allMembers'],
                'pic' => $data['pic'],
                'isPic' => $data['isPic'],
                'isMember' => $data['isMember'],
                'canSubmit' => $data['canSubmit'],
                'rescheduleRequest' => $rescheduleRequest,
            ]);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            redirect('/my-bookings');
        }
    }

    public function showEditDraft(Request $request)
    {
        $this->setLayout('main');
        $this->setTitle('Edit Draft Booking | Library Booking App');

        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ]);

            $user = auth()->user();
            $bookingId = (int) $validated['id'];

            $data = $this->bookingService->getBookingForUser(
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

            $rooms = $this->bookingService->getAllRooms();
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
            $validated = $request->validate([
                'booking_id' => 'required|integer',
                'ruangan_id' => 'required|integer',
                'tanggal_penggunaan_ruang' => 'required|date',
                'waktu_mulai' => 'required',
                'waktu_selesai' => 'required',
                'tujuan' => 'required|string|max:500',
            ]);

            $user = auth()->user();
            $bookingId = (int) $validated['booking_id'];

            $data = [
                'ruangan_id' => (int) $validated['ruangan_id'],
                'tanggal_penggunaan_ruang' => $validated['tanggal_penggunaan_ruang'],
                'waktu_mulai' => $validated['waktu_mulai'],
                'waktu_selesai' => $validated['waktu_selesai'],
                'tujuan' => $validated['tujuan'],
            ];

            $this->bookingService->validateUpdateDraftRules($data, $bookingId);
            $this->bookingService->validateNoTimeConflicts($data, $user->id_user, $bookingId);
            $this->bookingService->updateDraft($bookingId, $data, $user->id_user);

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
            $data = $request->validate([
                'booking_id' => 'required|integer',
            ]);

            $user = auth()->user();
            $bookingId = (int) $data['booking_id'];

            $this->bookingService->deleteDraft($bookingId, $user->id_user);

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
            $data = $request->validate([
                'booking_id' => 'required|integer',
            ]);

            $user = auth()->user();
            $bookingId = (int) $data['booking_id'];

            $this->bookingService->cancelPending($bookingId, $user->id_user);

            flash('success', 'Booking berhasil dibatalkan');
            redirect('/bookings/draft?id=' . $bookingId);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function showRescheduleForm(Request $request)
    {
        $this->setLayout('main');
        $this->setTitle('Reschedule Booking | Library Booking App');

        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ]);

            $user = auth()->user();
            $bookingId = (int) $validated['id'];

            $data = $this->bookingService->getBookingForUser(
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
            $validated = $request->validate([
                'booking_id' => 'required|integer',
                'tanggal_penggunaan_ruang' => 'required|date',
                'waktu_mulai' => 'required',
                'waktu_selesai' => 'required',
            ]);

            $user = auth()->user();
            $bookingId = (int) $validated['booking_id'];
            $newDate = $validated['tanggal_penggunaan_ruang'];
            $newStart = $validated['waktu_mulai'];
            $newEnd = $validated['waktu_selesai'];

            $data = $this->bookingService->getBookingForUser(
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
            $validated = $request->validate([
                'booking_id' => 'required|integer',
                'tanggal_penggunaan_ruang' => 'required|date',
                'waktu_mulai' => 'required',
                'waktu_selesai' => 'required',
            ]);

            $user = auth()->user();
            $bookingId = (int) $validated['booking_id'];

            $newData = [
                'tanggal_penggunaan_ruang' => $validated['tanggal_penggunaan_ruang'],
                'waktu_mulai' => $validated['waktu_mulai'],
                'waktu_selesai' => $validated['waktu_selesai'],
            ];

            $this->bookingService->createRescheduleRequest($bookingId, $newData, $user->id_user);

            flash('success', 'Permintaan reschedule terkirim. Menunggu persetujuan admin.');
            redirect('/bookings/detail?id=' . $bookingId);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function send(Request $request)
    {
        $bookingId = null;
        try {
            $data = $request->validate([
                'booking_id' => 'required|integer',
                'identifier' => 'required|string',
            ]);

            $bookingId = (int) $data['booking_id'];
            $user = auth()->user();

            $invitedUser = $this->bookingService->findUserByIdentifier($data['identifier']);

            if (!$invitedUser) {
                throw new Exception('User tidak ditemukan');
            }

            $autoApproved = $this->bookingService->sendInvitation(
                $bookingId,
                (int) $invitedUser->id_user,
                $user->id_user
            );

            if ($autoApproved) {
                flash('success', 'User sudah ditambahkan sebagai anggota (permintaan sebelumnya disetujui)');
            } else {
                flash('success', 'Undangan berhasil dikirim');
            }

            redirect('/bookings/draft?id=' . $bookingId);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            if ($bookingId) {
                redirect('/bookings/draft?id=' . $bookingId);
            } else {
                back();
            }
        }
    }

    public function accept(Request $request)
    {
        try {
            $data = $request->validate([
                'invitation_id' => 'required|integer',
            ]);

            $user = auth()->user();
            $invitationId = (int) $data['invitation_id'];

            $bookingId = $this->bookingService->acceptInvitation($invitationId, $user->id_user);

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
            $data = $request->validate([
                'invitation_id' => 'required|integer',
            ]);

            $user = auth()->user();
            $invitationId = (int) $data['invitation_id'];

            $this->bookingService->rejectInvitation($invitationId, $user->id_user);

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
            $data = $request->validate([
                'invitation_id' => 'required|integer',
                'booking_id' => 'required|integer',
            ]);

            $user = auth()->user();
            $invitationId = (int) $data['invitation_id'];
            $bookingId = (int) $data['booking_id'];

            $this->bookingService->cancelInvitation($invitationId, $user->id_user);

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
            $data = $request->validate([
                'invitation_id' => 'required|integer',
                'booking_id' => 'required|integer',
            ]);

            $user = auth()->user();
            $invitationId = (int) $data['invitation_id'];
            $bookingId = (int) $data['booking_id'];

            $this->bookingService->approveJoinRequest($invitationId, $user->id_user);

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
            $data = $request->validate([
                'invitation_id' => 'required|integer',
                'booking_id' => 'required|integer',
            ]);

            $user = auth()->user();
            $invitationId = (int) $data['invitation_id'];
            $bookingId = (int) $data['booking_id'];

            $this->bookingService->rejectJoinRequest($invitationId, $user->id_user);

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
            $data = $request->validate([
                'invitation_id' => 'required|integer',
            ]);

            $user = auth()->user();
            $invitationId = (int) $data['invitation_id'];

            $this->bookingService->cancelJoinRequest($invitationId, $user->id_user);

            flash('success', 'Permintaan bergabung dibatalkan');
            redirect('/dashboard');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }
}
