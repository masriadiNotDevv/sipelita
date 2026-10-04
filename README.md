# SIPELITA - Sistem Pelaporan Fasilitas Kampus

SIPELITA adalah sistem pelaporan fasilitas kampus yang mempermudah mahasiswa, staff dan admin dalam mengelola pelaporan kerusakan atau masalah fasilitas kampus.

## Tim Pengembang (Pencipta)
Masriadi, Nurul Aulia, Nadia, Nuri Zilvani, Elsariani, Jona Irwansyah, Irsan

## Deskripsi
SIPELITA bertujuan untuk memfasilitasi proses pelaporan fasilitas kampus secara terstruktur, transparan dan terpantau. Mahasiswa dapat membuat laporan, Staff dapat menindaklanjuti, dan Admin dapat mengelola seluruh data sistem.

## Teknologi yang Digunakan
- [Laravel](https://laravel.com/) - PHP Framework
- [MySQL](https://www.mysql.com/) - Database
- [Tailwind CSS](https://tailwindcss.com/) - Utility-first CSS Framework
- [Vite](https://vitejs.dev/) - Frontend Tooling
- [Google Fonts](https://fonts.google.com/) - Web Fonts
- [Google Icons / Material Symbols](https://fonts.google.com/icons) - Icon System

## Fitur
- Autentikasi & manajemen profil (login, register, verifikasi email, reset password)
- Role: `admin`, `staff`, `mahasiswa`
- Dashboard berdasarkan role
- Kelola staff (admin): tambah, edit, hapus, dan pencarian akun staff/admin
- Kedepan: Form Pelaporan Fasilitas
- Kedepan: Tracking Status Laporan
- Kedepan: Manajemen Laporan & Tanggapan
- Kedepan: Master Data (Kategori, Fasilitas, Lokasi)

Rincian perubahan tersedia pada `CHANGELOG.md`.

## Persyaratan Sistem
- PHP >= 8.4
- Composer >= 2.x
- Node.js >= 20.x dan npm >= 10.x
- MySQL >= 8.0

## Instalasi

1. Clone repositori
```bash
git clone <repo-url> sipe
cd sipe
```

2. Install dependency PHP
```bash
composer install
```

3. Install dependency Frontend
```bash
npm install
```

4. Konfigurasi environment
```bash
cp .env.example .env
php artisan key:generate
```

5. Konfigurasi database di `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipelita
DB_USERNAME=root
DB_PASSWORD=
```

6. Jalankan migration beserta data awal
```bash
php artisan migrate --seed
```

7. Jalankan aplikasi (dev)
```bash
composer run dev
```

## Akun Awal

`php artisan migrate --seed` membuat satu akun untuk setiap peran. Kredensial
default dapat dioverride melalui environment variable (lihat `config/sipelita.php`).

| Peran | Email | Password |
|---|---|---|
| admin | `admin@sipelita.test` | `password123` |
| staff | `staff@sipelita.test` | `password123` |
| mahasiswa | `mahasiswa@sipelita.test` | `password123` |

Kredensial di atas **hanya untuk pengembangan**. Untuk produksi, isi nilainya
melalui `.env`:

```env
SIPELITA_DEMO_ENABLED=false
SIPELITA_ADMIN_EMAIL=admin@kampus.test
SIPELITA_ADMIN_PASSWORD=<password-kuat>
SIPELITA_STAFF_PASSWORD=<password-kuat>
SIPELITA_MAHASISWA_PASSWORD=<password-kuat>
```

Seeder bersifat idempoten, sehingga `php artisan db:seed` dapat dijalankan ulang
tanpa menghasilkan akun duplikat.

Untuk membuat staff baru, gunakan halaman **Kelola Staff** di dashboard admin
(`/admin/staff`) alih-alih menambahkannya lewat seeder.

## Pengujian
```bash
php artisan test
vendor/bin/pint
```

## Lisensi

Proyek ini dilisensikan di bawah [MIT License](./LICENSE). Daftar dependensi
pihak ketiga beserta lisensinya ada di [ATTRIBUTIONS.md](./ATTRIBUTIONS.md) dan
[NOTICE](./NOTICE).

Copyright (c) 2026 Masriadi, Nurul Aulia, Nadia, Nuri Zilvani, Elsariani, Jona Irwansyah, Irsan.

## Atribusi Pihak Ketiga

Lihat [ATTRIBUTIONS.md](./ATTRIBUTIONS.md) dan [NOTICE](./NOTICE) untuk daftar pustaka pihak ketiga beserta lisensinya.

## Dokumentasi HAKI

- [PROMPTS.md](./PROMPTS.md) - Daftar prompt yang digunakan
- [ARCHITECTURE.md](./ARCHITECTURE.md) - Dokumentasi struktur & arsitektur
- [BERKAS-HAKI.md](./BERKAS-HAKI.md) - Checklist pengajuan HAKI
