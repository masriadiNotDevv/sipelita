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
│   ├── Staff/
│   └── Mahasiswa/
├── Http/Middleware/
│   └── RoleMiddleware.php
├── Models/
├── View/Components/
└── Providers/

database/
├── migrations/
├── factories/
└── seeders/

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
│   ├── staff/
│   └── mahasiswa/

routes/
├── web.php
└── auth.php

tests/
└── Feature/
    ├── HalamanPublikTest.php
    └── DashboardPerRoleTest.php

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
| GET | /profile | profile.edit | auth |

## Pengujian
- 37 feature test, 101 assertion
- `php artisan test`
- `vendor/bin/pint`

## Screenshot
(Akan ditambahkan setelah UI jadi)

## Versi yang Diajukan HAKI
v1.0.0 (akan di-tag di Git)
