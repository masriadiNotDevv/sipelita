<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StaffControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_tamu_diarahkan_ke_halaman_login(): void
    {
        $this->get(route('admin.staff.index'))->assertRedirect(route('login'));
        $this->get(route('admin.staff.create'))->assertRedirect(route('login'));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function roleBukanAdmin(): array
    {
        return [
            'staff' => [UserRole::Staff->value],
            'mahasiswa' => [UserRole::Mahasiswa->value],
        ];
    }

    #[DataProvider('roleBukanAdmin')]
    public function test_role_bukan_admin_diarahkan_ke_dashboard_miliknya(string $role): void
    {
        $user = User::factory()->create(['role' => $role]);

        $this->actingAs($user)
            ->get(route('admin.staff.index'))
            ->assertRedirect(route($user->userRole()->dashboardRoute()));

        $this->actingAs($user)
            ->get(route('admin.staff.create'))
            ->assertRedirect(route($user->userRole()->dashboardRoute()));
    }

    public function test_admin_melihat_daftar_admin_dan_staff(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Satu']);
        $staff = User::factory()->staff()->create(['name' => 'Staff Dua']);
        User::factory()->mahasiswa()->create(['name' => 'Mahasiswa Tiga']);

        $response = $this->actingAs($admin)->get(route('admin.staff.index'));

        $response->assertOk();
        $response->assertSee('Admin Satu');
        $response->assertSee('Staff Dua');
        $response->assertDontSee('Mahasiswa Tiga');
    }

    public function test_admin_mencari_akun_ganti_berdasarkan_nama(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->staff()->create(['name' => 'Siti Rahma']);
        User::factory()->staff()->create(['name' => 'Budi Santoso']);

        $response = $this->actingAs($admin)->get(route('admin.staff.index', ['cari' => 'Siti']));

        $response->assertOk();
        $response->assertSee('Siti Rahma');
        $response->assertDontSee('Budi Santoso');
    }

    public function test_admin_menambah_akun_staff_dengan_password_yang_disimpan_sebagai_hash(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.staff.store'), [
            'name' => 'Siti Rahma',
            'email' => 'siti@kampus.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => UserRole::Staff->value,
        ]);

        $response->assertRedirect(route('admin.staff.index'));
        $response->assertSessionHas('status');

        $staff = User::where('email', 'siti@kampus.test')->firstOrFail();

        $this->assertTrue($staff->hasRole(UserRole::Staff));
        $this->assertNotSame('rahasia123', $staff->password);
        $this->assertTrue(Hash::check('rahasia123', $staff->password));
    }

    public function test_permintaan_tanpa_data_tidak_valid_gagal_pada_seluruh_field_wajib(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.staff.store'), []);

        $response->assertSessionHasErrors(['name', 'email', 'password', 'role']);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_email_yang_sudah_digunakan_ditolak(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->staff()->create(['email' => 'staff@sipelita.test']);

        $response = $this->actingAs($admin)->post(route('admin.staff.store'), [
            'name' => 'Duplikat',
            'email' => 'staff@sipelita.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => UserRole::Staff->value,
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_peran_mahasiswa_ditolak_saat_menambah_akun(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.staff.store'), [
            'name' => 'Calon Mahasiswa',
            'email' => 'calon@kampus.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => UserRole::Mahasiswa->value,
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'calon@kampus.test']);
    }

    public function test_konfirmasi_password_yang_tidak_cocok_ditolak(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.staff.store'), [
            'name' => 'Budi Santoso',
            'email' => 'budi@kampus.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'berbeda123',
            'role' => UserRole::Staff->value,
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_admin_memperbarui_data_staff_tanpa_mengubah_password(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create([
            'name' => 'Nama Lama',
            'email' => 'lama@kampus.test',
        ]);
        $passwordAwal = $staff->password;

        $response = $this->actingAs($admin)->patch(route('admin.staff.update', $staff), [
            'name' => 'Nama Baru',
            'email' => 'baru@kampus.test',
            'role' => UserRole::Admin->value,
        ]);

        $response->assertRedirect(route('admin.staff.index'));

        $staff->refresh();

        $this->assertSame('Nama Baru', $staff->name);
        $this->assertSame('baru@kampus.test', $staff->email);
        $this->assertTrue($staff->hasRole(UserRole::Admin));
        $this->assertSame($passwordAwal, $staff->password);
    }

    public function test_password_staff_dapat_diperbarui(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();

        $this->actingAs($admin)->patch(route('admin.staff.update', $staff), [
            'name' => $staff->name,
            'email' => $staff->email,
            'password' => 'passwordbaru',
            'password_confirmation' => 'passwordbaru',
            'role' => UserRole::Staff->value,
        ])->assertRedirect(route('admin.staff.index'));

        $this->assertTrue(Hash::check('passwordbaru', $staff->fresh()->password));
    }

    public function test_admin_dapat_memperbarui_akun_tanpa_mengubah_email(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();

        $response = $this->actingAs($admin)->patch(route('admin.staff.update', $staff), [
            'name' => 'Nama Diperbarui',
            'email' => $staff->email,
            'role' => UserRole::Staff->value,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertSame('Nama Diperbarui', $staff->fresh()->name);
    }

    public function test_admin_tidak_dapat_mengubah_peran_akun_sendiri(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->patch(route('admin.staff.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => UserRole::Staff->value,
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertTrue($admin->fresh()->hasRole(UserRole::Admin));
    }

    public function test_akun_mahasiswa_tidak_dapat_diubah_lewat_halaman_kelola_staff(): void
    {
        $admin = User::factory()->admin()->create();
        $mahasiswa = User::factory()->mahasiswa()->create();

        $this->actingAs($admin)->get(route('admin.staff.edit', $mahasiswa))->assertNotFound();

        $this->actingAs($admin)->patch(route('admin.staff.update', $mahasiswa), [
            'name' => 'Berubah',
            'email' => $mahasiswa->email,
            'role' => UserRole::Staff->value,
        ])->assertNotFound();

        $this->actingAs($admin)->delete(route('admin.staff.destroy', $mahasiswa))->assertNotFound();

        $this->assertDatabaseHas('users', [
            'id' => $mahasiswa->id,
            'role' => UserRole::Mahasiswa->value,
        ]);
    }

    public function test_admin_menghapus_akun_staff(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();

        $response = $this->actingAs($admin)->delete(route('admin.staff.destroy', $staff));

        $response->assertRedirect(route('admin.staff.index'));
        $this->assertDatabaseMissing('users', ['id' => $staff->id]);
    }

    public function test_admin_tidak_dapat_menghapus_akun_sendiri(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->delete(route('admin.staff.destroy', $admin));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
