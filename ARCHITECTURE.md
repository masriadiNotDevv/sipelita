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
- Backend: Laravel 12.x (PHP 8.4)
- Database: MySQL
- Frontend: Blade + Tailwind CSS (v4 via @tailwindcss/vite) + Vite
- UI Assets: Google Fonts, Google Icons/Material Symbols
- Tools: Composer, npm

## Arsitektur
- MVC (Model-View-Controller) Laravel
- Role-based access (Admin, Staff, Mahasiswa)
- Routing terpusat `routes/web.php`
- Middleware untuk proteksi role

## Struktur Direktori (Rencana)
```
app/
├── Http/Controllers/
│   ├── Auth/
│   ├── Admin/
│   ├── Staff/
│   └── Mahasiswa/
├── Models/
├── Policies/
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
│   ├── components/
│   ├── home/
│   ├── admin/
│   ├── staff/
│   └── mahasiswa/

routes/
├── web.php
└── auth.php

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

## ERD (Rencana)
- users (id, name, email, password, role, nim/nik, phone, etc)
- laporan (id, user_id, kategori_id, fasilitas_id, deskripsi, lokasi, foto, status, prioritas, ...)
- kategori (id, nama, deskripsi)
- fasilitas (id, nama, lokasi, gedung, lantai)
- tanggapan (id, laporan_id, user_id, isi, status_baru, ...)

## Fitur Utama (Rencana)
- Authentication (login/register)
- Dashboard per role
- Form pelaporan
- Tracking status laporan
- Manajemen laporan & tanggapan
- Manajemen master data

## Screenshot
(Akan ditambahkan setelah UI jadi)

## Versi yang Diajukan HAKI
v1.0.0 (akan di-tag di Git)
