<?php

namespace Tests\Feature;

use App\Enums\PrioritasLaporan;
use App\LaporanWorkflow;
use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotifikasiTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(int $kategoriId): array
    {
        return [
            'judul' => 'Kran Air Bocor Di Koridor',
            'deskripsi' => 'Kran air di koridor lantai satu bocor dan perlu segera diperbaiki.',
            'lokasi' => 'Gedung A Lantai 1',
            'kategori_id' => $kategoriId,
            'prioritas' => PrioritasLaporan::Tinggi->value,
        ];
    }

    private function detailPayload(int $kategoriId): array
    {
        return [
            'judul' => 'Perbaikan kran air koridor lantai satu',
            'deskripsi' => 'Kran air koridor lantai satu sudah diperiksa dan akan diganti hari ini.',
            'lokasi' => 'Gedung A Lantai 1',
            'kategori_id' => $kategoriId,
            'fasilitas_id' => null,
            'prioritas' => PrioritasLaporan::Mendesak->value,
        ];
    }

    private function assertNotifikasi(
        User $penerima,
        string $event,
        string $eventLabel,
        Laporan $laporan,
        User $actor,
        string $pesan,
        string $url,
    ): void {
        $this->assertDatabaseHas('notifications', [
            'type' => 'laporan-'.$event,
            'notifiable_type' => User::class,
            'notifiable_id' => $penerima->id,
            'read_at' => null,
            'data->event' => $event,
            'data->event_label' => $eventLabel,
            'data->judul' => $laporan->judul,
            'data->kode' => $laporan->kode,
            'data->laporan_id' => $laporan->id,
            'data->status' => $laporan->status->value,
            'data->pesan' => $pesan,
            'data->actor_id' => $actor->id,
            'data->actor_name' => $actor->name,
            'data->url' => $url,
        ]);
    }

    public function test_mahasiswa_membuat_laporan_mengirim_notifikasi_ke_admin_staff_dan_pelapor(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Utama']);
        $staff = User::factory()->staff()->create(['name' => 'Staff Satu']);
        $mahasiswa = User::factory()->mahasiswa()->create(['name' => 'Siti Pelapor']);
        $kategori = Kategori::factory()->create();

        $this->actingAs($mahasiswa)->post(
            route('mahasiswa.laporan.store'),
            $this->validPayload($kategori->id),
        )->assertRedirect();

        $laporan = Laporan::sole();
        $pesan = "Laporan baru {$laporan->kode} telah diajukan oleh {$mahasiswa->name}.";

        $this->assertDatabaseCount('notifications', 3);
        $this->assertNotifikasi(
            $admin,
            'dibuat',
            'Laporan Baru',
            $laporan,
            $mahasiswa,
            $pesan,
            route('admin.laporan.show', $laporan),
        );
        $this->assertNotifikasi(
            $staff,
            'dibuat',
            'Laporan Baru',
            $laporan,
            $mahasiswa,
            $pesan,
            route('staff.laporan.show', $laporan),
        );
        $this->assertNotifikasi(
            $mahasiswa,
            'dibuat',
            'Laporan Baru',
            $laporan,
            $mahasiswa,
            $pesan,
            route('mahasiswa.laporan.show', $laporan),
        );
    }

    public function test_klaim_menghasilkan_notifikasi_diklaim(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Utama']);
        $staff = User::factory()->staff()->create(['name' => 'Staff Satu']);
        $mahasiswa = User::factory()->mahasiswa()->create(['name' => 'Siti Pelapor']);
        $laporan = Laporan::factory()->create(['user_id' => $mahasiswa->id]);

        $this->actingAs($staff)->patch(route('staff.laporan.klaim', $laporan));

        $laporan->refresh();
        $pesan = "{$staff->name} mengklaim laporan {$laporan->kode}.";

        $this->assertNotifikasi(
            $admin,
            'diklaim',
            'Laporan Diklaim',
            $laporan,
            $staff,
            $pesan,
            route('admin.laporan.show', $laporan),
        );
        $this->assertNotifikasi(
            $staff,
            'diklaim',
            'Laporan Diklaim',
            $laporan,
            $staff,
            $pesan,
            route('staff.laporan.show', $laporan),
        );
        $this->assertNotifikasi(
            $mahasiswa,
            'diklaim',
            'Laporan Diklaim',
            $laporan,
            $staff,
            $pesan,
            route('mahasiswa.laporan.show', $laporan),
        );
    }

    public function test_penugasan_dan_pelepasan_penangani_menghasilkan_notifikasi_ditugaskan(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Utama']);
        $staff = User::factory()->staff()->create(['name' => 'Staff Satu']);
        $mahasiswa = User::factory()->mahasiswa()->create(['name' => 'Siti Pelapor']);
        $laporan = Laporan::factory()->create(['user_id' => $mahasiswa->id]);

        $this->actingAs($admin)->patch(
            route('admin.laporan.penangani', $laporan),
            ['penangani_id' => $staff->id],
        );

        $pesanPenugasan = "{$admin->name} menugaskan laporan {$laporan->kode} kepada {$staff->name}.";

        $this->assertNotifikasi(
            $admin,
            'ditugaskan',
            'Penangani Diubah',
            $laporan,
            $admin,
            $pesanPenugasan,
            route('admin.laporan.show', $laporan),
        );
        $this->assertNotifikasi(
            $staff,
            'ditugaskan',
            'Penangani Diubah',
            $laporan,
            $admin,
            $pesanPenugasan,
            route('staff.laporan.show', $laporan),
        );
        $this->assertNotifikasi(
            $mahasiswa,
            'ditugaskan',
            'Penangani Diubah',
            $laporan,
            $admin,
            $pesanPenugasan,
            route('mahasiswa.laporan.show', $laporan),
        );

        $this->actingAs($admin)->patch(
            route('admin.laporan.penangani', $laporan),
            ['penangani_id' => ''],
        );

        $pesanPelepasan = "{$admin->name} melepas penangani laporan {$laporan->kode}.";

        $this->assertDatabaseCount('notifications', 6);
        $this->assertNotifikasi(
            $admin,
            'ditugaskan',
            'Penangani Diubah',
            $laporan,
            $admin,
            $pesanPelepasan,
            route('admin.laporan.show', $laporan),
        );
        $this->assertNotifikasi(
            $staff,
            'ditugaskan',
            'Penangani Diubah',
            $laporan,
            $admin,
            $pesanPelepasan,
            route('staff.laporan.show', $laporan),
        );
        $this->assertNotifikasi(
            $mahasiswa,
            'ditugaskan',
            'Penangani Diubah',
            $laporan,
            $admin,
            $pesanPelepasan,
            route('mahasiswa.laporan.show', $laporan),
        );
    }

    public function test_proses_menghasilkan_notifikasi_diproses_dengan_status_terbaru(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Utama']);
        $staff = User::factory()->staff()->create(['name' => 'Staff Satu']);
        $mahasiswa = User::factory()->mahasiswa()->create(['name' => 'Siti Pelapor']);
        $laporan = Laporan::factory()->create([
            'user_id' => $mahasiswa->id,
            'ditangani_oleh' => $staff->id,
        ]);

        $this->actingAs($staff)->patch(
            route('staff.laporan.proses', $laporan),
            ['status' => 'selesai', 'isi' => 'Sudah dicek dan ditindaklanjuti oleh tim fasilitas.'],
        );

        $laporan->refresh();
        $pesan = "{$staff->name} memperbarui laporan {$laporan->kode} menjadi Selesai.";

        $this->assertNotifikasi(
            $admin,
            'diproses',
            'Status Diperbarui',
            $laporan,
            $staff,
            $pesan,
            route('admin.laporan.show', $laporan),
        );
        $this->assertNotifikasi(
            $staff,
            'diproses',
            'Status Diperbarui',
            $laporan,
            $staff,
            $pesan,
            route('staff.laporan.show', $laporan),
        );
        $this->assertNotifikasi(
            $mahasiswa,
            'diproses',
            'Status Diperbarui',
            $laporan,
            $staff,
            $pesan,
            route('mahasiswa.laporan.show', $laporan),
        );
    }

    public function test_perbaruan_detail_menghasilkan_notifikasi_diedit(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Utama']);
        $staff = User::factory()->staff()->create(['name' => 'Staff Satu']);
        $mahasiswa = User::factory()->mahasiswa()->create(['name' => 'Siti Pelapor']);
        $kategori = Kategori::factory()->create();
        $laporan = Laporan::factory()->create(['user_id' => $mahasiswa->id]);

        $this->actingAs($admin)->put(
            route('admin.laporan.update', $laporan),
            $this->detailPayload($kategori->id),
        );

        $laporan->refresh();
        $pesan = "{$admin->name} memperbarui detail laporan {$laporan->kode}.";

        $this->assertNotifikasi(
            $admin,
            'diedit',
            'Detail Diperbarui',
            $laporan,
            $admin,
            $pesan,
            route('admin.laporan.show', $laporan),
        );
        $this->assertNotifikasi(
            $staff,
            'diedit',
            'Detail Diperbarui',
            $laporan,
            $admin,
            $pesan,
            route('staff.laporan.show', $laporan),
        );
        $this->assertNotifikasi(
            $mahasiswa,
            'diedit',
            'Detail Diperbarui',
            $laporan,
            $admin,
            $pesan,
            route('mahasiswa.laporan.show', $laporan),
        );
    }

    public function test_arsipkan_menghasilkan_notifikasi_diarsipkan_dengan_url_role_aware(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Utama']);
        $staff = User::factory()->staff()->create(['name' => 'Staff Satu']);
        $mahasiswa = User::factory()->mahasiswa()->create(['name' => 'Siti Pelapor']);
        $laporan = Laporan::factory()->create(['user_id' => $mahasiswa->id]);

        $this->actingAs($admin)->delete(route('admin.laporan.destroy', $laporan));

        $pesan = "{$admin->name} mengarsipkan laporan {$laporan->kode}.";

        $this->assertNotifikasi(
            $admin,
            'diarsipkan',
            'Laporan Diarsipkan',
            $laporan,
            $admin,
            $pesan,
            route('admin.laporan.arsip'),
        );
        $this->assertNotifikasi(
            $staff,
            'diarsipkan',
            'Laporan Diarsipkan',
            $laporan,
            $admin,
            $pesan,
            route('staff.laporan.index'),
        );
        $this->assertNotifikasi(
            $mahasiswa,
            'diarsipkan',
            'Laporan Diarsipkan',
            $laporan,
            $admin,
            $pesan,
            route('mahasiswa.laporan.index'),
        );
    }

    public function test_pemulihan_menghasilkan_notifikasi_dipulihkan(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Utama']);
        $staff = User::factory()->staff()->create(['name' => 'Staff Satu']);
        $mahasiswa = User::factory()->mahasiswa()->create(['name' => 'Siti Pelapor']);
        $laporan = Laporan::factory()->create(['user_id' => $mahasiswa->id]);

        $this->actingAs($admin)->delete(route('admin.laporan.destroy', $laporan));
        $this->actingAs($admin)->patch(route('admin.laporan.restore', $laporan));

        $laporan->refresh();
        $pesan = "{$admin->name} memulihkan laporan {$laporan->kode}.";

        $this->assertDatabaseCount('notifications', 6);
        $this->assertNotifikasi(
            $admin,
            'dipulihkan',
            'Laporan Dipulihkan',
            $laporan,
            $admin,
            $pesan,
            route('admin.laporan.show', $laporan),
        );
        $this->assertNotifikasi(
            $staff,
            'dipulihkan',
            'Laporan Dipulihkan',
            $laporan,
            $admin,
            $pesan,
            route('staff.laporan.show', $laporan),
        );
        $this->assertNotifikasi(
            $mahasiswa,
            'dipulihkan',
            'Laporan Dipulihkan',
            $laporan,
            $admin,
            $pesan,
            route('mahasiswa.laporan.show', $laporan),
        );
    }

    public function test_halaman_notifikasi_hanya_menampilkan_notifikasi_pemiliknya(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Utama']);
        $mahasiswa = User::factory()->mahasiswa()->create(['name' => 'Siti Pelapor']);
        $laporan = Laporan::factory()->create(['user_id' => $mahasiswa->id]);

        $this->app->make(LaporanWorkflow::class)->dibuat($laporan, $mahasiswa);

        $response = $this->actingAs($admin)->get(route('notifications.index'));

        $response->assertOk();
        $response->assertViewHas('notifications', fn ($notifications) => $notifications->total() === 1
            && $notifications->first()->notifiable_id === $admin->id);
        $response->assertSee("Laporan baru {$laporan->kode} telah diajukan oleh {$mahasiswa->name}.");
    }

    public function test_menandai_satu_notifikasi_sebagai_dibaca_hanya_untuk_pemiliknya(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Utama']);
        $staff = User::factory()->staff()->create(['name' => 'Staff Satu']);
        $mahasiswa = User::factory()->mahasiswa()->create(['name' => 'Siti Pelapor']);
        $laporan = Laporan::factory()->create(['user_id' => $mahasiswa->id]);

        $this->app->make(LaporanWorkflow::class)->dibuat($laporan, $mahasiswa);

        $notifikasiAdmin = $admin->notifications()->firstOrFail();
        $notifikasiStaff = $staff->notifications()->firstOrFail();

        $this->actingAs($admin)
            ->from(route('notifications.index'))
            ->patch(route('notifications.read', $notifikasiAdmin->id))
            ->assertRedirect(route('notifications.index'));

        $this->assertNotNull($notifikasiAdmin->fresh()->read_at);
        $this->assertSame(0, $admin->fresh()->unreadNotifications()->count());
        $this->assertSame(1, $staff->fresh()->unreadNotifications()->count());

        $this->actingAs($admin)
            ->patch(route('notifications.read', $notifikasiStaff->id))
            ->assertNotFound();

        $this->assertNull($notifikasiStaff->fresh()->read_at);
    }

    public function test_menandai_semua_notifikasi_sebagai_dibaca_hanya_untuk_pemiliknya(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Utama']);
        $staff = User::factory()->staff()->create(['name' => 'Staff Satu']);
        $mahasiswa = User::factory()->mahasiswa()->create(['name' => 'Siti Pelapor']);
        $kategori = Kategori::factory()->create();

        $this->actingAs($mahasiswa)->post(
            route('mahasiswa.laporan.store'),
            $this->validPayload($kategori->id),
        )->assertRedirect();
        $this->actingAs($mahasiswa)->post(
            route('mahasiswa.laporan.store'),
            $this->validPayload($kategori->id),
        )->assertRedirect();

        $this->assertSame(2, $admin->fresh()->unreadNotifications()->count());
        $this->assertSame(2, $staff->fresh()->unreadNotifications()->count());

        $this->actingAs($admin)
            ->from(route('notifications.index'))
            ->patch(route('notifications.read-all'))
            ->assertRedirect(route('notifications.index'))
            ->assertSessionHas('status');

        $this->assertSame(0, $admin->fresh()->unreadNotifications()->count());
        $this->assertSame(2, $staff->fresh()->unreadNotifications()->count());
        $this->assertSame(2, $mahasiswa->fresh()->unreadNotifications()->count());
    }
}
