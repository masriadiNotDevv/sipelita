# Changelog

Semua perubahan penting pada proyek SIPELITA dicatat pada berkas ini sebagai
bukti timeline pengembangan untuk keperluan pengajuan HAKI.

Format mengikuti [Keep a Changelog](https://keepachangelog.com/) dan proyek ini
menggunakan [Semantic Versioning](https://semver.org/).

## [Unreleased]

Fitur pelaporan di-commit sebagai `ddbf629` (`ddbf62938d051c83326d676fcbdb6cf7b4c1d1e9`)
pada branch `main` dan sudah dipush ke `origin/main`. Export HTML, dokumentasi,
serta alur kerja laporan staff/admin dan notifikasi di bawah ini masih belum
di-commit.

### Ditambahkan
- `config/sipelita.php` sebagai sumber kredensial akun demo dan master data kategori.
- `DemoAkunSeeder` membuat akun admin, staff, dan mahasiswa secara idempoten.
- `KategoriFasilitasSeeder` mengisi 8 kategori fasilitas awal.
- `App\Enums\UserRole::badgeClass()` untuk badge peran yang konsisten.
- Route binding `staff` di `App\Providers\AppServiceProvider` yang membatasi
  resource ke akun admin dan staff saja.
- `Admin\StaffController` dengan aksi `index`, `create`, `store`, `edit`,
  `update`, dan `destroy`, termasuk pencarian akun dan pagination.
- `StoreStaffRequest` dan `UpdateStaffRequest` sebagai batas validasi serta
  otorisasi halaman kelola staff.
- `tests/Feature/Admin/StaffControllerTest.php` dengan 17 feature test.
- `Mahasiswa\LaporanController` dengan aksi `index`, `create`, `store`, dan `show`,
  termasuk pencarian, filter status, pagination, dan statistik laporan.
- `StoreLaporanRequest` dan `LaporanPolicy` untuk validasi serta pembatasan
  laporan milik mahasiswa yang bersangkutan.
- View `resources/views/mahasiswa/laporan/` (`index`, `create`, `_form`, `show`)
  beserta tautan di navigasi dan dashboard mahasiswa.
- Migration `2026_10_05_013257_make_kode_nullable_on_laporans_table.php`.
- `tests/Feature/LaporanMahasiswaTest.php` dengan 16 feature test.
- `figma-export/sipelita-canvas.html`: canvas Figma mandiri berisi 17 frame dengan
  CSS internal, siap diimpor melalui Refore HTML to Figma.
- `RIWAYAT-PENGEMBANGAN.md` sebagai catatan kronologis bertanggal.
- `App\LaporanWorkflow` sebagai pusat alur klaim, penugasan, pemrosesan,
  penyuntingan, pembuatan, pengarsipan, dan pemulihan laporan.
- `App\Notifications\LaporanDiperbarui` untuk notifikasi database/in-app dengan
  metadata peristiwa, status, pelaku, dan tautan sesuai peran penerima.
- `Staff\LaporanController` dan `Admin\LaporanController` beserta view
  `staff/laporan`, `admin/laporan`, dan komponen `laporan-detail`.
- `NotificationController`, view `notifications`, dan komponen
  `notification-dropdown` untuk daftar notifikasi, tandai dibaca, dan tandai
  semua dibaca.
- `ProsesLaporanRequest`, `TugaskanLaporanRequest`, dan
  `UpdateLaporanAdminRequest` sebagai batas validasi dan otorisasi alur baru.
- Migration `create_notifications_table` (id uuid, `type`, `morphs('notifiable')`,
  `data`, `read_at`), `add_deleted_at_to_laporans_table`, dan
  `add_indexes_to_laporan_tables` untuk indeks `laporans(ditangani_oleh, status)`
  serta `tanggapans(laporan_id)`.
- Route `staff.laporan.*`, `admin.laporan.*`, dan `notifications.*`.
- `tests/Feature/LaporanStaffAdminTest.php`, `LaporanPolicyTest.php`, dan
  `NotifikasiTest.php` sebagai kerangka pengujian alur baru.

### Diubah
- `DatabaseSeeder` memanggil `KategoriFasilitasSeeder` dan `DemoAkunSeeder`.
- Navigasi menampilkan tautan "Kelola Staff" khusus untuk peran admin, baik pada
  tampilan desktop maupun mobile.
- Layout aplikasi menampilkan pesan sukses dan error berbasis flash message.
- `Laporan::formatKode()` menggantikan `nextKode()`; kode dibentuk dari `id`
  auto-increment dan tahun `created_at` sebagai `LAP-%Y-%04d` untuk menghindari
  race condition.
- `app/Http/Controllers/Controller.php` memakai trait `AuthorizesRequests`.
- Dokumentasi HAKI diselaraskan dengan fitur pelaporan dan export Figma.
- `Laporan` memakai trait `Illuminate\Database\Eloquent\SoftDeletes`; hapus oleh
  admin berarti mengarsipkan, bukan menghapus permanen, dan tersedia aksi restore.
- `Mahasiswa\LaporanController::store()` memanggil `LaporanWorkflow::dibuat()`
  sehingga admin, staff, dan pelapor memperoleh notifikasi saat laporan dibuat.
- `LaporanPolicy` diperluas dengan matriks izin untuk admin, staff, dan pelapor,
  termasuk klaim, penugasan, pemrosesan, pengarsipan, dan pemulihan.
- `RoleMiddleware` memvalidasi argumen peran memakai `UserRole::tryFrom()`.
- Status laporan tidak dibatasi urutannya; `selesai_at` hanya terisi saat status
  `Selesai`, lalu dikosongkan kembali bila status berubah.
- Catatan alur kerja diperbarui di `PROMPTS.md`, `ARCHITECTURE.md`,
  `BERKAS-HAKI.md`, dan `RIWAYAT-PENGEMBANGAN.md`.

### Diperbaiki
- Ejaan keterangan prioritas menjadi `Mengganggu aktivitas belajar dan mengajar`
  dan `Mengganggu keselamatan penghuni kampus`, serta beberapa teks canvas Figma.

## [1.0.0] - 2026-10-04

### Ditambahkan
- Inisialisasi proyek Laravel 13, Laravel Breeze, dan Laravel Boost.
- Dokumentasi HAKI: `PROMPTS.md`, `ARCHITECTURE.md`, `BERKAS-HAKI.md`,
  `ATTRIBUTIONS.md`, `NOTICE`, dan Lisensi MIT.
- Tema ungu SIPELITA berbasis Tailwind CSS dengan palet `brand`, Google Fonts
  Inter, dan Material Symbols Outlined.
- Halaman beranda, footer, dan navigasi.
- Enum `UserRole`, `StatusLaporan`, dan `PrioritasLaporan`.
- Kolom `role` pada tabel `users` beserta migration.
- `RoleMiddleware` dengan alias `role` di `bootstrap/app.php`.
- Dashboard admin, staff, dan mahasiswa beserta controller dan route.
- Migration, model, dan factory untuk `laporans`, `kategoris`, `fasilitas`,
  dan `tanggapans`.
- Halaman autentikasi Breeze, verifikasi email, reset password, dan profil.
- 37 feature test: `HalamanPublikTest` dan `DashboardPerRoleTest`.

### Diperbaiki
- `resources/views/layouts/navigation.blade.php` membaca `Auth::user()` tanpa
  Penjaga, sehingga halaman publik menghasilkan error
  `Attempt to read property "name" on null`.
- Duplikasi layout: komponen anonim `components/app-layout.blade.php` dihapus
  karena bertabrakan dengan class component `App\View\Components\AppLayout`.
- Tautan Lisensi, Notice, dan Atribusi di footer yang sebelumnya mengarah ke
  path yang tidak dilayani server.