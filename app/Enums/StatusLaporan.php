<?php

namespace App\Enums;

/**
 * Status penanganan laporan fasilitas pada SIPELITA.
 */
enum StatusLaporan: string
{
    case Menunggu = 'menunggu';
    case Diproses = 'diproses';
    case DalamPerbaikan = 'dalam_perbaikan';
    case Selesai = 'selesai';
    case Ditolak = 'ditolak';

    /**
     * Label status untuk ditampilkan pada antarmuka.
     */
    public function label(): string
    {
        return match ($this) {
            self::Menunggu => 'Menunggu',
            self::Diproses => 'Diproses',
            self::DalamPerbaikan => 'Dalam Perbaikan',
            self::Selesai => 'Selesai',
            self::Ditolak => 'Ditolak',
        };
    }

    /**
     * Nama ikon Material Symbol yang mewakili status.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Menunggu => 'hourglass_empty',
            self::Diproses => 'pending_actions',
            self::DalamPerbaikan => 'construction',
            self::Selesai => 'task_alt',
            self::Ditolak => 'cancel',
        };
    }

    /**
     * Kelas warna Tailwind untuk badge status.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Menunggu => 'bg-amber-100 text-amber-800',
            self::Diproses => 'bg-sky-100 text-sky-800',
            self::DalamPerbaikan => 'bg-brand-100 text-brand-800',
            self::Selesai => 'bg-emerald-100 text-emerald-800',
            self::Ditolak => 'bg-red-100 text-red-800',
        };
    }

    /**
     * Daftar seluruh nilai status.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
