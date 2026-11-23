<x-app-layout title="Laporan Saya">
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <nav class="flex items-center gap-1 text-sm text-gray-500">
                    <a href="{{ route('mahasiswa.dashboard') }}" class="link-brand">Dashboard</a>
                    <span class="material-symbols-outlined !text-base">chevron_right</span>
                    <span class="font-medium text-gray-700">Laporan Saya</span>
                </nav>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">Laporan Saya</h1>
                <p class="mt-1 text-sm text-gray-500">Pantau seluruh laporan yang pernah Anda ajukan beserta status penanganannya.</p>
            </div>
            <a href="{{ route('mahasiswa.laporan.create') }}" class="btn-primary">
                <span class="material-symbols-outlined !text-lg">add_circle</span>
                Buat Laporan
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Laporan</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $totalLaporan }}</p>
                        </div>
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-100 text-brand-700">
                            <span class="material-symbols-outlined">assignment</span>
                        </span>
                    </div>
                </div>
                <div class="card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Menunggu</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $totalMenunggu }}</p>
                        </div>
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                            <span class="material-symbols-outlined">hourglass_empty</span>
                        </span>
                    </div>
                </div>
                <div class="card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Diproses</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $totalDiproses }}</p>
                        </div>
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-100 text-sky-700">
                            <span class="material-symbols-outlined">pending_actions</span>
                        </span>
                    </div>
                </div>
                <div class="card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Selesai</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $totalSelesai }}</p>
                        </div>
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            <span class="material-symbols-outlined">task_alt</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="card mt-6">
                <form method="GET" action="{{ route('mahasiswa.laporan.index') }}" class="flex flex-wrap items-end gap-3">
                    <div class="min-w-[16rem] flex-1">
                        <label for="cari" class="label">Cari Laporan</label>
                        <input id="cari" name="cari" type="search" value="{{ $cari }}" class="input"
                            placeholder="Cari judul, kode, atau lokasi">
                    </div>
                    <div class="min-w-[12rem]">
                        <label for="status" class="label">Status</label>
                        <select id="status" name="status" class="input">
                            <option value="">Semua Status</option>
                            @foreach ($statusTersedia as $pilihan)
                                <option value="{{ $pilihan->value }}" @selected($statusTerpilih === $pilihan->value)>
                                    {{ $pilihan->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-secondary">
                        <span class="material-symbols-outlined !text-lg">search</span>
                        Terapkan
                    </button>
                    @if ($cari !== '' || $statusTerpilih !== '')
                        <a href="{{ route('mahasiswa.laporan.index') }}" class="btn-ghost">Reset</a>
                    @endif
                </form>
            </div>

            <div class="mt-6 space-y-4">
                @forelse ($laporans as $laporan)
                    <div class="card transition hover:border-brand-300">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="badge bg-gray-100 text-gray-700">{{ $laporan->kode }}</span>
                                    <span class="badge {{ $laporan->status->badgeClass() }}">
                                        <span class="material-symbols-outlined !text-sm">{{ $laporan->status->icon() }}</span>
                                        {{ $laporan->status->label() }}
                                    </span>
                                    <span class="badge {{ $laporan->prioritas->badgeClass() }}">
                                        <span class="material-symbols-outlined !text-sm">{{ $laporan->prioritas->icon() }}</span>
                                        {{ $laporan->prioritas->label() }}
                                    </span>
                                </div>

                                <h2 class="mt-3 text-base font-semibold text-gray-900">{{ $laporan->judul }}</h2>
                                <p class="mt-1 line-clamp-2 text-sm text-gray-600">{{ $laporan->deskripsi }}</p>

                                <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500">
                                    <span class="inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined !text-base">category</span>
                                        {{ $laporan->kategori?->nama ?? 'Umum' }}
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined !text-base">location_on</span>
                                        {{ $laporan->lokasi }}
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined !text-base">calendar_today</span>
                                        {{ $laporan->tanggal_pendek }}
                                    </span>
                                </div>
                            </div>

                            <a href="{{ route('mahasiswa.laporan.show', $laporan) }}" class="btn-secondary shrink-0">
                                Detail
                                <span class="material-symbols-outlined !text-lg">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="card">
                        <div class="py-12 text-center">
                            <span class="material-symbols-outlined !text-5xl text-gray-300">assignment</span>
                            <p class="mt-3 text-sm text-gray-500">
                                @if ($cari !== '' || $statusTerpilih !== '')
                                    Tidak ada laporan yang cocok dengan filter tersebut.
                                @else
                                    Anda belum mengajukan laporan. Mulai dengan membuat laporan pertama Anda.
                                @endif
                            </p>
                            @if ($cari === '' && $statusTerpilih === '')
                                <a href="{{ route('mahasiswa.laporan.create') }}" class="btn-primary mt-5">
                                    <span class="material-symbols-outlined !text-lg">add_circle</span>
                                    Buat Laporan
                                </a>
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($laporans->hasPages())
                <div class="mt-6">
                    {{ $laporans->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>