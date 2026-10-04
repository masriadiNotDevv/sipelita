<?php

namespace Database\Factories;

use App\Models\Laporan;
use App\Models\Tanggapan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tanggapan>
 */
class TanggapanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'laporan_id' => Laporan::factory(),
            'user_id' => User::factory()->staff(),
            'isi' => fake()->paragraph(),
        ];
    }
}
