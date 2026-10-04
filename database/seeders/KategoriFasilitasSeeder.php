<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriFasilitasSeeder extends Seeder
{
    /**
     * Isi master data kategori fasilitas.
     */
    public function run(): void
    {
        foreach (config('sipelita.kategori') as $nama) {
            Kategori::query()->firstOrCreate(['nama' => $nama]);
        }

        $this->command?->info('Kategori fasilitas awal berhasil dibuat.');
    }
}
