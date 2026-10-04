<footer class="border-t border-gray-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-2">
                <div class="flex items-center gap-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600 text-white shadow-sm">
                        <span class="material-symbols-outlined text-base">report</span>
                    </div>
                    <div class="flex flex-col leading-tight">
                        <span class="text-sm font-bold tracking-tight text-gray-900">SIPELITA</span>
                        <span class="text-[10px] font-medium text-gray-500">Sistem Pelaporan Fasilitas Kampus</span>
                    </div>
                </div>
                <p class="mt-4 max-w-xl text-sm leading-relaxed text-gray-600">
                    SIPELITA - Sistem Pelaporan Fasilitas Kampus. Mempermudah pelaporan, penanganan, dan pemantauan masalah fasilitas secara terstruktur, transparan, dan terpantau.
                </p>
                <div class="mt-6 flex items-center gap-3 text-gray-400">
                    <span class="material-symbols-outlined text-lg">school</span>
                    <span class="text-sm text-gray-500">Untuk mendukung pelayanan kampus yang lebih baik</span>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-900">Akses Cepat</h3>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="link-brand">Beranda</a></li>
                    @guest
                        <li><a href="{{ route('login') }}" class="link-brand">Masuk</a></li>
                        <li><a href="{{ route('register') }}" class="link-brand">Daftar</a></li>
                    @endguest
                    <li><a href="#tentang" class="link-brand">Tentang SIPELITA</a></li>
                    <li><a href="#alur" class="link-brand">Alur Pelaporan</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-900">Role Sistem</h3>
                <ul class="mt-4 space-y-2 text-sm text-gray-600">
                    <li class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg text-brand-600">shield_person</span>
                        Admin - Pengelola sistem
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg text-brand-600">support_agent</span>
                        Staff - Penindaklanjut laporan
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg text-brand-600">school</span>
                        Mahasiswa - Pelapor fasilitas
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-gray-200 pt-6 sm:flex-row">
            <p class="text-sm text-gray-500">
                &copy; {{ date('Y') }} SIPELITA. Dibuat oleh Masriadi, Nurul Aulia, Nadia, Nuri Zilvani, Elsariani, Jona Irwansyah, Irsan. Dilisensikan di bawah MIT License.
            </p>
            <div class="flex items-center gap-4 text-sm text-gray-500">
                <a href="/LICENSE" class="link-brand">Lisensi</a>
                <span aria-hidden="true">•</span>
                <a href="/ATTRIBUTIONS.md" class="link-brand">Atribusi</a>
                <span aria-hidden="true">•</span>
                <a href="/NOTICE" class="link-brand">Notice</a>
            </div>
        </div>
    </div>
</footer>
