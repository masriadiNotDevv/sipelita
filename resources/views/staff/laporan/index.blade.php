<x-app-layout title="Laporan Staff">
    <x-slot name="header">
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <a href="{{ route('staff.dashboard') }}" class="link-brand">Dashboard</a>
            <span class="material-symbols-outlined !text-base">chevron_right</span>
            <span class="font-medium text-gray-700">Laporan</span>
        </nav>
        <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">Laporan Masuk</h1>
        <p class="mt-1 text-sm text-gray-500">Lihat seluruh laporan, klaim yang tersedia, dan tindak lanjuti penanganan.</p>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
                @foreach ([
                    ['Total', $totalLaporan, 'assignment'],
                    ['Menunggu', $totalMenunggu, 'hourglass_empty'],
                    ['Diproses', $totalDiproses, 'pending_actions'],
                    ['Selesai', $totalSelesai, 'task_alt'],
                    ['Ditangani Saya', $totalDitanganiSaya, 'person_pin'],
                ] as $stat)
                    <div class="card">
                        <p class="text-xs font-medium text-gray-500">{{ $stat[0] }}</p>
                        <div class="mt-2 flex items-center justify-between gap-2">
                            <p class="text-2xl font-bold text-gray-900">{{ $stat[1] }}</p>
                            <span class="material-symbols-outlined text-brand-600">{{ $stat[2] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card">
                <form method="GET" action="{{ route('staff.laporan.index') }}" class="flex flex-wrap items-end gap-3">
                    <div class="min-w-[16rem] flex-1">
                        <label for="cari" class="label">Cari Laporan</label>
                        <input id="cari" name="cari" type="search" value="{{ $cari }}" class="input"
                            placeholder="Kode, judul, lokasi, atau pelapor">
                    </div>
                    <div class="w-full sm:w-48">
                        <label for="status" class="label">Status</label>
                        <select id="status" name="status" class="input">
                            <option value="">Semua Status</option>
                            @foreach ($statusTersedia as $status)
                                <option value="{{ $status->value }}" @selected($statusTerpilih === $status->value)>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-secondary">
                        <span class="material-symbols-outlined !text-lg">search</span>
                        Cari
                    </button>
                    @if ($cari !== '' || $statusTerpilih !== '')
                        <a href="{{ route('staff.laporan.index') }}" class="btn-ghost">Reset</a>
                    @endif
                </form>
            </div>

            <div class="card overflow-hidden p-0">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">Laporan</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">Pelapor</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">Penangani</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">Status</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($laporans as $laporan)
                                <tr>
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-gray-900">{{ $laporan->judul }}</p>
                                        <p class="text-xs text-gray-500">{{ $laporan->kode }} &middot; {{ $laporan->lokasi }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ $laporan->user?->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $laporan->ditanganiOleh?->name ?? 'Belum diklaim' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="badge {{ $laporan->status->badgeClass() }}">
                                            <span class="material-symbols-outlined !text-sm">{{ $laporan->status->icon() }}</span>
                                            {{ $laporan->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('staff.laporan.show', $laporan) }}" class="link-brand font-medium">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                        Tidak ada laporan yang cocok.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($laporans->hasPages())
                    <div class="border-t border-gray-200 px-6 py-4">{{ $laporans->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
