<x-app-layout title="Arsip Laporan">
    <x-slot name="header">
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <a href="{{ route('admin.dashboard') }}" class="link-brand">Dashboard</a>
            <span class="material-symbols-outlined !text-base">chevron_right</span>
            <a href="{{ route('admin.laporan.index') }}" class="link-brand">Kelola Laporan</a>
            <span class="material-symbols-outlined !text-base">chevron_right</span>
            <span class="font-medium text-gray-700">Arsip</span>
        </nav>
        <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">Arsip Laporan</h1>
        <p class="mt-1 text-sm text-gray-500">Laporan yang diarsipkan dapat dipulihkan kembali ke daftar aktif.</p>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="card">
                <form method="GET" action="{{ route('admin.laporan.arsip') }}" class="flex flex-wrap items-end gap-3">
                    <div class="min-w-[16rem] flex-1">
                        <label for="cari" class="label">Cari Arsip</label>
                        <input id="cari" name="cari" type="search" value="{{ $cari }}" class="input"
                            placeholder="Kode atau judul laporan">
                    </div>
                    <button type="submit" class="btn-secondary">
                        <span class="material-symbols-outlined !text-lg">search</span>
                        Cari
                    </button>
                    @if ($cari !== '')
                        <a href="{{ route('admin.laporan.arsip') }}" class="btn-ghost">Reset</a>
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
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">Diarsipkan</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($laporans as $laporan)
                                <tr>
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-gray-900">{{ $laporan->judul }}</p>
                                        <p class="text-xs text-gray-500">{{ $laporan->kode }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ $laporan->user?->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $laporan->deleted_at?->translatedFormat('d F Y H:i') ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <form method="POST" action="{{ route('admin.laporan.restore', $laporan) }}"
                                            class="flex justify-end">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn-secondary">
                                                <span class="material-symbols-outlined !text-lg">restore</span>
                                                Pulihkan
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">
                                        Belum ada laporan diarsipkan.
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
