<?php

namespace App\Enums;

/**
 * Prioritas laporan fasilitas pada SIPELITA.
 */
enum PrioritasLaporan: string
{
    case Rendah = 'rendah';
    case Sedang = 'sedang';
    case Tinggi = 'tinggi';
    case Mendesak = 'mendesak';

    /**
     * Label prioritas untuk ditampilkan pada antarmuka.
     */
    public function label(): string
    {
        return match ($this) {
            self::Rendah => 'Rendah',
            self::Sedang => 'Sedang',
            self::Tinggi => 'Tinggi',
            self::Mendesak => 'Mendesak',
        };
    }

    /**
     * Nama ikon Material Symbol yang mewakili prioritas.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Rendah => 'arrow_downward',
            self::Sedang => 'swap_vert',
            self::Tinggi => 'arrow_upward',
            self::Mendesak => 'priority_high',
        };
    }

    /**
     * Kelas warna Tailwind untuk badge prioritas.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Rendah => 'bg-gray-100 text-gray-700',
            self::Sedang => 'bg-sky-100 text-sky-800',
            self::Tinggi => 'bg-amber-100 text-amber-800',
            self::Mendesak => 'bg-red-100 text-red-800',
        };
    }

    /**
     * Daftar seluruh nilai prioritas.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
