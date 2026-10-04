# Changelog

Semua perubahan penting pada proyek SIPELITA dicatat pada berkas ini sebagai
bukti timeline pengembangan untuk keperluan pengajuan HAKI.

Format mengikuti [Keep a Changelog](https://keepachangelog.com/) dan proyek ini
menggunakan [Semantic Versioning](https://semver.org/).

## [Unreleased]

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

### Diubah
- `DatabaseSeeder` memanggil `KategoriFasilitasSeeder` dan `DemoAkunSeeder`.
- Navigasi menampilkan tautan "Kelola Staff" khusus untuk peran admin, baik pada
  tampilan desktop maupun mobile.
- Layout aplikasi menampilkan pesan sukses dan error berbasis flash message.

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