@php
    use App\Enums\UserRole;

    $user = auth()->user();
    $stats = [
        [
            'label' => 'Total Laporan',
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

<x-app-layout title="Dashboard Admin">
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Dashboard Admin</h1>
                <p class="mt-1 text-sm text-gray-500">Selamat datang, {{ $user->name }}. Pantau seluruh aktivitas SIPELITA.</p>
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
                    <h2 class="text-lg font-semibold text-gray-900">Laporan Terbaru</h2>
                    <p class="text-sm text-gray-500">Lima laporan terakhir yang masuk ke sistem.</p>

                    <div class="mt-4 divide-y divide-gray-100">
                        @forelse ($laporanTerbaru as $laporan)
                            <div class="flex flex-wrap items-center justify-between gap-3 py-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-gray-900">{{ $laporan->judul }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $laporan->kode }} &middot; {{ $laporan->user?->name }} &middot; {{ $laporan->tanggal_pendek }}
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
                            <p class="py-8 text-center text-sm text-gray-500">Belum ada laporan yang masuk.</p>
                        @endforelse
                    </div>
                </div>

                <div class="card">
                    <h2 class="text-lg font-semibold text-gray-900">Ringkasan Pengguna</h2>
                    <p class="text-sm text-gray-500">Jumlah pengguna terdaftar per peran.</p>

                    <dl class="mt-4 space-y-3">
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                            <dt class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                <span class="material-symbols-outlined !text-lg text-gray-500">groups</span>
                                Total Pengguna
                            </dt>
                            <dd class="text-lg font-bold text-gray-900">{{ $totalUser }}</dd>
                        </div>
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                            <dt class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                <span class="material-symbols-outlined !text-lg text-gray-500">{{ UserRole::Admin->icon() }}</span>
                                Admin
                            </dt>
                            <dd class="text-lg font-bold text-gray-900">{{ $totalUser - $totalMahasiswa - $totalStaff }}</dd>
                        </div>
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                            <dt class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                <span class="material-symbols-outlined !text-lg text-gray-500">{{ UserRole::Staff->icon() }}</span>
                                Staff
                            </dt>
                            <dd class="text-lg font-bold text-gray-900">{{ $totalStaff }}</dd>
                        </div>
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                            <dt class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                <span class="material-symbols-outlined !text-lg text-gray-500">{{ UserRole::Mahasiswa->icon() }}</span>
                                Mahasiswa
                            </dt>
                            <dd class="text-lg font-bold text-gray-900">{{ $totalMahasiswa }}</dd>
                        </div>
                    </dl>

                    <p class="mt-4 flex items-center gap-2 rounded-lg bg-brand-50 px-4 py-3 text-sm text-brand-800">
                        <span class="material-symbols-outlined !text-lg">category</span>
                        {{ $totalKategori }} kategori fasilitas terdaftar
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>