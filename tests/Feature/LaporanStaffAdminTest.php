<?php

namespace Tests\Feature;

use App\Enums\PrioritasLaporan;
use App\Enums\StatusLaporan;
use App\Enums\UserRole;
use App\Models\Fasilitas;
use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LaporanStaffAdminTest extends TestCase
{
    use RefreshDatabase;

    private function prosesPayload(StatusLaporan $status): array
    {
        return [
            'status' => $status->value,
            'isi' => 'Sudah dicek dan ditindaklanjuti oleh tim fasilitas.',
        ];
    }

    private function detailPayload(int $kategoriId, array $overrides = []): array
    {
        return array_merge([
            'judul' => 'Perbaikan lampu koridor lantai dua',
            'deskripsi' => 'Lampu koridor lantai dua sudah diperiksa dan akan diganti hari ini.',
            'lokasi' => 'Gedung B Lantai 2',
            'kategori_id' => $kategoriId,
            'fasilitas_id' => null,
            'prioritas' => PrioritasLaporan::Mendesak->value,
        ], $overrides);
    }

    public function test_staff_melihat_seluruh_laporan(): void
    {
        $staff = User::factory()->staff()->create();

        Laporan::factory()->create(['judul' => 'Kran Air Bocor Di Koridor']);
        Laporan::factory()->create(['judul' => 'AC Ruang Kelas Rusak']);

        $response = $this->actingAs($staff)->get(route('staff.laporan.index'));

        $response->assertOk();
        $response->assertSee('Kran Air Bocor Di Koridor');
        $response->assertSee('AC Ruang Kelas Rusak');
        $response->assertViewHas('laporans', fn ($paginator) => $paginator->total() === 2);
    }

    public function test_staff_memfilter_daftar_laporan_berdasarkan_status(): void
    {
        $staff = User::factory()->staff()->create();

        Laporan::factory()->create([
            'judul' => 'Kran Air Bocor Di Koridor',
            'status' => StatusLaporan::Menunggu,
        ]);
        Laporan::factory()->create([
            'judul' => 'AC Ruang Kelas Rusak',
            'status' => StatusLaporan::Selesai,
        ]);

        $response = $this->actingAs($staff)->get(
            route('staff.laporan.index', ['status' => StatusLaporan::Selesai->value]),
        );

        $response->assertOk();
        $response->assertSee('AC Ruang Kelas Rusak');
        $response->assertDontSee('Kran Air Bocor Di Koridor');
        $response->assertViewHas('laporans', fn ($paginator) => $paginator->total() === 1);
    }

    public function test_staff_mencari_laporan_berdasarkan_pelapor_dan_kode(): void
    {
        $staff = User::factory()->staff()->create();
        $pelapor = User::factory()->mahasiswa()->create(['name' => 'Siti Pelapor']);

        Laporan::factory()->create([
            'user_id' => $pelapor->id,
            'kode' => 'LAP-2026-0101',
            'judul' => 'Lampu Koridor Mati',
        ]);
        Laporan::factory()->create([
            'kode' => 'LAP-2026-0102',
            'judul' => 'Pintu Toilet Rusak',
        ]);

        $berdasarkanPelapor = $this->actingAs($staff)->get(
            route('staff.laporan.index', ['cari' => 'Siti']),
        );
        $berdasarkanKode = $this->actingAs($staff)->get(
            route('staff.laporan.index', ['cari' => 'LAP-2026-0102']),
        );

        $berdasarkanPelapor->assertOk();
        $berdasarkanPelapor->assertSee('Lampu Koridor Mati');
        $berdasarkanPelapor->assertDontSee('Pintu Toilet Rusak');
        $berdasarkanPelapor->assertViewHas('laporans', fn ($paginator) => $paginator->total() === 1);

        $berdasarkanKode->assertOk();
        $berdasarkanKode->assertSee('Pintu Toilet Rusak');
        $berdasarkanKode->assertDontSee('Lampu Koridor Mati');
        $berdasarkanKode->assertViewHas('laporans', fn ($paginator) => $paginator->total() === 1);
    }

    public function test_staff_membuka_detail_laporan(): void
    {
        $staff = User::factory()->staff()->create();
        $laporan = Laporan::factory()->create(['judul' => 'Pintu Toilet Rusak']);

        $response = $this->actingAs($staff)->get(route('staff.laporan.show', $laporan));

        $response->assertOk();
        $response->assertSee($laporan->kode);
        $response->assertSee('Pintu Toilet Rusak');
        $response->assertSee('Riwayat Tanggapan');
        $response->assertViewHas('laporan', fn ($detail) => $detail->id === $laporan->id);
    }

    public function test_staff_dapat_mengklaim_laporan_yang_belum_ditangani(): void
    {
        $staff = User::factory()->staff()->create(['name' => 'Staff Satu']);
        $laporan = Laporan::factory()->create(['ditangani_oleh' => null]);

        $response = $this->actingAs($staff)
            ->from(route('staff.laporan.show', $laporan))
            ->patch(route('staff.laporan.klaim', $laporan));

        $response->assertRedirect(route('staff.laporan.show', $laporan));
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('laporans', [
            'id' => $laporan->id,
            'ditangani_oleh' => $staff->id,
        ]);
    }

    public function test_staff_tidak_dapat_mengklaim_laporan_yang_sudah_ditangani(): void
    {
        $staff = User::factory()->staff()->create();
        $staffLain = User::factory()->staff()->create();
        $laporan = Laporan::factory()->create([
            'ditangani_oleh' => $staffLain->id,
            'status' => StatusLaporan::Diproses,
        ]);

        $this->actingAs($staff)
            ->patch(route('staff.laporan.klaim', $laporan))
            ->assertForbidden();

        $this->assertDatabaseHas('laporans', [
            'id' => $laporan->id,
            'ditangani_oleh' => $staffLain->id,
        ]);
    }

    public function test_staff_memproses_laporan_yang_ditangani_dengan_transisi_status_bebas_dan_selesai_at(): void
    {
        $this->travelTo('2026-11-23 09:00:00');

        $staff = User::factory()->staff()->create();
        $laporan = Laporan::factory()->create([
            'ditangani_oleh' => $staff->id,
            'status' => StatusLaporan::Menunggu,
        ]);

        $selesai = $this->actingAs($staff)
            ->from(route('staff.laporan.show', $laporan))
            ->patch(route('staff.laporan.proses', $laporan), $this->prosesPayload(StatusLaporan::Selesai));

        $selesai->assertRedirect(route('staff.laporan.show', $laporan));
        $selesai->assertSessionHas('status');

        $laporan->refresh();

        $this->assertSame(StatusLaporan::Selesai, $laporan->status);
        $this->assertSame('2026-11-23 09:00:00', $laporan->selesai_at->toDateTimeString());
        $this->assertDatabaseHas('tanggapans', [
            'laporan_id' => $laporan->id,
            'user_id' => $staff->id,
            'isi' => 'Sudah dicek dan ditindaklanjuti oleh tim fasilitas.',
            'status_sebelum' => StatusLaporan::Menunggu->value,
            'status_sesudah' => StatusLaporan::Selesai->value,
        ]);

        $diproses = $this->actingAs($staff)
            ->from(route('staff.laporan.show', $laporan))
            ->patch(route('staff.laporan.proses', $laporan), $this->prosesPayload(StatusLaporan::Diproses));

        $diproses->assertRedirect(route('staff.laporan.show', $laporan));

        $laporan->refresh();

        $this->assertSame(StatusLaporan::Diproses, $laporan->status);
        $this->assertNull($laporan->selesai_at);
        $this->assertDatabaseHas('tanggapans', [
            'laporan_id' => $laporan->id,
            'user_id' => $staff->id,
            'status_sebelum' => StatusLaporan::Selesai->value,
            'status_sesudah' => StatusLaporan::Diproses->value,
        ]);
    }

    public function test_staff_tidak_dapat_memproses_laporan_yang_tidak_ditangani(): void
    {
        $staff = User::factory()->staff()->create();
        $laporan = Laporan::factory()->create([
            'ditangani_oleh' => null,
            'status' => StatusLaporan::Menunggu,
        ]);

        $this->actingAs($staff)
            ->patch(route('staff.laporan.proses', $laporan), $this->prosesPayload(StatusLaporan::Selesai))
            ->assertForbidden();

        $this->assertDatabaseMissing('tanggapans', ['laporan_id' => $laporan->id]);
        $laporan->refresh();
        $this->assertSame(StatusLaporan::Menunggu, $laporan->status);
        $this->assertNull($laporan->selesai_at);
    }

    public function test_staff_tidak_dapat_menugaskan_memulihkan_atau_mengarsipkan_laporan(): void
    {
        $staff = User::factory()->staff()->create();
        $laporan = Laporan::factory()->create(['ditangani_oleh' => null]);

        $this->actingAs($staff);

        $this->patch(route('admin.laporan.penangani', $laporan), ['penangani_id' => $staff->id])
            ->assertRedirect(route('staff.dashboard'));
        $this->patch(route('admin.laporan.restore', $laporan))
            ->assertRedirect(route('staff.dashboard'));
        $this->delete(route('admin.laporan.destroy', $laporan))
            ->assertRedirect(route('staff.dashboard'));

        $this->assertDatabaseHas('laporans', [
            'id' => $laporan->id,
            'ditangani_oleh' => null,
            'deleted_at' => null,
        ]);
    }

    public function test_admin_melihat_dan_memfilter_daftar_laporan(): void
    {
        $admin = User::factory()->admin()->create();

        Laporan::factory()->create([
            'judul' => 'AC Ruang Kelas Rusak',
            'status' => StatusLaporan::Diproses,
            'prioritas' => PrioritasLaporan::Tinggi,
        ]);
        Laporan::factory()->create([
            'judul' => 'Kran Air Bocor Di Koridor',
            'status' => StatusLaporan::Menunggu,
            'prioritas' => PrioritasLaporan::Rendah,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.laporan.index', [
            'status' => StatusLaporan::Diproses->value,
            'prioritas' => PrioritasLaporan::Tinggi->value,
        ]));

        $response->assertOk();
        $response->assertSee('AC Ruang Kelas Rusak');
        $response->assertDontSee('Kran Air Bocor Di Koridor');
        $response->assertViewHas('laporans', fn ($paginator) => $paginator->total() === 1);
    }

    public function test_admin_mencari_laporan_berdasarkan_pelapor_dan_kode(): void
    {
        $admin = User::factory()->admin()->create();
        $pelapor = User::factory()->mahasiswa()->create(['name' => 'Siti Pelapor']);

        Laporan::factory()->create([
            'user_id' => $pelapor->id,
            'kode' => 'LAP-2026-0101',
            'judul' => 'Lampu Koridor Mati',
        ]);
        Laporan::factory()->create([
            'kode' => 'LAP-2026-0102',
            'judul' => 'Pintu Toilet Rusak',
        ]);

        $berdasarkanPelapor = $this->actingAs($admin)->get(
            route('admin.laporan.index', ['cari' => 'Siti']),
        );
        $berdasarkanKode = $this->actingAs($admin)->get(
            route('admin.laporan.index', ['cari' => 'LAP-2026-0102']),
        );

        $berdasarkanPelapor->assertOk();
        $berdasarkanPelapor->assertSee('Lampu Koridor Mati');
        $berdasarkanPelapor->assertDontSee('Pintu Toilet Rusak');
        $berdasarkanPelapor->assertViewHas('laporans', fn ($paginator) => $paginator->total() === 1);

        $berdasarkanKode->assertOk();
        $berdasarkanKode->assertSee('Pintu Toilet Rusak');
        $berdasarkanKode->assertDontSee('Lampu Koridor Mati');
        $berdasarkanKode->assertViewHas('laporans', fn ($paginator) => $paginator->total() === 1);
    }

    public function test_admin_membuka_detail_laporan(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create(['name' => 'Staff Satu']);
        $laporan = Laporan::factory()->create([
            'judul' => 'AC Ruang Kelas Rusak',
            'ditangani_oleh' => $staff->id,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.laporan.show', $laporan));

        $response->assertOk();
        $response->assertSee($laporan->kode);
        $response->assertSee('AC Ruang Kelas Rusak');
        $response->assertSee('Staff Satu');
        $response->assertSee('Edit Detail');
        $response->assertSee('Arsipkan Laporan');
    }

    public function test_admin_membuka_formulir_edit_detail_laporan(): void
    {
        $admin = User::factory()->admin()->create();
        $laporan = Laporan::factory()->create(['judul' => 'AC Ruang Kelas Rusak']);

        $response = $this->actingAs($admin)->get(route('admin.laporan.edit', $laporan));

        $response->assertOk();
        $response->assertSee('Edit Detail Laporan');
        $response->assertSee('AC Ruang Kelas Rusak');
        $response->assertViewHas('prioritasTersedia');
    }

    public function test_admin_dapat_menugaskan_dan_melepas_staff_penangani(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create(['name' => 'Staff Satu']);
        $laporan = Laporan::factory()->create(['ditangani_oleh' => null]);

        $ditugaskan = $this->actingAs($admin)
            ->from(route('admin.laporan.show', $laporan))
            ->patch(route('admin.laporan.penangani', $laporan), ['penangani_id' => $staff->id]);

        $ditugaskan->assertRedirect(route('admin.laporan.show', $laporan));
        $ditugaskan->assertSessionHas('status');
        $this->assertDatabaseHas('laporans', [
            'id' => $laporan->id,
            'ditangani_oleh' => $staff->id,
        ]);

        $dilepas = $this->actingAs($admin)
            ->patch(route('admin.laporan.penangani', $laporan), ['penangani_id' => '']);

        $dilepas->assertSessionHas('status');
        $this->assertDatabaseHas('laporans', [
            'id' => $laporan->id,
            'ditangani_oleh' => null,
        ]);
    }

    public function test_admin_dapat_memperbarui_detail_laporan(): void
    {
        $admin = User::factory()->admin()->create();
        $kategori = Kategori::factory()->create();
        $fasilitas = Fasilitas::factory()->create();
        $laporan = Laporan::factory()->create(['status' => StatusLaporan::Menunggu]);
        $payload = $this->detailPayload($kategori->id, ['fasilitas_id' => $fasilitas->id]);

        $response = $this->actingAs($admin)->put(route('admin.laporan.update', $laporan), $payload);

        $response->assertRedirect(route('admin.laporan.show', $laporan));
        $response->assertSessionHas('status');

        $laporan->refresh();

        $this->assertSame('Perbaikan lampu koridor lantai dua', $laporan->judul);
        $this->assertSame('Lampu koridor lantai dua sudah diperiksa dan akan diganti hari ini.', $laporan->deskripsi);
        $this->assertSame('Gedung B Lantai 2', $laporan->lokasi);
        $this->assertSame($kategori->id, $laporan->kategori_id);
        $this->assertSame($fasilitas->id, $laporan->fasilitas_id);
        $this->assertSame(PrioritasLaporan::Mendesak, $laporan->prioritas);
        $this->assertSame(StatusLaporan::Menunggu, $laporan->status);
    }

    public function test_admin_dapat_memproses_laporan_dan_mengisi_selesai_at(): void
    {
        $this->travelTo('2026-11-23 10:30:00');

        $admin = User::factory()->admin()->create();
        $laporan = Laporan::factory()->create(['status' => StatusLaporan::Menunggu]);

        $response = $this->actingAs($admin)
            ->from(route('admin.laporan.show', $laporan))
            ->patch(route('admin.laporan.proses', $laporan), $this->prosesPayload(StatusLaporan::Selesai));

        $response->assertRedirect(route('admin.laporan.show', $laporan));
        $response->assertSessionHas('status');

        $laporan->refresh();

        $this->assertSame(StatusLaporan::Selesai, $laporan->status);
        $this->assertSame('2026-11-23 10:30:00', $laporan->selesai_at->toDateTimeString());
        $this->assertDatabaseHas('tanggapans', [
            'laporan_id' => $laporan->id,
            'user_id' => $admin->id,
            'status_sebelum' => StatusLaporan::Menunggu->value,
            'status_sesudah' => StatusLaporan::Selesai->value,
        ]);
    }

    public function test_admin_mengarsipkan_laporan_melalui_destroy(): void
    {
        $admin = User::factory()->admin()->create();
        $laporan = Laporan::factory()->create(['judul' => 'AC Ruang Kelas Rusak']);

        $response = $this->actingAs($admin)->delete(route('admin.laporan.destroy', $laporan));

        $response->assertRedirect(route('admin.laporan.index'));
        $response->assertSessionHas('status');
        $this->assertSoftDeleted('laporans', ['id' => $laporan->id]);

        $this->actingAs($admin)->get(route('admin.laporan.index'))
            ->assertDontSee('AC Ruang Kelas Rusak');

        $arsip = $this->actingAs($admin)->get(route('admin.laporan.arsip'));
        $arsip->assertSee('AC Ruang Kelas Rusak');
        $arsip->assertViewHas('laporans', fn ($paginator) => $paginator->total() === 1);
    }

    public function test_admin_memulihkan_laporan_dari_arsip(): void
    {
        $admin = User::factory()->admin()->create();
        $laporan = Laporan::factory()->create(['judul' => 'AC Ruang Kelas Rusak']);

        $this->actingAs($admin)->delete(route('admin.laporan.destroy', $laporan));

        $response = $this->actingAs($admin)->patch(route('admin.laporan.restore', $laporan));

        $response->assertRedirect(route('admin.laporan.arsip'));
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('laporans', [
            'id' => $laporan->id,
            'deleted_at' => null,
        ]);

        $this->actingAs($admin)->get(route('admin.laporan.index'))
            ->assertSee('AC Ruang Kelas Rusak');

        $arsip = $this->actingAs($admin)->get(route('admin.laporan.arsip'));
        $arsip->assertDontSee('AC Ruang Kelas Rusak');
        $arsip->assertViewHas('laporans', fn ($paginator) => $paginator->total() === 0);
    }

    public function test_admin_menolak_penangani_yang_bukan_staff(): void
    {
        $admin = User::factory()->admin()->create();
        $mahasiswa = User::factory()->mahasiswa()->create();
        $laporan = Laporan::factory()->create(['ditangani_oleh' => null]);

        $response = $this->actingAs($admin)->patch(
            route('admin.laporan.penangani', $laporan),
            ['penangani_id' => $mahasiswa->id],
        );
        $response->assertSessionHasErrors([
            'penangani_id' => 'Penangani harus berupa akun staff yang tersedia.',
        ]);

        $this->actingAs($admin)->patch(
            route('admin.laporan.penangani', $laporan),
            ['penangani_id' => 999999],
        )->assertSessionHasErrors('penangani_id');

        $this->assertDatabaseHas('laporans', [
            'id' => $laporan->id,
            'ditangani_oleh' => null,
        ]);
    }

    public function test_admin_menolak_status_proses_yang_tidak_valid(): void
    {
        $admin = User::factory()->admin()->create();
        $laporan = Laporan::factory()->create(['status' => StatusLaporan::Menunggu]);

        $this->actingAs($admin)->patch(route('admin.laporan.proses', $laporan), [
            'status' => 'tidak-ada',
            'isi' => 'Sudah dicek dan ditindaklanjuti oleh tim fasilitas.',
        ])->assertSessionHasErrors([
            'status' => 'Status laporan tidak valid.',
        ]);

        $this->assertDatabaseHas('laporans', [
            'id' => $laporan->id,
            'status' => StatusLaporan::Menunggu->value,
        ]);
        $this->assertDatabaseCount('tanggapans', 0);
    }

    public function test_admin_menolak_detail_laporan_yang_tidak_valid(): void
    {
        $admin = User::factory()->admin()->create();
        $kategori = Kategori::factory()->create();
        $laporan = Laporan::factory()->create([
            'judul' => 'AC Ruang Kelas Rusak',
            'status' => StatusLaporan::Menunggu,
        ]);

        $payload = $this->detailPayload($kategori->id, [
            'judul' => 'Pendek',
            'deskripsi' => 'Terlalu singkat',
            'kategori_id' => 999999,
            'fasilitas_id' => 999999,
            'prioritas' => 'tidak-ada',
        ]);

        $this->actingAs($admin)->put(route('admin.laporan.update', $laporan), $payload)
            ->assertSessionHasErrors([
                'judul' => 'Judul minimal 10 karakter.',
                'deskripsi' => 'Deskripsi minimal 20 karakter.',
                'kategori_id' => 'Kategori yang dipilih tidak tersedia.',
                'fasilitas_id' => 'Fasilitas yang dipilih tidak tersedia.',
                'prioritas' => 'Prioritas yang dipilih tidak valid.',
            ]);

        $this->assertDatabaseHas('laporans', [
            'id' => $laporan->id,
            'judul' => 'AC Ruang Kelas Rusak',
            'status' => StatusLaporan::Menunggu->value,
        ]);
    }

    public function test_tamu_diarahkan_ke_login_untuk_rute_laporan_admin(): void
    {
        $laporan = Laporan::factory()->create();

        $this->get(route('admin.laporan.index'))->assertRedirect(route('login'));
        $this->get(route('admin.laporan.arsip'))->assertRedirect(route('login'));
        $this->get(route('admin.laporan.show', $laporan))->assertRedirect(route('login'));
        $this->get(route('admin.laporan.edit', $laporan))->assertRedirect(route('login'));
        $this->patch(route('admin.laporan.penangani', $laporan))->assertRedirect(route('login'));
        $this->patch(route('admin.laporan.proses', $laporan))->assertRedirect(route('login'));
        $this->put(route('admin.laporan.update', $laporan))->assertRedirect(route('login'));
        $this->delete(route('admin.laporan.destroy', $laporan))->assertRedirect(route('login'));
        $this->patch(route('admin.laporan.restore', $laporan))->assertRedirect(route('login'));
    }

    public static function peranBukanAdmin(): array
    {
        return [
            'staff' => [UserRole::Staff->value],
            'mahasiswa' => [UserRole::Mahasiswa->value],
        ];
    }

    #[DataProvider('peranBukanAdmin')]
    public function test_role_bukan_admin_diarahkan_ke_dashboard_miliknya(string $role): void
    {
        $user = User::factory()->create(['role' => $role]);
        $laporan = Laporan::factory()->create();
        $dashboard = route($user->userRole()->dashboardRoute());

        $this->actingAs($user);

        $this->get(route('admin.laporan.index'))->assertRedirect($dashboard);
        $this->get(route('admin.laporan.arsip'))->assertRedirect($dashboard);
        $this->get(route('admin.laporan.show', $laporan))->assertRedirect($dashboard);
        $this->get(route('admin.laporan.edit', $laporan))->assertRedirect($dashboard);
        $this->patch(route('admin.laporan.penangani', $laporan))->assertRedirect($dashboard);
        $this->patch(route('admin.laporan.proses', $laporan))->assertRedirect($dashboard);
        $this->put(route('admin.laporan.update', $laporan))->assertRedirect($dashboard);
        $this->delete(route('admin.laporan.destroy', $laporan))->assertRedirect($dashboard);
        $this->patch(route('admin.laporan.restore', $laporan))->assertRedirect($dashboard);
    }

    #[DataProvider('peranBukanAdmin')]
    public function test_permintaan_json_dari_role_bukan_admin_menghasilkan_403(string $role): void
    {
        $user = User::factory()->create(['role' => $role]);
        $laporan = Laporan::factory()->create();

        $this->actingAs($user);

        $this->getJson(route('admin.laporan.index'))->assertForbidden();
        $this->patchJson(route('admin.laporan.penangani', $laporan), [
            'penangani_id' => $user->id,
        ])->assertForbidden();
    }

    public function test_tamu_diarahkan_ke_login_untuk_rute_laporan_staff(): void
    {
        $laporan = Laporan::factory()->create();

        $this->get(route('staff.laporan.index'))->assertRedirect(route('login'));
        $this->get(route('staff.laporan.show', $laporan))->assertRedirect(route('login'));
        $this->patch(route('staff.laporan.klaim', $laporan))->assertRedirect(route('login'));
        $this->patch(route('staff.laporan.proses', $laporan))->assertRedirect(route('login'));
    }

    public static function peranBukanStaff(): array
    {
        return [
            'admin' => [UserRole::Admin->value],
            'mahasiswa' => [UserRole::Mahasiswa->value],
        ];
    }

    #[DataProvider('peranBukanStaff')]
    public function test_role_bukan_staff_diarahkan_ke_dashboard_miliknya(string $role): void
    {
        $user = User::factory()->create(['role' => $role]);
        $laporan = Laporan::factory()->create();
        $dashboard = route($user->userRole()->dashboardRoute());

        $this->actingAs($user);

        $this->get(route('staff.laporan.index'))->assertRedirect($dashboard);
        $this->get(route('staff.laporan.show', $laporan))->assertRedirect($dashboard);
        $this->patch(route('staff.laporan.klaim', $laporan))->assertRedirect($dashboard);
        $this->patch(route('staff.laporan.proses', $laporan))->assertRedirect($dashboard);
    }

    #[DataProvider('peranBukanStaff')]
    public function test_permintaan_json_dari_role_bukan_staff_menghasilkan_403(string $role): void
    {
        $user = User::factory()->create(['role' => $role]);
        $laporan = Laporan::factory()->create();

        $this->actingAs($user)
            ->getJson(route('staff.laporan.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->getJson(route('staff.laporan.show', $laporan))
            ->assertForbidden();
    }
}
