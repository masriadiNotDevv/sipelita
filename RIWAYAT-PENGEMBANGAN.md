# Riwayat Pengembangan SIPELITA

Dokumen ini mencatat kronologi pengembangan SIPELITA berdasarkan tanggal commit Git.
Detail teknis perubahan ada pada `CHANGELOG.md`, sedangkan prompt yang digunakan ada pada `PROMPTS.md`.

## 04-10-2026 — Inisialisasi dan fondasi aplikasi
- Inisialisasi repositori, Lisensi MIT, `README.md`, `PROMPTS.md`, `ARCHITECTURE.md`, `BERKAS-HAKI.md`, `ATTRIBUTIONS.md`, dan `NOTICE`.
- Tailwind CSS, tema ungu, layout, navigasi, footer, dan halaman beranda.
- Struktur role `admin`, `staff`, `mahasiswa`; model, migration, dan factory awal.
- Commit: `af6808a`, `eddbad1`, `eb4c94d`, `ffd275e`, `9b8cd27`.

## 05-10-2026 — Akses per role dan Kelola Staff
- Perbaikan akses `Auth::user()` pada navigasi.
- Middleware `role`, dashboard per role, dan 37 feature test.
- Akun demo admin/staff/mahasiswa, kategori fasilitas, dan fitur Kelola Staff.
- Total pengujian menjadi 54 feature test, 163 assertion.
- Commit: `4b24ea6`, `c959d51`, `4debfc2`.

## 23-11-2026 — Form pelaporan mahasiswa
- `Mahasiswa\LaporanController`, `StoreLaporanRequest`, dan `LaporanPolicy`.
- Daftar laporan dengan pencarian, filter status, pagination, dan statistik.
- Form pembuatan laporan dengan validasi dan upload bukti foto JPG/PNG/WEBP maksimal 2 MB.
- Halaman detail laporan serta tautan di navigasi dan dashboard mahasiswa.
- Kode laporan `LAP-%Y-%04d` dibuat dari `id` auto-increment dan tahun `created_at`.
- 16 feature test baru; total pengujian menjadi 70 test, 232 assertion.
- Commit dan push: `ddbf629`.

## 23-11-2026 — Export canvas Figma
- Dibuat `figma-export/sipelita-canvas.html` sebagai satu file mandiri.
- 17 frame: Beranda, Masuk, Daftar, Lupa Password, Reset Password, Verifikasi Email,
  Konfirmasi Password, Dashboard Admin, Kelola Staff, Tambah Staff, Edit Staff,
  Dashboard Staff, Dashboard Mahasiswa, Laporan Saya, Buat Laporan, Detail Laporan,
  dan Pengaturan Profil.
- CSS internal di `<style>`; tanpa CSS, font, atau script external.
- Ditujukan untuk import melalui Refore HTML to Figma.
- Belum di-commit pada saat riwayat ini ditulis.

## 23-11-2026 — Alur kerja laporan staff/admin dan notifikasi
- Branch `main`; HEAD sebelum pekerjaan ini adalah `ddbf629`
  (`ddbf62938d051c83326d676fcbdb6cf7b4c1d1e9`) dan sudah dipush ke `origin/main`.
- Keputusan user: admin mengelola penuh laporan termasuk pengarsipan lewat soft
  delete dan restore tanpa force delete; staff melihat semua laporan dan
  mengklaim yang belum ditangani; admin menugaskan atau menugaskan ulang staff.
- Status laporan bebas: seluruh `StatusLaporan` boleh dipilih kapan saja.
  `selesai_at` hanya diisi saat status `Selesai` dan dikosongkan bila status berubah.
- Notifikasi database/in-app untuk admin, staff, dan pelapor pada peristiwa
  dibuat, diklaim, ditugaskan, diproses/status, diedit, diarsipkan, dipulihkan.
- `App\LaporanWorkflow` memusatkan alur; `App\Notifications\LaporanDiperbarui`
  mengirim notifikasi ke tabel `notifications` (id uuid, morph notifiable,
  `data`, `read_at`).
- `LaporanPolicy` diperluas dengan matriks izin admin/staff/pelapor;
  `RoleMiddleware` memvalidasi peran dengan `UserRole::tryFrom()`.
- `Laporan` memakai `SoftDeletes`; `Mahasiswa\LaporanController::store()` kini
  memanggil `LaporanWorkflow::dibuat()`.
- Migration baru: `create_notifications_table`,
  `add_deleted_at_to_laporans_table`, dan `add_indexes_to_laporan_tables`
  untuk indeks `laporans(ditangani_oleh, status)` dan `tanggapans(laporan_id)`.
- Rute baru: `staff.laporan.index/show/klaim/proses`,
  `admin.laporan.index/arsip/show/edit/update/destroy/restore/penangani/proses`,
  serta `notifications.index/read/read-all`.
- View baru di `staff/laporan`, `admin/laporan`, `notifications`, dan komponen
  `laporan-detail` serta `notification-dropdown`; kerangka pengujian
  `LaporanStaffAdminTest`, `LaporanPolicyTest`, dan `NotifikasiTest`.
- Verifikasi yang harus dijalankan: `php artisan migrate --force`,
  `vendor/bin/pint --dirty --format agent`, `php artisan test --compact`,
  `npm.cmd run build`.
- Pekerjaan ini masih belum di-commit dan harus di-commit lalu dipush ke
  `origin/main`.
