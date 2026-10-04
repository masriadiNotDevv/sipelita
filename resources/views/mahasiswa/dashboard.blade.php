@php
    $user = auth()->user();
    $stats = [
        [
            'label' => 'Laporan Saya',
            'value' => $totalLaporan,
            'icon' => 'assignment',
            'class' => 'bg-brand-100 text-brand-700',
        ],
        [
            'label' => 'Menunggu',
            'value' => $laporanMenunggu,
            'icon' => 'hourglass_empty',
            'class' => 'bg-amber-100 text-amber-700',
        ],
        [
            'label' => 'Diproses',
            'value' => $laporanDiproses,
            'icon' => 'pending_actions',
            'class' => 'bg-sky-100 text-sky-700',
        ],
        [
            'label' => 'Selesai',
            'value' => $laporanSelesai,
            'icon' => 'task_alt',
            'class' => 'bg-emerald-100 text-emerald-700',
        ],
    ];
@endphp

<x-app-layout title="Dashboard Mahasiswa">
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Dashboard Mahasiswa</h1>
                <p class="mt-1 text-sm text-gray-500">Selamat datang, {{ $user->name }}. Pantau status laporan Anda.</p>
            </div>
            <span class="badge bg-brand-100 text-brand-800">
                <span class="material-symbols-outlined !text-base">{{ $user->userRole()->icon() }}</span>
                {{ $user->userRole()->label() }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($stats as $stat)
                    <div class="card">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">{{ $stat['label'] }}</p>
                                <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($stat['value'], 0, ',', '.') }}</p>
                            </div>
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl {{ $stat['class'] }}">
                                <span class="material-symbols-outlined">{{ $stat['icon'] }}</span>
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="card lg:col-span-2">
                    <h2 class="text-lg font-semibold text-gray-900">Laporan Terbaru Saya</h2>
                    <p class="text-sm text-gray-500">Riwayat laporan yang Anda ajukan beserta statusnya.</p>

                    <div class="mt-4 divide-y divide-gray-100">
                        @forelse ($laporanTerbaru as $laporan)
                            <div class="flex flex-wrap items-center justify-between gap-3 py-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-gray-900">{{ $laporan->judul }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $laporan->kode }} &middot; {{ $laporan->kategori?->nama ?? 'Umum' }} &middot; {{ $laporan->tanggal_pendek }}
                                    </p>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <span class="badge {{ $laporan->prioritas->badgeClass() }}">
                                        <span class="material-symbols-outlined !text-sm">{{ $laporan->prioritas->icon() }}</span>
                                        {{ $laporan->prioritas->label() }}
                                    </span>
                                    <span class="badge {{ $laporan->status->badgeClass() }}">
                                        <span class="material-symbols-outlined !text-sm">{{ $laporan->status->icon() }}</span>
                                        {{ $laporan->status->label() }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="py-10 text-center">
                                <span class="material-symbols-outlined !text-4xl text-gray-300">report</span>
                                <p class="mt-2 text-sm text-gray-500">Anda belum mengajukan laporan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="card">
                    <h2 class="text-lg font-semibold text-gray-900">Cara Melapor</h2>
                    <p class="text-sm text-gray-500">Ikuti langkah berikut agar laporan diproses cepat.</p>

                    <ol class="mt-4 space-y-3">
                        <li class="flex items-start gap-3 rounded-lg bg-gray-50 px-4 py-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-600 text-xs font-semibold text-white">1</span>
                            <p class="text-sm text-gray-700">Deskripsikan masalah fasilitas dan tentukan lokasinya.</p>
                        </li>
                        <li class="flex items-start gap-3 rounded-lg bg-gray-50 px-4 py-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-600 text-xs font-semibold text-white">2</span>
                            <p class="text-sm text-gray-700">Unggah bukti foto bila ada agar mudah diverifikasi.</p>
                        </li>
                        <li class="flex items-start gap-3 rounded-lg bg-gray-50 px-4 py-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-600 text-xs font-semibold text-white">3</span>
                            <p class="text-sm text-gray-700">Pantau progres melalui dashboard dan tanggapan staff.</p>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>