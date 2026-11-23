<?php

namespace App\Policies;

use App\Enums\StatusLaporan;
use App\Models\Laporan;
use App\Models\User;

class LaporanPolicy
{
    /**
     * Mahasiswa hanya dapat melihat daftar laporan miliknya sendiri.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Laporan hanya dapat dibuka oleh pelapornya.
     */
    public function view(User $user, Laporan $laporan): bool
    {
        return $laporan->user_id === $user->id;
    }

    /**
     * Mahasiswa dapat mengajukan laporan baru.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Pelapor dapat memperbarui laporan selama belum selesai ditangani.
     */
    public function update(User $user, Laporan $laporan): bool
    {
        return $laporan->user_id === $user->id
            && in_array($laporan->status, [
                StatusLaporan::Menunggu,
                StatusLaporan::Diproses,
            ], true);
    }

    /**
     * Pelapor dapat membatalkan laporan yang belum mulai diproses.
     */
    public function delete(User $user, Laporan $laporan): bool
    {
        return $laporan->user_id === $user->id
            && $laporan->status === StatusLaporan::Menunggu;
    }
}
