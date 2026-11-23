# Prompt yang Digunakan Selama Pembuatan Proyek SIPELITA

Dokumen ini bertujuan untuk mendokumentasikan seluruh prompt yang dimasukkan ke AI/Assistant selama pengembangan proyek "SIPELITA - Sistem Pelaporan Fasilitas Kampus".

Hal ini disiapkan sebagai bagian dari bukti proses pengembangan (orisinalitas) untuk keperluan pengajuan HAKI.

## Prompt 1 - Permintaan Awal
```
saya membuat proyek "SIPELITA - Sistem Pelaporan Fasilitas Kampus", proyek tersebut mau saya HAKI kan dan tentunya akan mengunakan framework pihak ketiga yg gratis seperti, google icons, google fonts, tailwindcss, laravel, mysql, dan kodenya tidak boleh mengambil kode orang lain,
```

## Prompt 2 - Tanya Rencana Lisensi & Dokumen
```
kira2 dalam merancang app untuk di HAKI kan, apa yg dipersiapkan ?
```

## Prompt 3 - Instruksi Pembuatan Proyek Lengkap
```
ok setiap pembuatan apk harus kamu sertakan apa saja prompt yg saya masukan , dokumentasi struktur app nya ,,buat proyeknya gunakan tailwindcss gunakan styling yg enak dilihat gunakan warna aksen ungu , rolenya ada 3 orang admin, staff, dan mahasiswa, buat tampilan home untuk instruksi lainnya nanti sy tambahkan
```

## Prompt 4 - Perbaikan Error Halaman & Akses Per Role
```
pastikan semua halaman bisa diakses, dan fix error resources\views\layouts\navigation.blade.php:26 Attempt to read property "name" on null
```

## Prompt 5 - Pertanyaan Lisensi Font & Ikon
```
apakah kamu menggunakan fonts dan icons yg opensource ?
```

### Tindak Lanjut
Diverifikasi bahwa aset yang dipakai adalah open source:
- Font **Inter** - SIL Open Font License 1.1
- Ikon **Material Symbols Outlined** - Apache License 2.0 (Google LLC)

Keduanya dimuat dari CDN `fonts.googleapis.com` sehingga tidak ada file aset pihak ketiga yang disalin ke dalam repo. Opsi self-hosting sempat mengusulkan pemindahan aset ke `public/fonts/` agar aplikasi berjalan mandiri, namun keputusan tersebut masih menunggu konfirmasi.

## Prompt 6 - Kredensial Admin & Fitur Tambah Staff
```
apa kredensial adminnya ?
```

### Tindak Lanjut
Ditemukan bahwa tabel `users` masih kosong dan belum ada jalur untuk membuat akun admin:
- Registrasi selalu menghasilkan peran `mahasiswa`
- `DatabaseSeeder` belum pernah dijalankan

### Prompt 7 - Permintaan Akun Admin/Staff dan Fitur Tambah Staff
```
bagusnya ada akun untuk admin dan staff , dan buatkan fitur tambah staff , dan catat perubahan dari saya dan prompt saya , dan commit juga
```

## Prompt 8 - Implementasi Fitur Pelaporan (ringkasan)
> Catatan: teks asli tidak tercatat pada fase sebelumnya. Ringkasan berikut
> berdasarkan perubahan yang diminta dan sudah diverifikasi dari commit.

Buat fitur form pelaporan untuk mahasiswa: daftar laporan dengan pencarian dan
filter status, form pembuatan laporan dengan validasi serta upload bukti foto,
halaman detail laporan, pembatasan akses berdasarkan kepemilikan, pengujian, dan
pencatatan perubahan. Untuk sementara perubahan belum boleh di-commit.

### Tindak Lanjut
- `Mahasiswa\LaporanController`, `StoreLaporanRequest`, dan `LaporanPolicy`.
- View `mahasiswa/laporan`, route `mahasiswa.laporan.*`, dan 16 feature test.
- Kode laporan dihasilkan dari `id` auto-increment melalui `formatKode()`.

## Prompt 9 - Push ke GitHub
```
coba push ke github
```

### Tindak Lanjut
- Commit `ddbf629` dibuat dan dipush ke `origin/main`.
- Sebelumnya, saat hanya diminta mengecek koneksi, user memilih "Belum, cukup cek koneksi".

## Prompt 10 - Export Figma dari HTML
```
buat saja 1 file html dan isinya kayak canvas figma dan mempunyai page terpisah kayak di figma 1 file html smua page dstu semua tanpa css external hanya internal saja buat skarang
```

### Tindak Lanjut
- Dibuat `figma-export/sipelita-canvas.html`.
- File berisi 17 frame, CSS internal di `<style>`, tanpa CSS/font/script external.
- Plugin yang dipilih: Refore HTML to Figma (AI-Generated HTML Pages → Select File).
- File ini masih belum di-commit pada saat dokumentasi ini diperbarui.

## Prompt 11 - Alur Kerja Laporan Staff/Admin dan Notifikasi
```
lanjutkan fitur laporan untuk staff dan admin. staff bisa melihat semua laporan dan mengklaim laporan yang belum ditangani, admin bisa mengelola penuh laporan termasuk hapus, tapi hapusnya cukup diarsipkan dengan soft delete dan bisa dipulihkan, jangan force delete. admin juga bisa menugaskan atau mengganti staff penangani. status laporan bebas, semua StatusLaporan boleh dipilih kapan saja, dan selesai_at hanya diisi kalau statusnya Selesai. tambahkan notifikasi database/in-app untuk admin, staff, dan pelapor saat laporan dibuat, diklaim, ditugaskan, diproses/status, diedit, diarsipkan, atau dipulihkan. update catatan alur kerjanya, lalu commit dan push ke origin/main.
```

### Tindak Lanjut
Keputusan yang disetujui user dicatat dan dipakai sebagai acuan implementasi:
- Admin: kelola penuh laporan, termasuk pengarsipan via soft delete dan restore;
  tidak ada force delete. Staff: melihat semua laporan dan mengklaim yang belum
  ditangani. Admin: menugaskan atau menugaskan ulang staff penangani.
- Status laporan bebas (semua `StatusLaporan` valid kapan saja); `selesai_at`
  hanya diisi saat status `Selesai`.
- Notifikasi database/in-app untuk admin, staff, dan pelapor pada peristiwa
  dibuat, diklaim, ditugaskan, diproses/status, diedit, diarsipkan, dipulihkan.
- Alur kerja dipusatkan di `App\LaporanWorkflow`; notifikasi memakai
  `App\Notifications\LaporanDiperbarui` dan tabel `notifications`.
- Matriks izin ada di `LaporanPolicy`; akses rute diperkeras dengan peran dan
  `UserRole::tryFrom()` pada `RoleMiddleware`.
- Rute baru: `staff.laporan.index/show/klaim/proses`,
  `admin.laporan.index/arsip/show/edit/update/destroy/restore/penangani/proses`,
  dan `notifications.index/read/read-all`.
- Catatan alur kerja diperbarui di `ARCHITECTURE.md` dan
  `RIWAYAT-PENGEMBANGAN.md`.
- Verifikasi yang harus dijalankan sebelum commit: `php artisan migrate --force`,
  `vendor/bin/pint --dirty --format agent`, `php artisan test --compact`,
  `npm.cmd run build`; setelah itu `git commit` dan `git push origin main`.
- Branch `main`; HEAD sebelum pekerjaan ini adalah `ddbf629` dan sudah dipush.
  Pekerjaan prompt ini masih belum di-commit pada saat prompt dicatat.

## Ringkasan Perubahan per Prompt
| Prompt | Perubahan | Commit |
|---|---|---|
| 1-3 | Inisialisasi Laravel, dokumentasi HAKI, Lisensi MIT, tema ungu, halaman beranda | `af6808a`, `eddbad1`, `eb4c94d` |
| 4 | Perbaikan error navigasi, akses berbasis peran, dashboard per role, tema dipulihkan | `c959d51` |
| 5 | Konfirmasi lisensi open source font & ikon | - |
| 6-7 | `config/sipelita.php`, `DemoAkunSeeder`, `KategoriFasilitasSeeder`, `StaffController`, form request, route & view `admin/staff`, navigasi admin | `4debfc2` |
| 8 | Form pelaporan mahasiswa, validasi, upload bukti, policy, kode otomatis, dan 16 feature test | `ddbf629` |
| 9 | Commit dan push ke `origin/main` | `ddbf629` |
| 10 | Canvas Figma satu file HTML dengan 17 frame dan CSS internal | belum di-commit |
| 11 | Alur laporan staff/admin, arsip soft delete + restore, notifikasi semua peran/event, pembaruan catatan alur, dan verifikasi sebelum push | belum di-commit; sebelum pekerjaan `ddbf629` |

Detail teknis perubahan dapat dilihat pada `CHANGELOG.md`, `ARCHITECTURE.md`, dan `RIWAYAT-PENGEMBANGAN.md`.

## Catatan
- Seluruh kode dibangun secara orisinal berdasarkan kebutuhan SIPELITA.
- Tidak menyalin repository, template komersial, atau snippet kode orang lain secara utuh.
- Menggunakan dependensi pihak ketiga berlisensi gratis (MIT, Apache 2.0, OFL) yang terdokumentasi.
- History Git sejak awal menjadi bukti timeline penciptaan.
