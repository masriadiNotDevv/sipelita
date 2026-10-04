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

## Fitur (Rencana)
- Autentikasi & Manajemen Pengguna
- Role: `admin`, `staff`, `mahasiswa`
- Dashboard berdasarkan role
- Form Pelaporan Fasilitas
- Tracking Status Laporan
- Manajemen Laporan & Tanggapan
- Master Data (Kategori, Fasilitas, Lokasi)

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

6. Jalankan migration
```bash
php artisan migrate
```

7. Jalankan aplikasi (dev)
```bash
composer run dev
```

## Lisensi

Proyek ini dilisensikan di bawah [MIT License](./LICENSE).

Copyright (c) 2026 Masriadi, Nurul Aulia, Nadia, Nuri Zilvani, Elsariani, Jona Irwansyah, Irsan.

## Atribusi Pihak Ketiga

Lihat [ATTRIBUTIONS.md](./ATTRIBUTIONS.md) dan [NOTICE](./NOTICE) untuk daftar pustaka pihak ketiga beserta lisensinya.

## Dokumentasi HAKI

- [PROMPTS.md](./PROMPTS.md) - Daftar prompt yang digunakan
- [ARCHITECTURE.md](./ARCHITECTURE.md) - Dokumentasi struktur & arsitektur
- [BERKAS-HAKI.md](./BERKAS-HAKI.md) - Checklist pengajuan HAKI
