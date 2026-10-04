<x-app-layout>
    <section class="relative overflow-hidden bg-gradient-to-b from-white via-brand-50/20 to-white">
        <div class="absolute inset-x-0 -top-40 -z-10 h-[28rem] bg-gradient-to-b from-brand-100/60 via-transparent to-transparent blur-3xl"></div>
        <div class="mx-auto max-w-7xl px-4 pb-16 pt-10 sm:px-6 lg:px-8 lg:pt-16">
            <div class="grid items-center gap-10 lg:grid-cols-2">
                <div class="space-y-8">
                    <div class="inline-flex items-center gap-2 rounded-full border border-brand-200 bg-white px-4 py-1 text-sm font-medium text-brand-700 shadow-sm">
                        <span class="material-symbols-outlined text-base">verified</span>
                        Sistem Pelaporan Fasilitas Kampus
                    </div>
                    <div class="space-y-4">
                        <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl">
                            Laporkan, Tindaklanjuti, &amp; Pantau Fasilitas Kampus dengan
                            <span class="bg-gradient-to-r from-brand-600 via-brand-500 to-brand-700 bg-clip-text text-transparent">SIPELITA</span>
                        </h1>
                        <p class="max-w-2xl text-lg leading-relaxed text-gray-600">
                            SIPELITA mempermudah mahasiswa melaporkan kerusakan fasilitas, membantu staff menindaklanjuti secara terstruktur, dan memberi admin kendali penuh untuk memastikan setiap laporan terselesaikan dengan transparan.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-4">
                        @guest
                            <a href="{{ route('register') }}" class="btn-primary">
                                <span class="material-symbols-outlined text-lg">add_circle</span>
                                Buat Laporan Pertama
                            </a>
                            <a href="{{ route('login') }}" class="btn-secondary">
                                <span class="material-symbols-outlined text-lg">login</span>
                                Masuk ke Akun
                            </a>
                        @else
                            @if (auth()->user()->role === 'mahasiswa')
                                <a href="{{ url('/mahasiswa/dashboard') }}" class="btn-primary">
                                    <span class="material-symbols-outlined text-lg">description</span>
                                    Ke Dashboard Mahasiswa
                                </a>
                            @elseif (auth()->user()->role === 'staff')
                                <a href="{{ url('/staff/dashboard') }}" class="btn-primary">
                                    <span class="material-symbols-outlined text-lg">assignment</span>
                                    Ke Dashboard Staff
                                </a>
                            @elseif (auth()->user()->role === 'admin')
                                <a href="{{ url('/admin/dashboard') }}" class="btn-primary">
                                    <span class="material-symbols-outlined text-lg">dashboard</span>
                                    Ke Dashboard Admin
                                </a>
                            @endif
                            <a href="{{ route('profile.edit') }}" class="btn-ghost">
                                <span class="material-symbols-outlined text-lg">settings</span>
                                Pengaturan Profil
                            </a>
                        @endguest
                    </div>
                    <div class="flex flex-wrap items-center gap-6 pt-2 text-sm text-gray-500">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-base text-brand-600">how_to_reg</span>
                            Role: Admin, Staff, Mahasiswa
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-base text-brand-600">timeline</span>
                            Tracking Status Real-time
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-base text-brand-600">shield</span>
                            Berbasis Laravel + Tailwind
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="absolute -inset-4 -z-10 rounded-[2rem] bg-gradient-to-tr from-brand-200/40 via-transparent to-transparent blur-2xl"></div>
                    <div class="card overflow-hidden p-0 shadow-lg ring-1 ring-gray-200">
                        <div class="flex items-center gap-2 border-b border-gray-200 bg-gray-50 px-5 py-3">
                            <div class="flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-red-400"></span>
                                <span class="h-2.5 w-2.5 rounded-full bg-yellow-400"></span>
                                <span class="h-2.5 w-2.5 rounded-full bg-green-400"></span>
                            </div>
                            <div class="ml-4 flex-1 rounded-lg bg-white px-3 py-1.5 text-xs text-gray-500 ring-1 ring-gray-200">sipelita.kampus</div>
                        </div>
                        <div class="grid gap-6 p-6 sm:p-8">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Ringkasan Laporan</p>
                                    <p class="text-sm text-gray-500">Tampilan contoh dashboard SIPELITA</p>
                                </div>
                                <span class="inline-flex items-center gap-2 rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700">
                                    <span class="material-symbols-outlined text-base">insights</span>
                                    Dalam Perbaikan
                                </span>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-3">
                                <div class="card p-4">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm text-gray-500">Total Laporan</p>
                                        <span class="material-symbols-outlined text-lg text-brand-600">folder</span>
                                    </div>
                                    <p class="mt-2 text-2xl font-bold text-gray-900">128</p>
                                    <p class="mt-1 text-xs text-gray-500">+12 bulan ini</p>
                                </div>
                                <div class="card p-4">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm text-gray-500">Menunggu</p>
                                        <span class="material-symbols-outlined text-lg text-amber-500">hourglass_empty</span>
                                    </div>
                                    <p class="mt-2 text-2xl font-bold text-gray-900">24</p>
                                    <p class="mt-1 text-xs text-gray-500">Perlu tindak lanjut</p>
                                </div>
                                <div class="card p-4">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm text-gray-500">Selesai</p>
                                        <span class="material-symbols-outlined text-lg text-emerald-600">check_circle</span>
                                    </div>
                                    <p class="mt-2 text-2xl font-bold text-gray-900">92</p>
                                    <p class="mt-1 text-xs text-gray-500">Terselesaikan</p>
                                </div>
                            </div>
                            <div class="card p-0">
                                <div class="border-b border-gray-200 px-5 py-3">
                                    <p class="text-sm font-semibold text-gray-900">Laporan Terbaru</p>
                                </div>
                                <div class="divide-y divide-gray-100">
                                    <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-brand-700">
                                                <span class="material-symbols-outlined text-lg">lightbulb</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">Lampu koridor gedung A mati</p>
                                                <p class="text-xs text-gray-500">Oleh: Mahasiswa • 2 jam lalu</p>
                                            </div>
                                        </div>
                                        <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800">Diproses</span>
                                    </div>
                                    <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-brand-700">
                                                <span class="material-symbols-outlined text-lg">water_drop</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">Kran air wastafel bocor</p>
                                                <p class="text-xs text-gray-500">Oleh: Mahasiswa • Kemarin</p>
                                            </div>
                                        </div>
                                        <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">Selesai</span>
                                    </div>
                                    <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-brand-700">
                                                <span class="material-symbols-outlined text-lg">chair</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">Kursi ruang kelas rusak</p>
                                                <p class="text-xs text-gray-500">Oleh: Mahasiswa • 3 hari lalu</p>
                                            </div>
                                        </div>
                                        <span class="rounded-full bg-brand-100 px-2.5 py-0.5 text-xs font-semibold text-brand-800">Dalam Perbaikan</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="tentang" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl text-center">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Tentang SIPELITA</h2>
            <p class="mt-4 text-lg leading-relaxed text-gray-600">
                Dibangun khusus untuk kebutuhan pelaporan fasilitas kampus. Dirancang agar proses pelaporan tidak berputar lewat grup chat, tapi terpusat, terdokumentasi, dan mudah dipantau oleh semua pihak.
            </p>
        </div>
        <div class="mt-12 grid gap-6 md:grid-cols-3">
            <div class="card flex flex-col gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-100 text-brand-700">
                    <span class="material-symbols-outlined">edit_note</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Mudah Dilaporkan</h3>
                <p class="text-sm leading-relaxed text-gray-600">
                    Form pelaporan yang ringkas. Cukup isi lokasi, jenis fasilitas, deskripsi, dan data pendukung untuk mempercepat penanganan.
                </p>
            </div>
            <div class="card flex flex-col gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-100 text-brand-700">
                    <span class="material-symbols-outlined">sync_alt</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Transparan & Terpantau</h3>
                <p class="text-sm leading-relaxed text-gray-600">
                    Setiap laporan memiliki status yang jelas (Menunggu, Diproses, Dalam Perbaikan, Selesai, Ditolak) sehingga pelapor bisa memantau perkembangannya.
                </p>
            </div>
            <div class="card flex flex-col gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-100 text-brand-700">
                    <span class="material-symbols-outlined">groups</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Terbagi Per Role</h3>
                <p class="text-sm leading-relaxed text-gray-600">
                    Akses disesuaikan untuk Mahasiswa, Staff, dan Admin. Tiap role hanya melihat menu yang relevan dengan tugasnya.
                </p>
            </div>
        </div>
    </section>

    <section id="alur" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl text-center">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Alur Pelaporan SIPELITA</h2>
            <p class="mt-4 text-lg leading-relaxed text-gray-600">
                Langkah sederhana untuk melaporkan dan menindaklanjuti fasilitas kampus.
            </p>
        </div>
        <div class="mt-12 grid gap-6 md:grid-cols-4">
            <div class="card relative flex flex-col gap-3">
                <span class="text-3xl font-bold text-brand-600">01</span>
                <h3 class="text-lg font-semibold text-gray-900">Daftar & Masuk</h3>
                <p class="text-sm leading-relaxed text-gray-600">Mahasiswa mendaftar akun dan login ke SIPELITA.</p>
                <span class="material-symbols-outlined absolute right-6 top-6 text-4xl text-brand-100">person_add</span>
            </div>
            <div class="card relative flex flex-col gap-3">
                <span class="text-3xl font-bold text-brand-600">02</span>
                <h3 class="text-lg font-semibold text-gray-900">Buat Laporan</h3>
                <p class="text-sm leading-relaxed text-gray-600">Isi form pelaporan dengan lokasi, fasilitas, deskripsi & foto jika ada.</p>
                <span class="material-symbols-outlined absolute right-6 top-6 text-4xl text-brand-100">edit_document</span>
            </div>
            <div class="card relative flex flex-col gap-3">
                <span class="text-3xl font-bold text-brand-600">03</span>
                <h3 class="text-lg font-semibold text-gray-900">Ditindaklanjuti</h3>
                <p class="text-sm leading-relaxed text-gray-600">Staff menerima dan menindaklanjuti laporan sesuai prioritas.</p>
                <span class="material-symbols-outlined absolute right-6 top-6 text-4xl text-brand-100">assignment_turned_in</span>
            </div>
            <div class="card relative flex flex-col gap-3">
                <span class="text-3xl font-bold text-brand-600">04</span>
                <h3 class="text-lg font-semibold text-gray-900">Selesai & Terpantau</h3>
                <p class="text-sm leading-relaxed text-gray-600">Laporan ditutup setelah selesai dan dapat dipantau statusnya.</p>
                <span class="material-symbols-outlined absolute right-6 top-6 text-4xl text-brand-100">task_alt</span>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <div class="card flex flex-col items-center gap-6 px-6 py-10 text-center sm:px-10">
            <div class="inline-flex items-center gap-2 rounded-full border border-brand-200 bg-brand-50 px-4 py-1 text-sm font-medium text-brand-700">
                <span class="material-symbols-outlined text-base">rocket_launch</span>
                SIPELITA siap digunakan
            </div>
            <div class="space-y-3">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Mulai kelola pelaporan fasilitas kampus sekarang</h2>
                <p class="max-w-2xl text-lg leading-relaxed text-gray-600">
                    Sesuai kebutuhan tim: Admin, Staff, dan Mahasiswa. Dibangun orisinal dengan Laravel, MySQL, Tailwind CSS & Vite.
                </p>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-4">
                @guest
                    <a href="{{ route('register') }}" class="btn-primary">
                        <span class="material-symbols-outlined text-lg">person_add</span>
                        Daftar Sebagai Mahasiswa
                    </a>
                    <a href="{{ route('login') }}" class="btn-secondary">Sudah punya akun? Masuk</a>
                @else
                    <a href="{{ auth()->user()->role === 'admin' ? url('/admin/dashboard') : (auth()->user()->role === 'staff' ? url('/staff/dashboard') : url('/mahasiswa/dashboard')) }}" class="btn-primary">
                        <span class="material-symbols-outlined text-lg">arrow_forward</span>
                        Lanjut ke Dashboard
                    </a>
                @endguest
            </div>
            <p class="text-sm text-gray-500">
                SIPELITA &copy; {{ date('Y') }} • Tim Pengembang: Masriadi, Nurul Aulia, Nadia, Nuri Zilvani, Elsariani, Jona Irwansyah, Irsan
            </p>
        </div>
    </section>
</x-app-layout>
