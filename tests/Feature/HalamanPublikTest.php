<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HalamanPublikTest extends TestCase
{
    use RefreshDatabase;

    public function test_beranda_tampil_untuk_tamu(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('SIPELITA');
        $response->assertSee(route('login'));
        $response->assertSee(route('register'));
    }

    public function test_halaman_autentikasi_tampil_untuk_tamu(): void
    {
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertOk();
        $this->get(route('password.request'))->assertOk();
    }

    public function test_dashboard_mengarahkan_tamu_ke_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_halaman_dashboard_role_mengarahkan_tamu_ke_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('staff.dashboard'))->assertRedirect(route('login'));
        $this->get(route('mahasiswa.dashboard'))->assertRedirect(route('login'));
    }

    public function test_pendaftaran_menugaskan_peran_mahasiswa(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Wulan Pratama',
            'email' => 'wulan@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));

        $user = User::where('email', 'wulan@example.com')->firstOrFail();

        $this->assertTrue($user->hasRole(UserRole::Mahasiswa));
        $this->assertAuthenticatedAs($user);
    }
}
