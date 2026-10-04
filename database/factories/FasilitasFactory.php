<?php

namespace Database\Factories;

use App\Models\Fasilitas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fasilitas>
 */
class FasilitasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->randomElement([
                'Ruang Kelas',
                'Laboratorium Komputer',
                'Perpustakaan',
                'Ruang Auditorium',
                'Kantin Kampus',
                'Toilet Umum',
            ]),
            'lokasi' => 'Gedung '.fake()->randomElement(['A', 'B', 'C', 'D']),
            'gedung' => 'Gedung '.fake()->randomElement(['A', 'B', 'C', 'D']),
            'lantai' => (string) fake()->numberBetween(1, 4),
        ];
    }
}
