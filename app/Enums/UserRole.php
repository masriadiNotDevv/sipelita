<?php

namespace App\Enums;

/**
 * Peran pengguna pada Sistem Pelaporan Fasilitas Kampus SIPELITA.
 */
enum UserRole: string
{
    case Admin = 'admin';
    case Staff = 'staff';
    case Mahasiswa = 'mahasiswa';

    /**
     * Label peran untuk ditampilkan pada antarmuka.
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Staff => 'Staff',
            self::Mahasiswa => 'Mahasiswa',
        };
    }

    /**
     * Deskripsi singkat peran untuk dokumentasi.
     */
    public function description(): string
    {
        return match ($this) {
            self::Admin => 'Pengelola seluruh data sistem SIPELITA',
            self::Staff => 'Penindak lanjut laporan fasilitas',
            self::Mahasiswa => 'Pelapor masalah fasilitas kampus',
        };
    }

    /**
     * Material Symbol yang mewakili peran.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Admin => 'shield_person',
            self::Staff => 'support_agent',
            self::Mahasiswa => 'school',
        };
    }

    /**
     * Nama rute dashboard peran.
     */
    public function dashboardRoute(): string
    {
        return match ($this) {
            self::Admin => 'admin.dashboard',
            self::Staff => 'staff.dashboard',
            self::Mahasiswa => 'mahasiswa.dashboard',
        };
    }

    /**
     * Alamat dashboard peran.
     */
    public function dashboardPath(): string
    {
        return match ($this) {
            self::Admin => '/admin/dashboard',
            self::Staff => '/staff/dashboard',
            self::Mahasiswa => '/mahasiswa/dashboard',
        };
    }

    /**
     * Daftar seluruh nilai peran.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
