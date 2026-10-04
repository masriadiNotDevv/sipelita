# Checklist Berkas HAKI - SIPELITA

## Informasi Umum
- Judul Ciptaan: SIPELITA - Sistem Pelaporan Fasilitas Kampus
- Jenis Ciptaan: Program Komputer
- Tanggal Pertama Kali Diciptakan: [diisi berdasarkan commit pertama Git]
- Versi Diajukan: v1.0.0
- Tim Pencipta: Masriadi, Nurul Aulia, Nadia, Nuri Zilvani, Elsariani, Jona Irwansyah, Irsan

## A. Bukti Orisinalitas
- [ ] Git repository dibuat sejak awal pengembangan
- [ ] Commit teratur dengan pesan jelas
- [ ] Commit pertama teridentifikasi (hash + tanggal)
- [ ] `git tag v1.0.0` setelah versi final
- [ ] Wireframe/ERD/Flowchart disimpan (opsional tapi direkomendasikan)
- [ ] Tidak ada kode copy-paste dari repo publik
- [ ] Audit orisinalitas dilakukan

## B. Dokumen Teknis
- [x] `PROMPTS.md` - dokumentasi prompt
- [x] `ARCHITECTURE.md` - arsitektur & struktur
- [x] `README.md` - deskripsi, instalasi, lisensi
- [x] `CHANGELOG.md` - riwayat perubahan per versi
- [ ] Screenshot UI (minimal 3-5)
- [ ] Deskripsi Ciptaan untuk DJKI (PDF)

## C. Kepatuhan Lisensi Pihak Ketiga
- [x] `LICENSE` - MIT
- [x] `NOTICE` 
- [x] `ATTRIBUTIONS.md`
- [x] Daftar dependensi lengkap (`composer.json`, `package.json`)
- [ ] Cek tidak ada dependensi Copyleft (GPL/AGPL) - MySQL dicatat sebagai
      perangkat lunak basis data terpisah, tidak dibundle dalam repo
- [x] Hanya aset berlisensi gratis (Google Fonts/Icons) yang dipakai
      - Inter: SIL OFL 1.1
      - Material Symbols Outlined: Apache 2.0
- [ ] Tambahkan `THIRD-PARTY-LICENSES.md` berisi teks lisensi lengkap
- [ ] Hapus dependensi basi: `@tailwindcss/vite` terpasang namun tidak dipakai
- [ ] `npm audit` masih melaporkan 5 vulnerability high pada transitive
      dependency Tailwind v3 (`braces`, `micromatch`, `fast-glob`)

## D. Data DJKI
- [ ] Nama lengkap pencipta sesuai KTP
- [ ] NIK pencipta
- [ ] Alamat pencipta
- [ ] Kewarganegaraan: Indonesia
- [ ] Kesepakatan pemegang hak cipta (internal tim)
- [ ] Formulir pendaftaran e-HakCipta

## E. Berkas Penyerahan (Deposit)
- [ ] `.zip` bersih (exclude: vendor/, node_modules/, .env, storage/, bootstrap/cache/, public/build/)
- [ ] Sertakan `LICENSE`, `README.md`, `NOTICE`, `ATTRIBUTIONS.md`
- [ ] Source code sesuai versi `v1.0.0`
- [ ] Bukti pembayaran
- [ ] Surat kuasa (jika diwakilkan)

## Catatan
Kepatuhan lisensi: Laravel MIT, Tailwind MIT, Google Icons Apache 2.0, Google Fonts OFL/Apache 2.0. Aman untuk pendaftaran HAKI.
