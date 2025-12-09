<?php

namespace App\Core\Repository;

use App\Core\Database;
use App\Core\QueryBuilder;
use App\Models\BookingInvitation;
use App\Models\User;

class InvitationRepository
{
    public function __construct(private Database $database)
    {
    }

    public function create(array $data): BookingInvitation
    {
        $invitation = new BookingInvitation();

        foreach ($data as $key => $value) {
            if (property_exists($invitation, $key)) {
                $invitation->{$key} = $value;
            }
        }

        $invitation->save();

        return $invitation;
    }

    public function findById(int $id): ?BookingInvitation
    {
        return BookingInvitation::Query()->where('id_invitation', $id)->first();
    }

    public function findByBookingAndUser(int $bookingId, int $userId): ?BookingInvitation
    {
        return BookingInvitation::Query()
            ->where('booking_id', $bookingId)
            ->where('invited_user_id', $userId)
            ->first();
    }

    public function getPendingForUser(int $userId): array
    {
        $qb = new QueryBuilder($this->database->pdo);
        return $qb->table('booking_invitations bi')
            ->select([
                'bi.*',
                'b.tanggal_penggunaan_ruang',
                'b.waktu_mulai',
                'b.waktu_selesai',
                'r.nama_ruangan',
                'r.jenis_ruangan',
                'u.nama as invited_by_name',
            ])
            ->join('booking b', 'bi.booking_id', '=', 'b.id_booking')
            ->join('ruangan r', 'b.ruangan_id', '=', 'r.id_ruangan')
            ->join('users u', 'bi.invited_by_user_id', '=', 'u.id_user')
            ->where('bi.invited_user_id', $userId)
            ->where('bi.status', 'pending')
            ->where('b.status', 'draft')
            ->orderBy('bi.created_at', 'desc')
            ->get();
    }

    public function getPendingForBooking(int $bookingId): array
    {
        $qb = new QueryBuilder($this->database->pdo);
        return $qb->table('booking_invitations bi')
            ->select(['bi.*', 'u.nama', 'u.email'])
            ->join('users u', 'bi.invited_user_id', '=', 'u.id_user')
            ->where('bi.booking_id', $bookingId)
            ->where('bi.status', 'pending')
            ->orderBy('bi.created_at', 'desc')
            ->get();
    }

    public function accept(int $invitationId): bool
    {
        $invitation = $this->findById($invitationId);

        if (!$invitation) {
            return false;
        }

        $invitation->status = 'accepted';
        return $invitation->save();
    }

    public function reject(int $invitationId): bool
    {
        $invitation = $this->findById($invitationId);

        if (!$invitation) {
            return false;
        }

        $invitation->status = 'rejected';
        return $invitation->save();
    }

    public function delete(int $invitationId): bool
    {
        $qb = new QueryBuilder($this->database->pdo);
        return $qb->table('booking_invitations')
            ->where('id_invitation', $invitationId)
            ->delete();
    }

    public function hasExistingInvitation(int $bookingId, int $userId): bool
    {
        return $this->findByBookingAndUser($bookingId, $userId) !== null;
    }

    public function findUserByIdentifier(string $identifier): ?object
    {
        $qb = new QueryBuilder($this->database->pdo);
        return $qb->table('users')
            ->setModel(User::class)
            ->where('email', $identifier)
            ->orWhere('nim', $identifier)
            ->orWhere('nip', $identifier)
            ->first();
    }

    public function resetToPending(int $invitationId): bool
    {
        $invitation = $this->findById($invitationId);

        if (!$invitation) {
            return false;
        }

        $invitation->status = 'pending';
        return $invitation->save();
    }

    public function countPendingForUser(int $userId): int
    {
        $qb = new QueryBuilder($this->database->pdo);
        return $qb->table('booking_invitations')
            ->where('invited_user_id', $userId)
            ->where('status', 'pending')
            ->count();
    }

    public function getPendingInvitedByPic(int $bookingId, int $picUserId): array
    {
        $qb = new QueryBuilder($this->database->pdo);
        return $qb->table('booking_invitations bi')
            ->select(['bi.*', 'u.nama', 'u.email', 'u.nim', 'u.nip'])
            ->join('users u', 'bi.invited_user_id', '=', 'u.id_user')
            ->where('bi.booking_id', $bookingId)
            ->where('bi.invited_by_user_id', $picUserId)
            ->where('bi.status', 'pending')
            ->get();
    }

    public function getPendingJoinRequests(int $bookingId): array
    {
        $qb = new QueryBuilder($this->database->pdo);
        return $qb->table('booking_invitations bi')
            ->select(['bi.*', 'u.nama', 'u.email', 'u.nim', 'u.nip'])
            ->join('users u', 'bi.invited_user_id', '=', 'u.id_user')
            ->where('bi.booking_id', $bookingId)
            ->whereNull('bi.invited_by_user_id')
            ->where('bi.status', 'pending')
            ->get();
    }

    public function getMyPendingJoinRequests(int $userId): array
    {
        $qb = new QueryBuilder($this->database->pdo);
        return $qb->table('booking_invitations bi')
            ->select([
                'bi.*',
                'b.tanggal_penggunaan_ruang',
                'b.waktu_mulai',
                'b.waktu_selesai',
                'b.tujuan',
                'r.nama_ruangan',
                'r.jenis_ruangan',
                'u.nama as pic_nama'
            ])
            ->join('booking b', 'bi.booking_id', '=', 'b.id_booking')
            ->join('ruangan r', 'b.ruangan_id', '=', 'r.id_ruangan')
            ->join('users u', 'b.user_id', '=', 'u.id_user')
            ->where('bi.invited_user_id', $userId)
            ->whereNull('bi.invited_by_user_id') // Self-request (via token)
            ->where('bi.status', 'pending')
            ->get();
    }

    public function rejectAllPendingForBooking(int $bookingId): int
    {
        $qb = new QueryBuilder($this->database->pdo);
        return $qb->table('booking_invitations')
            ->where('booking_id', $bookingId)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);
    }
}
