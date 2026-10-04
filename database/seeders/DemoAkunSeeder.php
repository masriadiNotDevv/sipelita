<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoAkunSeeder extends Seeder
{
    /**
     * Buat akun admin, staff, dan mahasiswa untuk pengembangan.
     *
     * Seeder bersifat idempoten: akun yang sudah ada diperbarui, bukan diduplikasi.
     */
    public function run(): void
    {
        if (! config('sipelita.demo.enabled')) {
            $this->command?->warn('Akun demo dilewati karena SIPELITA_DEMO_ENABLED=false.');

            return;
        }

        foreach ($this->akun() as $akun) {
            User::query()->updateOrCreate(
                ['email' => $akun['email']],
                [
                    'name' => $akun['name'],
                    'password' => Hash::make($akun['password']),
                    'email_verified_at' => now(),
                    'role' => $akun['role'],
                ],
            );
        }

        $this->command?->info('Akun demo admin, staff, dan mahasiswa siap digunakan.');
    }

    /**
     * Daftar akun demo beserta perannya.
     *
     * @return array<int, array{name: string, email: string, password: string, role: UserRole}>
     */
    private function akun(): array
    {
        return [
            [
                'name' => config('sipelita.demo.admin.name'),
                'email' => config('sipelita.demo.admin.email'),
                'password' => config('sipelita.demo.admin.password'),
                'role' => UserRole::Admin,
            ],
            [
                'name' => config('sipelita.demo.staff.name'),
                'email' => config('sipelita.demo.staff.email'),
                'password' => config('sipelita.demo.staff.password'),
                'role' => UserRole::Staff,
            ],
            [
                'name' => config('sipelita.demo.mahasiswa.name'),
                'email' => config('sipelita.demo.mahasiswa.email'),
                'password' => config('sipelita.demo.mahasiswa.password'),
                'role' => UserRole::Mahasiswa,
            ],
        ];
    }
}
