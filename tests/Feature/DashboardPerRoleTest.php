<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardPerRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dapat_membuka_dashboard_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    }

    public function test_staff_dapat_membuka_dashboard_staff(): void
    {
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)->get(route('staff.dashboard'))->assertOk();
    }

    public function test_mahasiswa_dapat_membuka_dashboard_mahasiswa(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();

        $this->actingAs($mahasiswa)->get(route('mahasiswa.dashboard'))->assertOk();
    }

    public function test_route_dashboard_mengalihkan_ke_dashboard_peran(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));

        $staff = User::factory()->staff()->create();
        $this->actingAs($staff)->get(route('dashboard'))->assertRedirect(route('staff.dashboard'));

        $mahasiswa = User::factory()->mahasiswa()->create();
        $this->actingAs($mahasiswa)->get(route('dashboard'))->assertRedirect(route('mahasiswa.dashboard'));
    }

    public function test_beranda_mengalihkan_pengguna_terautentikasi_ke_dashboard(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();

        $this->actingAs($mahasiswa)->get(route('home'))->assertRedirect(route('mahasiswa.dashboard'));
    }

    public function test_peran_tidak_sesuai_diarahkan_ke_dashboard_miliknya(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();

        $this->actingAs($mahasiswa)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('mahasiswa.dashboard'));

        $this->actingAs($mahasiswa)
            ->get(route('staff.dashboard'))
            ->assertRedirect(route('mahasiswa.dashboard'));
    }

    public function test_peran_tidak_sesuai_mengembalikan_403_untuk_permintaan_json(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();

        $this->actingAs($mahasiswa)
            ->getJson(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_dan_staff_saling_ditolak(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();

        $this->actingAs($admin)->get(route('staff.dashboard'))->assertRedirect(route('admin.dashboard'));
        $this->actingAs($staff)->get(route('admin.dashboard'))->assertRedirect(route('staff.dashboard'));
    }

    public function test_admin_dapat_membuka_halaman_profil(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('profile.edit'))->assertOk();
    }
}
