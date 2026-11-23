@props(['laporan'])

<div class="card">
    <h2 class="text-base font-semibold text-gray-900">Detail Laporan</h2>

    <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
            <dt class="text-sm text-gray-500">Kode Laporan</dt>
            <dd class="mt-0.5 font-semibold text-gray-900">{{ $laporan->kode }}</dd>
        </div>
        <div>
            <dt class="text-sm text-gray-500">Pelapor</dt>
            <dd class="mt-0.5 font-semibold text-gray-900">{{ $laporan->user?->name ?? '-' }}</dd>
        </div>
        <div>
            <dt class="text-sm text-gray-500">Kategori</dt>
            <dd class="mt-0.5 font-semibold text-gray-900">{{ $laporan->kategori?->nama ?? 'Umum' }}</dd>
        </div>
        <div>
            <dt class="text-sm text-gray-500">Fasilitas</dt>
            <dd class="mt-0.5 font-semibold text-gray-900">{{ $laporan->fasilitas?->nama ?? '-' }}</dd>
        </div>
        <div>
            <dt class="text-sm text-gray-500">Lokasi</dt>
            <dd class="mt-0.5 font-semibold text-gray-900">{{ $laporan->lokasi }}</dd>
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
        <div>
            <dt class="text-sm text-gray-500">Diajukan</dt>
            <dd class="mt-0.5 font-semibold text-gray-900">
                {{ $laporan->created_at?->translatedFormat('d F Y H:i') ?? '-' }}
            </dd>
        </div>
    </dl>

    <div class="mt-6 border-t border-gray-100 pt-6">
        <h3 class="text-sm font-semibold text-gray-700">Deskripsi Masalah</h3>
        <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-gray-700">{{ $laporan->deskripsi }}</p>
    </div>

    @if ($laporan->bukti_foto)
        <div class="mt-6 border-t border-gray-100 pt-6">
            <h3 class="text-sm font-semibold text-gray-700">Bukti Foto</h3>
            <a href="{{ Storage::disk('public')->url($laporan->bukti_foto) }}" target="_blank" rel="noopener"
                class="mt-3 block overflow-hidden rounded-xl border border-gray-200">
                <img src="{{ Storage::disk('public')->url($laporan->bukti_foto) }}"
                    alt="Bukti foto laporan {{ $laporan->kode }}" class="w-full object-cover">
            </a>
        </div>
    @endif
</div>
