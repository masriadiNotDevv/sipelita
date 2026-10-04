# Dokumentasi Struktur Aplikasi - SIPELITA

## Nama Proyek
SIPELITA - Sistem Pelaporan Fasilitas Kampus

## Tanggal Penciptaan
(Tanggal akan dicatat berdasarkan commit pertama Git)

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

## Arsitektur
- MVC (Model-View-Controller) Laravel
- Role-based access (Admin, Staff, Mahasiswa) melalui enum `App\Enums\UserRole`
- Routing terpusat `routes/web.php`
- Middleware `role:{role}` yang terdaftar sebagai alias di `bootstrap/app.php`
- Status laporan memakai enum `App\Enums\StatusLaporan`, prioritas memakai `App\Enums\PrioritasLaporan`
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
│   │   └── StaffController.php
│   ├── Staff/
│   └── Mahasiswa/
├── Http/Middleware/
│   └── RoleMiddleware.php
├── Http/Requests/
│   ├── StoreStaffRequest.php
│   └── UpdateStaffRequest.php
├── Models/
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
│   │   └── staff/
│   │       ├── index.blade.php
│   │       ├── create.blade.php
│   │       ├── edit.blade.php
│   │       └── _form.blade.php
│   ├── staff/
│   └── mahasiswa/

routes/
├── web.php
└── auth.php

tests/
└── Feature/
    ├── HalamanPublikTest.php
    ├── DashboardPerRoleTest.php
    ├── Auth/
    └── Admin/
        └── StaffControllerTest.php

public/
├── assets/
└── build/
```

## Role & Hak Akses
| Role | Deskripsi |
|---|---|
| admin | Mengelola pengguna, kategori, fasilitas, master data, laporan, statistik |
| staff | Menangani laporan, update status, memberikan tanggapan |
| mahasiswa | Membuat laporan, melihat riwayat laporan, notifikasi/status |

## ERD
- users (id, name, email, email_verified_at, password, role, remember_token, timestamps)
- laporan (id, kode, user_id, kategori_id, fasilitas_id, ditangani_oleh, judul, deskripsi, lokasi, prioritas, status, bukti_foto, selesai_at, timestamps)
- kategori (id, nama, deskripsi, timestamps)
- fasilitas (id, nama, lokasi, gedung, lantai, timestamps)
- tanggapan (id, laporan_id, user_id, isi, status_sebelum, status_sesudah, timestamps)

Relasi:
- `users` 1-* `laporan` (sebagai pelapor)
- `users` 1-* `laporan` (sebagai ditangani_oleh / staff)
- `kategoris` 1-* `laporan`
- `fasilitas` 1-* `laporan`
- `laporans` 1-* `tanggapans`
- `users` 1-* `tanggapans`

## Fitur Utama
- [x] Authentication (login, register, verifikasi email, reset password)
- [x] Profil pengguna
- [x] Dashboard per role (admin, staff, mahasiswa)
- [x] Middleware pembatasan akses berbasis peran
- [x] Kelola staff (tambah, edit, hapus, pencarian akun admin & staff)
- [x] Seeder akun awal admin, staff, mahasiswa, dan kategori fasilitas
- [ ] Form pelaporan
- [ ] Tracking status laporan
- [ ] Manajemen laporan & tanggapan
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

## Pengujian
- 54 feature test, 163 assertion
- `tests/Feature/HalamanPublikTest.php` - halaman publik dan alur autentikasi
- `tests/Feature/DashboardPerRoleTest.php` - dashboard per peran
- `tests/Feature/Admin/StaffControllerTest.php` - kelola staff
- `php artisan test`
- `vendor/bin/pint`

## Screenshot
(Akan ditambahkan setelah UI jadi)

## Versi yang Diajukan HAKI
v1.0.0 (akan di-tag di Git)
