<?php

namespace Tests\Feature;

use App\Enums\PrioritasLaporan;
use App\Enums\StatusLaporan;
use App\Enums\UserRole;
use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LaporanMahasiswaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Data formulir yang valid untuk pengajuan laporan.
     *
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'judul' => 'Lampu Koridor Lantai 2 Mati',
            'deskripsi' => 'Lampu koridor lantai 2 tidak menyala sejak tiga hari lalu sehingga gelap saat malam.',
            'lokasi' => 'Gedung B Lantai 2 sisi timur',
            'kategori_id' => Kategori::factory()->create()->id,
            'prioritas' => PrioritasLaporan::Tinggi->value,
        ], $overrides);
    }

    public function test_mahasiswa_dapat_membuka_daftar_laporan(): void
    {
        $mahasiswa = User::factory()->create(['role' => UserRole::Mahasiswa]);

        Laporan::factory()->count(3)->create(['user_id' => $mahasiswa->id]);
        Laporan::factory()->create();

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.laporan.index'));

        $response->assertOk();
        $response->assertViewHas('laporans', fn ($paginator) => $paginator->total() === 3);
        $response->assertSee('Laporan Saya');
    }

    public function test_daftar_laporan_hanya_menampilkan_laporan_pemilik(): void
    {
        $mahasiswa = User::factory()->create(['role' => UserRole::Mahasiswa]);
        $lain = User::factory()->create(['role' => UserRole::Mahasiswa]);

        $laporanSaya = Laporan::factory()->create([
            'user_id' => $mahasiswa->id,
            'judul' => 'Pintu Toilet Rusak',
        ]);
        Laporan::factory()->create([
            'user_id' => $lain->id,
            'judul' => 'Air Minum Tidak Mengalir',
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.laporan.index'));

        $response->assertOk();
        $response->assertSee('Pintu Toilet Rusak');
        $response->assertDontSee('Air Minum Tidak Mengalir');
        $response->assertViewHas('laporans', fn ($paginator) => $paginator->total() === 1);
    }

    public function test_mahasiswa_dapat_membuka_formulir_baru(): void
    {
        $mahasiswa = User::factory()->create(['role' => UserRole::Mahasiswa]);

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.laporan.create'));

        $response->assertOk();
        $response->assertViewHas('kategoris');
        $response->assertViewHas('prioritas');
    }

    public function test_mahasiswa_dapat_menyimpan_laporan_baru(): void
    {
        $mahasiswa = User::factory()->create(['role' => UserRole::Mahasiswa]);
        $kategori = Kategori::factory()->create();

        $response = $this->actingAs($mahasiswa)->post(
            route('mahasiswa.laporan.store'),
            $this->validPayload(['kategori_id' => $kategori->id]),
        );

        $laporan = Laporan::sole();

        $response->assertRedirect(route('mahasiswa.laporan.show', $laporan));
        $response->assertSessionHas('status');

        $this->assertSame($mahasiswa->id, $laporan->user_id);
        $this->assertSame($kategori->id, $laporan->kategori_id);
        $this->assertSame(StatusLaporan::Menunggu, $laporan->status);
        $this->assertSame(PrioritasLaporan::Tinggi, $laporan->prioritas);
        $this->assertSame('Lampu Koridor Lantai 2 Mati', $laporan->judul);
    }

    public function test_kode_laporan_terisi_otomatis_dengan_format_tahun_dan_id(): void
    {
        $mahasiswa = User::factory()->create(['role' => UserRole::Mahasiswa]);

        $this->actingAs($mahasiswa)->post(
            route('mahasiswa.laporan.store'),
            $this->validPayload(),
        );

        $laporan = Laporan::sole();

        $this->assertSame(
            sprintf('LAP-%s-%04d', now()->format('Y'), $laporan->id),
            $laporan->kode,
        );
    }

    public function test_bukti_foto_disimpan_ke_disk_public(): void
    {
        Storage::fake('public');

        $mahasiswa = User::factory()->create(['role' => UserRole::Mahasiswa]);
        $gambar = UploadedFile::fake()->image('bukti.jpg', 640, 480);

        $this->actingAs($mahasiswa)->post(
            route('mahasiswa.laporan.store'),
            $this->validPayload(['bukti_foto' => $gambar]),
        );

        $laporan = Laporan::sole();

        $this->assertNotNull($laporan->bukti_foto);
        Storage::disk('public')->assertExists($laporan->bukti_foto);
    }

    public function test_form_mempertahankan_input_saat_validasi_gagal(): void
    {
        $mahasiswa = User::factory()->create(['role' => UserRole::Mahasiswa]);
        $kategori = Kategori::factory()->create();

        $response = $this->from(route('mahasiswa.laporan.create'))->actingAs($mahasiswa)->post(
            route('mahasiswa.laporan.store'),
            $this->validPayload(['kategori_id' => $kategori->id, 'judul' => 'Pendek']),
        );

        $response->assertRedirect(route('mahasiswa.laporan.create'));
        $response->assertSessionHasErrors('judul');
        $this->assertSame(0, Laporan::count());
    }

    public function test_validasi_menolak_kategori_prioritas_dan_foto_yang_tidak_valid(): void
    {
        Storage::fake('public');

        $mahasiswa = User::factory()->create(['role' => UserRole::Mahasiswa]);

        $response = $this->actingAs($mahasiswa)->post(
            route('mahasiswa.laporan.store'),
            [
                'judul' => 'Pendek',
                'deskripsi' => 'Terlalu singkat',
                'lokasi' => '',
                'kategori_id' => 999999,
                'prioritas' => 'tidak-ada',
                'bukti_foto' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
            ],
        );

        $response->assertSessionHasErrors([
            'judul',
            'deskripsi',
            'lokasi',
            'kategori_id',
            'prioritas',
            'bukti_foto',
        ]);
        $this->assertSame(0, Laporan::count());
    }

    public function test_bukti_foto_lebih_dari_dua_megabyte_ditolak(): void
    {
        Storage::fake('public');

        $mahasiswa = User::factory()->create(['role' => UserRole::Mahasiswa]);

        $response = $this->actingAs($mahasiswa)->post(
            route('mahasiswa.laporan.store'),
            $this->validPayload([
                'bukti_foto' => UploadedFile::fake()->image('besar.jpg')->size(3000),
            ]),
        );

        $response->assertSessionHasErrors('bukti_foto');
        $this->assertSame(0, Laporan::count());
    }

    public function test_mahasiswa_dapat_membuka_detail_laporan_miliknya(): void
    {
        $mahasiswa = User::factory()->create(['role' => UserRole::Mahasiswa]);
        $laporan = Laporan::factory()->create([
            'user_id' => $mahasiswa->id,
            'judul' => 'Air Minum Tidak Mengalir',
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.laporan.show', $laporan));

        $response->assertOk();
        $response->assertSee($laporan->kode);
        $response->assertSee('Air Minum Tidak Mengalir');
        $response->assertSee('Progres Penanganan');
    }

    public function test_mahasiswa_tidak_dapat_membuka_laporan_milik_mahasiswa_lain(): void
    {
        $laporan = Laporan::factory()->create();

        $response = $this->actingAs(
            User::factory()->create(['role' => UserRole::Mahasiswa]),
        )->get(route('mahasiswa.laporan.show', $laporan));

        $response->assertForbidden();
    }

    public function test_role_lain_diarahkan_ke_dashboard_mereka(): void
    {
        $laporan = Laporan::factory()->create();

        foreach ([UserRole::Admin, UserRole::Staff] as $role) {
            $user = User::factory()->create(['role' => $role]);
            $dashboard = route($user->userRole()->dashboardRoute());

            $this->actingAs($user)->get(route('mahasiswa.laporan.index'))
                ->assertRedirect($dashboard);
            $this->actingAs($user)->get(route('mahasiswa.laporan.create'))
                ->assertRedirect($dashboard);
            $this->actingAs($user)->post(route('mahasiswa.laporan.store'), $this->validPayload())
                ->assertRedirect($dashboard);
            $this->actingAs($user)->get(route('mahasiswa.laporan.show', $laporan))
                ->assertRedirect($dashboard);
        }

        $this->assertSame(1, Laporan::where('id', $laporan->id)->count());
    }

    public function test_permintaan_json_dari_role_lain_menghasilkan_403(): void
    {
        $laporan = Laporan::factory()->create();

        $this->actingAs(User::factory()->create(['role' => UserRole::Admin]))
            ->getJson(route('mahasiswa.laporan.show', $laporan))
            ->assertForbidden();
    }

    public function test_tamu_diarahkan_ke_halaman_login(): void
    {
        $this->get(route('mahasiswa.laporan.index'))->assertRedirect(route('login'));
        $this->get(route('mahasiswa.laporan.create'))->assertRedirect(route('login'));
    }

    public function test_pencarian_daftar_laporan_memfilter_hasil(): void
    {
        $mahasiswa = User::factory()->create(['role' => UserRole::Mahasiswa]);

        Laporan::factory()->create([
            'user_id' => $mahasiswa->id,
            'judul' => 'Pintu Toilet Rusak',
        ]);
        Laporan::factory()->create([
            'user_id' => $mahasiswa->id,
            'judul' => 'Lampu Terang Mati',
        ]);

        $response = $this->actingAs($mahasiswa)->get(
            route('mahasiswa.laporan.index', ['cari' => 'Pintu']),
        );

        $response->assertOk();
        $response->assertSee('Pintu Toilet Rusak');
        $response->assertDontSee('Lampu Terang Mati');
    }

    public function test_filter_status_daftar_laporan(): void
    {
        $mahasiswa = User::factory()->create(['role' => UserRole::Mahasiswa]);

        Laporan::factory()->create([
            'user_id' => $mahasiswa->id,
            'status' => StatusLaporan::Menunggu,
        ]);
        Laporan::factory()->create([
            'user_id' => $mahasiswa->id,
            'status' => StatusLaporan::Selesai,
        ]);

        $response = $this->actingAs($mahasiswa)->get(
            route('mahasiswa.laporan.index', ['status' => StatusLaporan::Selesai->value]),
        );

        $response->assertOk();
        $response->assertViewHas('laporans', fn ($paginator) => $paginator->total() === 1);
    }
}
