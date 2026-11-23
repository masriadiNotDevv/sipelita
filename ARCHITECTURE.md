# Dokumentasi Struktur Aplikasi - SIPELITA

## Nama Proyek
SIPELITA - Sistem Pelaporan Fasilitas Kampus

## Tanggal Penciptaan
04-10-2026 (commit pertama Git `af6808a`)

## Tim Pengembang (Pencipta)
Masriadi, Nurul Aulia, Nadia, Nuri Zilvani, Elsariani, Jona Irwansyah, Irsan

## Deskripsi Singkat
Sistem pelaporan fasilitas kampus yang memfasilitasi mahasiswa untuk melaporkan kerusakan/masalah fasilitas, staff untuk menangani laporan, dan admin untuk mengelola sistem.

## Stack Teknologi
- Backend: Laravel 13.34.0 (PHP 8.4)
- Database: MySQL (produksi) / SQLite (pengembangan & test)
- Frontend: Blade + Tailwind CSS v3.4 + Vite
- UI Assets: Google Fonts (Inter), Google Material Symbols
- Auth: Laravel Breeze
- Tools: Composer, npm
- Export desain: HTML mandiri (`figma-export/sipelita-canvas.html`) dengan CSS internal

## Arsitektur
- MVC (Model-View-Controller) Laravel
- Role-based access (Admin, Staff, Mahasiswa) melalui enum `App\Enums\UserRole`
- Routing terpusat `routes/web.php`
- Middleware `role:{role}` yang terdaftar sebagai alias di `bootstrap/app.php`
- Status laporan memakai enum `App\Enums\StatusLaporan`, prioritas memakai `App\Enums\PrioritasLaporan`
- Otorisasi laporan melalui `App\Policies\LaporanPolicy` (matriks admin, staff, pelapor)
- Kode laporan dibentuk oleh `Laporan::formatKode()` dari `id` auto-increment dan tahun `created_at`
- Alur laporan dipusatkan di `App\LaporanWorkflow` (klaim, tugaskan, proses, edit,
  dibuat, arsipkan, pulihkan) dan memancarkan notifikasi database/in-app
- `Laporan` memakai `Illuminate\Database\Eloquent\SoftDeletes`: hapus admin =
  arsip, dengan aksi restore; tidak ada force delete
- Notifikasi `App\Notifications\LaporanDiperbarui` disimpan di tabel
  `notifications` (id uuid, morph notifiable, `data`, `read_at`)
- `RoleMiddleware` mencocokkan argumen peran dengan `UserRole::tryFrom()`
- Indeks: `laporans(ditangani_oleh, status)` dan `tanggapans(laporan_id)`
- Layout utama `resources/views/layouts/app.blade.php` dirender oleh class component `App\View\Components\AppLayout`

## Struktur Direktori
```
app/
├── Enums/
│   ├── UserRole.php
│   ├── StatusLaporan.php
│   └── PrioritasLaporan.php
├── Http/Controllers/
│   ├── Auth/
│   ├── Admin/
│   │   ├── DashboardController.php
│   │   ├── LaporanController.php
│   │   └── StaffController.php
│   ├── Staff/
│   │   ├── DashboardController.php
│   │   └── LaporanController.php
│   ├── Mahasiswa/
│   │   ├── DashboardController.php
│   │   └── LaporanController.php
│   └── NotificationController.php
├── Http/Middleware/
│   └── RoleMiddleware.php
├── Http/Requests/
│   ├── StoreLaporanRequest.php
│   ├── StoreStaffRequest.php
│   ├── UpdateStaffRequest.php
│   ├── ProsesLaporanRequest.php
│   ├── TugaskanLaporanRequest.php
│   └── UpdateLaporanAdminRequest.php
├── Models/
├── Notifications/
│   └── LaporanDiperbarui.php
├── Policies/
│   └── LaporanPolicy.php
├── LaporanWorkflow.php
├── View/Components/
└── Providers/

config/
└── sipelita.php

database/
├── migrations/
├── factories/
└── seeders/
    ├── DatabaseSeeder.php
    ├── KategoriFasilitasSeeder.php
    └── DemoAkunSeeder.php

resources/
├── css/app.css
├── js/app.js
├── views/
│   ├── layouts/
│   │   ├── app.blade.php
│   │   ├── guest.blade.php
│   │   ├── navigation.blade.php
│   │   └── footer.blade.php
│   ├── auth/
│   ├── profile/
│   ├── admin/
│   │   ├── dashboard.blade.php
│   │   ├── staff/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── edit.blade.php
│   │   │   └── _form.blade.php
│   │   └── laporan/
│   │       ├── index.blade.php
│   │       ├── arsip.blade.php
│   │       ├── show.blade.php
│   │       └── edit.blade.php
│   ├── staff/
│   │   ├── dashboard.blade.php
│   │   └── laporan/
│   │       ├── index.blade.php
│   │       └── show.blade.php
│   ├── notifications/
│   │   └── index.blade.php
│   ├── components/
│   │   ├── laporan-detail.blade.php
│   │   └── notification-dropdown.blade.php
│   └── mahasiswa/
│       ├── dashboard.blade.php
│       └── laporan/
│           ├── index.blade.php
│           ├── create.blade.php
│           ├── _form.blade.php
│           └── show.blade.php

routes/
├── web.php
└── auth.php

tests/
└── Feature/
    ├── HalamanPublikTest.php
    ├── DashboardPerRoleTest.php
    ├── LaporanMahasiswaTest.php
    ├── LaporanStaffAdminTest.php
    ├── LaporanPolicyTest.php
    ├── NotifikasiTest.php
    ├── Auth/
    └── Admin/
        └── StaffControllerTest.php

figma-export/
└── sipelita-canvas.html

public/
├── assets/
└── build/
```

## Role & Hak Akses
| Role | Deskripsi |
|---|---|
| admin | Mengelola pengguna, kategori, fasilitas, master data, laporan, statistik; melihat, mengedit, menugaskan penangani, memproses, mengarsipkan (soft delete), dan memulihkan laporan |
| staff | Melihat semua laporan, mengklaim laporan yang belum ditangani, mencatat tanggapan, dan memperbarui status laporan yang ditanganinya |
| mahasiswa | Membuat laporan, melihat riwayat dan detail laporan milik sendiri (pencarian, filter status, pagination), serta menerima notifikasi perkembangannya |

### Matriks Kebijakan `LaporanPolicy`
| Kemampuan | Admin | Staff | Mahasiswa (pelapor) |
|---|---|---|---|
| `viewAny` | Ya | Ya | Ya |
| `view` | Ya | Ya | Hanya laporan miliknya |
| `create` | Tidak | Tidak | Ya |
| `update` | Ya, semua laporan | Tidak | Hanya miliknya, status `Menunggu`/`Diproses` |
| `delete` (arsipkan) | Ya | Tidak | Hanya miliknya, status `Menunggu` |
| `restore` | Ya | Tidak | Tidak |
| `klaim` | Tidak | Ya, laporan belum ditangani dan status `Menunggu`/`Diproses`/`DalamPerbaikan` | Tidak |
| `tugaskan` | Ya | Tidak | Tidak |
| `proses` | Ya | Ya, laporan yang ditanganinya | Tidak |

Status laporan bersifat bebas: seluruh nilai `StatusLaporan` dapat dipilih kapan
saja tanpa pembatasan urutan. Kolom `selesai_at` diisi hanya saat status
`Selesai` dan dikosongkan lagi bila status diubah ke nilai lain.

## ERD
- users (id, name, email, email_verified_at, password, role, remember_token, timestamps)
- laporan (id, kode, user_id, kategori_id, fasilitas_id, ditangani_oleh, judul, deskripsi, lokasi, prioritas, status, bukti_foto, selesai_at, deleted_at, timestamps)
- kategori (id, nama, deskripsi, timestamps)
- fasilitas (id, nama, lokasi, gedung, lantai, timestamps)
- tanggapan (id, laporan_id, user_id, isi, status_sebelum, status_sesudah, timestamps)
- notifications (id uuid, type, notifiable_type, notifiable_id, data, read_at, timestamps)

`laporans.kode` dapat bernilai null sampai `isiKode()` dijalankan. Format akhir adalah `LAP-%Y-%04d`. `deleted_at` dipakai untuk arsip laporan (soft delete) dan restore oleh admin. Indeks yang ditambahkan: `laporans(ditangani_oleh, status)` dan `tanggapans(laporan_id)`.

Relasi:
- `users` 1-* `laporan` (sebagai pelapor)
- `users` 1-* `laporan` (sebagai ditangani_oleh / staff)
- `kategoris` 1-* `laporan`
- `fasilitas` 1-* `laporan`
- `laporans` 1-* `tanggapans`
- `users` 1-* `tanggapans`
- `notifications` polymorphik ke `users` melalui `notifiable_type` dan `notifiable_id`

## Fitur Utama
- [x] Authentication (login, register, verifikasi email, reset password)
- [x] Profil pengguna
- [x] Dashboard per role (admin, staff, mahasiswa)
- [x] Middleware pembatasan akses berbasis peran
- [x] Kelola staff (tambah, edit, hapus, pencarian akun admin & staff)
- [x] Seeder akun awal admin, staff, mahasiswa, dan kategori fasilitas
- [x] Form pelaporan (mahasiswa, validasi + bukti foto)
- [x] Tracking status laporan (riwayat, filter status, detail laporan)
- [x] Manajemen laporan & tanggapan (staff/admin): klaim, penugasan, proses/status,
      edit, arsip soft delete, dan restore
- [x] Notifikasi database/in-app untuk admin, staff, dan pelapor atas seluruh
      peristiwa laporan
- [ ] Manajemen master data

## Rute
| Method | URI | Nama | Akses |
|---|---|---|---|
| GET | / | home | publik (redirect ke dashboard bila terautentikasi) |
| GET | /login | login | tamu |
| GET | /register | register | tamu |
| GET | /password/reset | password.request | tamu |
| GET | /dashboard | dashboard | auth (redirect sesuai role) |
| GET | /admin/dashboard | admin.dashboard | admin |
| GET | /staff/dashboard | staff.dashboard | staff |
| GET | /mahasiswa/dashboard | mahasiswa.dashboard | mahasiswa |
| GET | /admin/staff | admin.staff.index | admin |
| GET | /admin/staff/create | admin.staff.create | admin |
| POST | /admin/staff | admin.staff.store | admin |
| GET | /admin/staff/{staff}/edit | admin.staff.edit | admin |
| PATCH | /admin/staff/{staff} | admin.staff.update | admin |
| DELETE | /admin/staff/{staff} | admin.staff.destroy | admin |
| GET | /mahasiswa/laporan | mahasiswa.laporan.index | mahasiswa |
| GET | /mahasiswa/laporan/create | mahasiswa.laporan.create | mahasiswa |
| POST | /mahasiswa/laporan | mahasiswa.laporan.store | mahasiswa |
| GET | /mahasiswa/laporan/{laporan} | mahasiswa.laporan.show | mahasiswa (pemilik) |
| GET | /notifications | notifications.index | auth |
| PATCH | /notifications/read-all | notifications.read-all | auth |
| PATCH | /notifications/{notification}/read | notifications.read | auth (pemilik notifikasi) |
| GET | /staff/laporan | staff.laporan.index | staff |
| GET | /staff/laporan/{laporan} | staff.laporan.show | staff |
| PATCH | /staff/laporan/{laporan}/klaim | staff.laporan.klaim | staff |
| PATCH | /staff/laporan/{laporan}/proses | staff.laporan.proses | staff |
| GET | /admin/laporan | admin.laporan.index | admin |
| GET | /admin/laporan/arsip | admin.laporan.arsip | admin |
| GET | /admin/laporan/{laporan} | admin.laporan.show | admin |
| GET | /admin/laporan/{laporan}/edit | admin.laporan.edit | admin |
| PATCH/PUT | /admin/laporan/{laporan} | admin.laporan.update | admin |
| DELETE | /admin/laporan/{laporan} | admin.laporan.destroy | admin (arsipkan/soft delete) |
| PATCH | /admin/laporan/{laporan}/restore | admin.laporan.restore | admin |
| PATCH | /admin/laporan/{laporan}/penangani | admin.laporan.penangani | admin |
| PATCH | /admin/laporan/{laporan}/proses | admin.laporan.proses | admin |
| GET | /profile | profile.edit | auth |

## Konfigurasi
- `config/sipelita.php` - kredensial akun demo (`SIPELITA_ADMIN_*`,
  `SIPELITA_STAFF_*`, `SIPELITA_MAHASISWA_*`, `SIPELITA_DEMO_ENABLED`) dan daftar
  kategori fasilitas awal.
- `app/Providers/AppServiceProvider.php` - route binding `staff` yang hanya
  menyelesaikan akun admin dan staff, sehingga akun mahasiswa menghasilkan 404.

## Seeder
- `KategoriFasilitasSeeder` - mengisi master data kategori.
- `DemoAkunSeeder` - membuat akun admin, staff, mahasiswa; idempoten dan dapat
  dinonaktifkan lewat `SIPELITA_DEMO_ENABLED=false`.
- Data `fasilitas` belum di-seed; dropdown fasilitas pada form laporan menampilkan
  pesan "Data fasilitas belum tersedia" bila tabel masih kosong.

## Pengujian
- 70 feature test, 232 assertion
- `tests/Feature/HalamanPublikTest.php` - halaman publik dan alur autentikasi
- `tests/Feature/DashboardPerRoleTest.php` - dashboard per peran
- `tests/Feature/Admin/StaffControllerTest.php` - kelola staff
- `tests/Feature/LaporanMahasiswaTest.php` - pelaporan mahasiswa
- `tests/Feature/LaporanStaffAdminTest.php`, `LaporanPolicyTest.php`,
  `NotifikasiTest.php` - kerangka pengujian alur staff/admin, policy, dan notifikasi
- Urutan verifikasi perubahan terbaru: `php artisan migrate --force`,
  `vendor/bin/pint --dirty --format agent`, `php artisan test --compact`,
  `npm.cmd run build`

## Export Desain
- `figma-export/sipelita-canvas.html` berisi 17 frame dengan CSS internal di `<style>`
  dan tanpa CSS, font, atau script external.
- File diimpor ke Figma melalui plugin Refore HTML to Figma pada menu
  AI-Generated HTML Pages → Select File.

## Screenshot
- Belum diekspor; wireframe/sumber tersedia di `figma-export/sipelita-canvas.html`.

## Versi yang Diajukan HAKI
v1.0.0 (belum di-tag di Git; termasuk pembaruan pelaporan, alur laporan
staff/admin, dan notifikasi). Branch `main`; HEAD sebelum pekerjaan ini adalah
`ddbf629` dan sudah dipush ke `origin/main`.
