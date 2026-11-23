<x-app-layout :title="'Detail '.$laporan->kode">
    <x-slot name="header">
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <a href="{{ route('mahasiswa.dashboard') }}" class="link-brand">Dashboard</a>
            <span class="material-symbols-outlined !text-base">chevron_right</span>
            <a href="{{ route('mahasiswa.laporan.index') }}" class="link-brand">Laporan Saya</a>
            <span class="material-symbols-outlined !text-base">chevron_right</span>
            <span class="font-medium text-gray-700">{{ $laporan->kode }}</span>
        </nav>
        <div class="mt-1 flex flex-wrap items-center gap-3">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ $laporan->judul }}</h1>
            <span class="badge {{ $laporan->status->badgeClass() }}">
                <span class="material-symbols-outlined !text-sm">{{ $laporan->status->icon() }}</span>
                {{ $laporan->status->label() }}
            </span>
        </div>
        <p class="mt-1 text-sm text-gray-500">Diajukan pada {{ $laporan->created_at?->translatedFormat('d F Y H:i') }}</p>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                    <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                    <p class="text-sm font-medium text-emerald-800">{{ session('status') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 space-y-6">
                    <div class="card">
                        <h2 class="text-base font-semibold text-gray-900">Detail Laporan</h2>

                        <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm text-gray-500">Kode Laporan</dt>
                                <dd class="mt-0.5 font-semibold text-gray-900">{{ $laporan->kode }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Kategori</dt>
                                <dd class="mt-0.5 font-semibold text-gray-900">{{ $laporan->kategori?->nama ?? 'Umum' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Lokasi</dt>
                                <dd class="mt-0.5 font-semibold text-gray-900">{{ $laporan->lokasi }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Fasilitas</dt>
                                <dd class="mt-0.5 font-semibold text-gray-900">{{ $laporan->fasilitas?->nama ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Prioritas</dt>
                                <dd class="mt-1">
                                    <span class="badge {{ $laporan->prioritas->badgeClass() }}">
                                        <span class="material-symbols-outlined !text-sm">{{ $laporan->prioritas->icon() }}</span>
                                        {{ $laporan->prioritas->label() }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Ditangani Oleh</dt>
                                <dd class="mt-0.5 font-semibold text-gray-900">{{ $laporan->ditanganiOleh?->name ?? 'Belum ditugaskan' }}</dd>
                            </div>
                        </dl>

                        <div class="mt-6 border-t border-gray-100 pt-6">
                            <h3 class="text-sm font-semibold text-gray-700">Deskripsi Masalah</h3>
                            <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-gray-700">{{ $laporan->deskripsi }}</p>
                        </div>
                    </div>

                    @if ($laporan->bukti_foto)
                        <div class="card">
                            <h2 class="text-base font-semibold text-gray-900">Bukti Foto</h2>
                            <a href="{{ Storage::disk('public')->url($laporan->bukti_foto) }}" target="_blank" rel="noopener"
                                class="mt-4 block overflow-hidden rounded-xl border border-gray-200">
                                <img src="{{ Storage::disk('public')->url($laporan->bukti_foto) }}"
                                    alt="Bukti foto laporan {{ $laporan->kode }}" class="w-full object-cover">
                            </a>
                        </div>
                    @endif

                    <div class="card">
                        <h2 class="text-base font-semibold text-gray-900">Riwayat Tanggapan</h2>

                        @if ($laporan->tanggapan->isEmpty())
                            <p class="mt-4 rounded-xl bg-gray-50 p-4 text-sm text-gray-500">
                                Belum ada tanggapan dari staff untuk laporan ini.
                            </p>
                        @else
                            <ol class="mt-5 space-y-4">
                                @foreach ($laporan->tanggapan->sortBy('created_at') as $tanggapan)
                                    <li class="relative border-l-2 border-brand-200 pl-5">
                                        <span class="absolute -left-[7px] top-1.5 h-3 w-3 rounded-full bg-brand-500"></span>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="text-sm font-semibold text-gray-900">{{ $tanggapan->user?->name ?? 'Staff' }}</p>
                                            <p class="text-xs text-gray-400">{{ $tanggapan->created_at?->translatedFormat('d M Y H:i') }}</p>
                                        </div>
                                        @if ($tanggapan->status_sebelum && $tanggapan->status_sesudah)
                                            <p class="mt-1 text-xs text-gray-500">
                                                Status:
                                                <span class="font-medium text-gray-700">{{ $tanggapan->status_sebelum->label() }}</span>
                                                <span class="material-symbols-outlined !inline !text-xs">arrow_forward</span>
                                                <span class="font-medium text-gray-700">{{ $tanggapan->status_sesudah->label() }}</span>
                                            </p>
                                        @endif
                                        <p class="mt-1.5 whitespace-pre-line text-sm leading-relaxed text-gray-700">{{ $tanggapan->isi }}</p>
                                    </li>
                                @endforeach
                            </ol>
                        @endif
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="card">
                        <h2 class="text-base font-semibold text-gray-900">Progres Penanganan</h2>

                        @php
                            $urutan = [
                                \App\Enums\StatusLaporan::Menunggu,
                                \App\Enums\StatusLaporan::Diproses,
                                \App\Enums\StatusLaporan::DalamPerbaikan,
                                \App\Enums\StatusLaporan::Selesai,
                            ];
                        @endphp

                        <ol class="mt-5 space-y-4">
                            @foreach ($urutan as $tahap)
                                <li class="flex items-start gap-3">
                                    <span @class([
                                        'flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold',
                                        'bg-emerald-100 text-emerald-700' => $laporan->status === $tahap && $laporan->status === \App\Enums\StatusLaporan::Selesai,
                                        'bg-brand-100 text-brand-700' => $laporan->status === $tahap,
                                        'bg-gray-100 text-gray-400' => $laporan->status !== $tahap,
                                    ])>
                                        <span class="material-symbols-outlined !text-sm">{{ $tahap->icon() }}</span>
                                    </span>
                                    <div class="min-w-0">
                                        <p @class([
                                            'text-sm font-medium',
                                            'text-gray-900' => $laporan->status === $tahap,
                                            'text-gray-500' => $laporan->status !== $tahap,
                                        ])>{{ $tahap->label() }}</p>
                                        @if ($laporan->status === $tahap)
                                            <p class="text-xs text-gray-500">Status saat ini</p>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ol>

                        @if ($laporan->status === \App\Enums\StatusLaporan::Ditolak)
                            <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4">
                                <p class="flex items-center gap-1.5 text-sm font-semibold text-red-800">
                                    <span class="material-symbols-outlined !text-lg">cancel</span>
                                    Laporan Ditolak
                                </p>
                                <p class="mt-1.5 text-xs text-red-700">
                                    Silakan baca tanggapan staff di riwayat tanggapan.
                                </p>
                            </div>
                        @endif

                        @if ($laporan->selesai_at)
                            <p class="mt-5 text-xs text-gray-500">
                                Diselesaikan pada {{ $laporan->selesai_at->translatedFormat('d F Y H:i') }}
                            </p>
                        @endif
                    </div>

                    <div class="card">
                        <div class="space-y-3">
                            <a href="{{ route('mahasiswa.laporan.index') }}" class="btn-secondary w-full">
                                <span class="material-symbols-outlined !text-lg">arrow_back</span>
                                Kembali ke Daftar
                            </a>
                            @if ($laporan->status === \App\Enums\StatusLaporan::Selesai)
                                <a href="{{ route('mahasiswa.laporan.create') }}" class="btn-primary w-full">
                                    <span class="material-symbols-outlined !text-lg">add_circle</span>
                                    Buat Laporan Baru
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>