<?php

namespace Tests\Feature;

use App\Enums\StatusLaporan;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class LaporanPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_view_any_diizinkan_untuk_semua_peran(): void
    {
        $pengguna = [
            User::factory()->admin()->create(),
            User::factory()->staff()->create(),
            User::factory()->mahasiswa()->create(),
            User::factory()->mahasiswa()->create(),
        ];

        foreach ($pengguna as $user) {
            $this->assertTrue(Gate::forUser($user)->allows('viewAny', Laporan::class));
        }
    }

    public function test_create_hanya_diizinkan_untuk_mahasiswa(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $mahasiswa = User::factory()->mahasiswa()->create();

        $this->assertTrue(Gate::forUser($admin)->denies('create', Laporan::class));
        $this->assertTrue(Gate::forUser($staff)->denies('create', Laporan::class));
        $this->assertTrue(Gate::forUser($mahasiswa)->allows('create', Laporan::class));
    }

    public function test_view_diizinkan_untuk_admin_staff_dan_pemilik_saja(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $pemilik = User::factory()->mahasiswa()->create();
        $bukanPemilik = User::factory()->mahasiswa()->create();
        $laporan = Laporan::factory()->create(['user_id' => $pemilik->id]);

        $this->assertTrue(Gate::forUser($admin)->allows('view', $laporan));
        $this->assertTrue(Gate::forUser($staff)->allows('view', $laporan));
        $this->assertTrue(Gate::forUser($pemilik)->allows('view', $laporan));
        $this->assertTrue(Gate::forUser($bukanPemilik)->denies('view', $laporan));
    }

    public function test_update_diizinkan_untuk_admin_dan_pemilik_pada_status_menunggu_atau_diproses(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $pemilik = User::factory()->mahasiswa()->create();
        $bukanPemilik = User::factory()->mahasiswa()->create();

        foreach ([StatusLaporan::Menunggu, StatusLaporan::Diproses] as $status) {
            $laporan = Laporan::factory()->create([
                'user_id' => $pemilik->id,
                'status' => $status,
            ]);

            $this->assertTrue(Gate::forUser($admin)->allows('update', $laporan));
            $this->assertTrue(Gate::forUser($pemilik)->allows('update', $laporan));
            $this->assertTrue(Gate::forUser($staff)->denies('update', $laporan));
            $this->assertTrue(Gate::forUser($bukanPemilik)->denies('update', $laporan));
        }

        foreach ([StatusLaporan::DalamPerbaikan, StatusLaporan::Selesai, StatusLaporan::Ditolak] as $status) {
            $laporan = Laporan::factory()->create([
                'user_id' => $pemilik->id,
                'status' => $status,
            ]);

            $this->assertTrue(Gate::forUser($admin)->allows('update', $laporan));
            $this->assertTrue(Gate::forUser($pemilik)->denies('update', $laporan));
        }
    }

    public function test_delete_diizinkan_untuk_admin_dan_pemilik_pada_status_menunggu(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $pemilik = User::factory()->mahasiswa()->create();
        $bukanPemilik = User::factory()->mahasiswa()->create();

        $menunggu = Laporan::factory()->create([
            'user_id' => $pemilik->id,
            'status' => StatusLaporan::Menunggu,
        ]);

        $this->assertTrue(Gate::forUser($admin)->allows('delete', $menunggu));
        $this->assertTrue(Gate::forUser($pemilik)->allows('delete', $menunggu));
        $this->assertTrue(Gate::forUser($staff)->denies('delete', $menunggu));
        $this->assertTrue(Gate::forUser($bukanPemilik)->denies('delete', $menunggu));

        foreach ([StatusLaporan::Diproses, StatusLaporan::DalamPerbaikan, StatusLaporan::Selesai, StatusLaporan::Ditolak] as $status) {
            $laporan = Laporan::factory()->create([
                'user_id' => $pemilik->id,
                'status' => $status,
            ]);

            $this->assertTrue(Gate::forUser($admin)->allows('delete', $laporan));
            $this->assertTrue(Gate::forUser($pemilik)->denies('delete', $laporan));
        }
    }

    public function test_restore_hanya_diizinkan_untuk_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $pemilik = User::factory()->mahasiswa()->create();
        $bukanPemilik = User::factory()->mahasiswa()->create();
        $laporan = Laporan::factory()->create(['user_id' => $pemilik->id]);

        $this->assertTrue(Gate::forUser($admin)->allows('restore', $laporan));
        $this->assertTrue(Gate::forUser($staff)->denies('restore', $laporan));
        $this->assertTrue(Gate::forUser($pemilik)->denies('restore', $laporan));
        $this->assertTrue(Gate::forUser($bukanPemilik)->denies('restore', $laporan));
    }

    public function test_klaim_hanya_diizinkan_untuk_staff_pada_laporan_yang_tersedia(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $staffLain = User::factory()->staff()->create();
        $pemilik = User::factory()->mahasiswa()->create();
        $bukanPemilik = User::factory()->mahasiswa()->create();

        $laporan = Laporan::factory()->create([
            'user_id' => $pemilik->id,
            'ditangani_oleh' => null,
            'status' => StatusLaporan::Menunggu,
        ]);

        $this->assertTrue(Gate::forUser($staff)->allows('klaim', $laporan));
        $this->assertTrue(Gate::forUser($admin)->denies('klaim', $laporan));
        $this->assertTrue(Gate::forUser($pemilik)->denies('klaim', $laporan));
        $this->assertTrue(Gate::forUser($bukanPemilik)->denies('klaim', $laporan));

        foreach ([StatusLaporan::Diproses, StatusLaporan::DalamPerbaikan] as $status) {
            $tersedia = Laporan::factory()->create([
                'ditangani_oleh' => null,
                'status' => $status,
            ]);

            $this->assertTrue(Gate::forUser($staff)->allows('klaim', $tersedia));
        }

        foreach ([StatusLaporan::Selesai, StatusLaporan::Ditolak] as $status) {
            $tidakTersedia = Laporan::factory()->create([
                'ditangani_oleh' => null,
                'status' => $status,
            ]);

            $this->assertTrue(Gate::forUser($staff)->denies('klaim', $tidakTersedia));
        }

        $laporan->forceFill(['ditangani_oleh' => $staffLain->id])->save();
        $this->assertTrue(Gate::forUser($staff)->denies('klaim', $laporan));

        $laporan->forceFill(['ditangani_oleh' => $staff->id])->save();
        $this->assertTrue(Gate::forUser($staff)->denies('klaim', $laporan));
    }

    public function test_tugaskan_hanya_diizinkan_untuk_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $pemilik = User::factory()->mahasiswa()->create();
        $bukanPemilik = User::factory()->mahasiswa()->create();
        $laporan = Laporan::factory()->create(['user_id' => $pemilik->id]);

        $this->assertTrue(Gate::forUser($admin)->allows('tugaskan', $laporan));
        $this->assertTrue(Gate::forUser($staff)->denies('tugaskan', $laporan));
        $this->assertTrue(Gate::forUser($pemilik)->denies('tugaskan', $laporan));
        $this->assertTrue(Gate::forUser($bukanPemilik)->denies('tugaskan', $laporan));
    }

    public function test_proses_diizinkan_untuk_admin_dan_staff_yang_ditugaskan(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $staffLain = User::factory()->staff()->create();
        $pemilik = User::factory()->mahasiswa()->create();
        $bukanPemilik = User::factory()->mahasiswa()->create();
        $laporan = Laporan::factory()->create([
            'user_id' => $pemilik->id,
            'ditangani_oleh' => $staff->id,
        ]);

        $this->assertTrue(Gate::forUser($admin)->allows('proses', $laporan));
        $this->assertTrue(Gate::forUser($staff)->allows('proses', $laporan));
        $this->assertTrue(Gate::forUser($staffLain)->denies('proses', $laporan));
        $this->assertTrue(Gate::forUser($pemilik)->denies('proses', $laporan));
        $this->assertTrue(Gate::forUser($bukanPemilik)->denies('proses', $laporan));

        $laporan->forceFill(['ditangani_oleh' => null])->save();

        $this->assertTrue(Gate::forUser($admin)->allows('proses', $laporan));
        $this->assertTrue(Gate::forUser($staff)->denies('proses', $laporan));
    }
}
