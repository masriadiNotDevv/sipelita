<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Akun Demo SIPELITA
    |--------------------------------------------------------------------------
    |
    | Kredensial berikut hanya digunakan oleh seeder `DemoAkunSeeder` untuk
    | membuat akun awal saat pengembangan. Nilai dapat dioverridetrue melalui
    | environment variable agar kredensial produksi tidak tersimpan di repo.
    |
    */

    'demo' => [
        'enabled' => (bool) env('SIPELITA_DEMO_ENABLED', true),

        'admin' => [
            'name' => env('SIPELITA_ADMIN_NAME', 'Administrator SIPELITA'),
            'email' => env('SIPELITA_ADMIN_EMAIL', 'admin@sipelita.test'),
            'password' => env('SIPELITA_ADMIN_PASSWORD', 'password123'),
        ],

        'staff' => [
            'name' => env('SIPELITA_STAFF_NAME', 'Staff SIPELITA'),
            'email' => env('SIPELITA_STAFF_EMAIL', 'staff@sipelita.test'),
            'password' => env('SIPELITA_STAFF_PASSWORD', 'password123'),
        ],

        'mahasiswa' => [
            'name' => env('SIPELITA_MAHASISWA_NAME', 'Mahasiswa SIPELITA'),
            'email' => env('SIPELITA_MAHASISWA_EMAIL', 'mahasiswa@sipelita.test'),
            'password' => env('SIPELITA_MAHASISWA_PASSWORD', 'password123'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Kategori Fasilitas Awal
    |--------------------------------------------------------------------------
    |
    | Master data kategori yang dibuat oleh `KategoriFasilitasSeeder` agar fitur
    | pelaporan langsung memiliki pilihan kategori yang relevan.
    |
    */

    'kategori' => [
        'Ruang Kelas',
        'Laboratorium',
        'Perpustakaan',
        'Kantin Kampus',
        'Toilet Umum',
        'Area Parkir',
        'Wi-Fi & Jaringan',
        'Halaman Terbuka',
    ],

];
