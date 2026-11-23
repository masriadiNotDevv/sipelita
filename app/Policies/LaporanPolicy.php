<?php

namespace App\Policies;

use App\Enums\StatusLaporan;
use App\Enums\UserRole;
use App\Models\Laporan;
use App\Models\User;

class LaporanPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Laporan $laporan): bool
    {
        return $laporan->user_id === $user->id
            || $user->hasAnyRole([UserRole::Admin, UserRole::Staff]);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::Mahasiswa);
    }

    public function update(User $user, Laporan $laporan): bool
    {
        if ($user->hasRole(UserRole::Admin)) {
            return true;
        }

        return $laporan->user_id === $user->id
            && in_array($laporan->status, [StatusLaporan::Menunggu, StatusLaporan::Diproses], true);
    }

    public function delete(User $user, Laporan $laporan): bool
    {
        return $user->hasRole(UserRole::Admin)
            || ($laporan->user_id === $user->id && $laporan->status === StatusLaporan::Menunggu);
    }

    public function restore(User $user, Laporan $laporan): bool
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function klaim(User $user, Laporan $laporan): bool
    {
        return $user->hasRole(UserRole::Staff)
            && $laporan->ditangani_oleh === null
            && in_array($laporan->status, [
                StatusLaporan::Menunggu,
                StatusLaporan::Diproses,
                StatusLaporan::DalamPerbaikan,
            ], true);
    }

    public function tugaskan(User $user, Laporan $laporan): bool
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function proses(User $user, Laporan $laporan): bool
    {
        return $user->hasRole(UserRole::Admin)
            || ($user->hasRole(UserRole::Staff) && $laporan->ditangani_oleh === $user->id);
    }
}
