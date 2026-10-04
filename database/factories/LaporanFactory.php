<?php

namespace Database\Factories;

use App\Enums\PrioritasLaporan;
use App\Enums\StatusLaporan;
use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Laporan>
 */
class LaporanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode' => Laporan::nextKode(),
            'user_id' => User::factory(),
            'kategori_id' => Kategori::factory(),
            'judul' => rtrim(fake()->sentence(6), '.'),
            'deskripsi' => fake()->paragraph(),
            'lokasi' => 'Gedung '.fake()->randomElement(['A', 'B', 'C', 'D']).' Lantai '.fake()->numberBetween(1, 4),
            'prioritas' => fake()->randomElement(PrioritasLaporan::cases()),
            'status' => StatusLaporan::Menunggu,
        ];
    }

    /**
     * Indicate that the report is still awaiting handling.
     */
    public function menunggu(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusLaporan::Menunggu,
        ]);
    }

    /**
     * Indicate that the report is being processed.
     */
    public function diproses(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusLaporan::Diproses,
        ]);
    }

    /**
     * Indicate that the report is under repair.
     */
    public function dalamPerbaikan(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusLaporan::DalamPerbaikan,
        ]);
    }

    /**
     * Indicate that the report has been completed.
     */
    public function selesai(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusLaporan::Selesai,
            'selesai_at' => now(),
        ]);
    }
}
