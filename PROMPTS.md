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

## Ringkasan Perubahan per Prompt
| Prompt | Perubahan | Commit |
|---|---|---|
| 1-3 | Inisialisasi Laravel, dokumentasi HAKI, Lisensi MIT, tema ungu, halaman beranda | `af6808a`, `eddbad1`, `eb4c94d` |
| 4 | Perbaikan error navigasi, akses berbasis peran, dashboard per role, tema dipulihkan | `c959d51` |
| 5 | Konfirmasi lisensi open source font & ikon | - |
| 6-7 | `config/sipelita.php`, `DemoAkunSeeder`, `KategoriFasilitasSeeder`, `StaffController`, form request, route & view `admin/staff`, navigasi admin | lihat `CHANGELOG.md` |

Detail teknis perubahan dapat dilihat pada `CHANGELOG.md` dan `ARCHITECTURE.md`.

## Catatan
- Seluruh kode dibangun secara orisinal berdasarkan kebutuhan SIPELITA.
- Tidak menyalin repository, template komersial, atau snippet kode orang lain secara utuh.
- Menggunakan dependensi pihak ketiga berlisensi gratis (MIT, Apache 2.0, OFL) yang terdokumentasi.
- History Git sejak awal menjadi bukti timeline penciptaan.
