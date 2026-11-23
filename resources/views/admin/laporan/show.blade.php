@php
    use App\Enums\StatusLaporan;
@endphp

<x-app-layout :title="'Kelola '.$laporan->kode">
    <x-slot name="header">
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <a href="{{ route('admin.dashboard') }}" class="link-brand">Dashboard</a>
            <span class="material-symbols-outlined !text-base">chevron_right</span>
            <a href="{{ route('admin.laporan.index') }}" class="link-brand">Kelola Laporan</a>
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
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <x-laporan-detail :laporan="$laporan" />

                    <div class="card">
                        <h2 class="text-base font-semibold text-gray-900">Riwayat Tanggapan</h2>
                        @if ($laporan->tanggapan->isEmpty())
                            <p class="mt-4 rounded-xl bg-gray-50 p-4 text-sm text-gray-500">
                                Belum ada tanggapan untuk laporan ini.
                            </p>
                        @else
                            <ol class="mt-5 space-y-4">
                                @foreach ($laporan->tanggapan as $tanggapan)
                                    <li class="relative border-l-2 border-brand-200 pl-5">
                                        <span class="absolute -left-[7px] top-1.5 h-3 w-3 rounded-full bg-brand-500"></span>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="text-sm font-semibold text-gray-900">{{ $tanggapan->user?->name ?? 'Pengguna' }}</p>
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
                        <h2 class="text-base font-semibold text-gray-900">Penangani</h2>
                        <p class="mt-1 text-sm text-gray-500">Tugaskan staff atau lepaskan penugasan.</p>

                        <form method="POST" action="{{ route('admin.laporan.penangani', $laporan) }}" class="mt-4 space-y-4">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label for="penangani_id" class="label">Staff Penangani</label>
                                <select id="penangani_id" name="penangani_id" class="input @error('penangani_id') input-error @enderror">
                                    <option value="">Belum Ditugaskan</option>
                                    @foreach ($staffTersedia as $staff)
                                        <option value="{{ $staff->id }}" @selected(old('penangani_id', $laporan->ditangani_oleh) == $staff->id)>
                                            {{ $staff->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('penangani_id')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="btn-secondary w-full">
                                <span class="material-symbols-outlined !text-lg">person_pin</span>
                                Simpan Penangani
                            </button>
                        </form>
                    </div>

                    <div class="card">
                        <h2 class="text-base font-semibold text-gray-900">Tanggapi Laporan</h2>
                        <p class="mt-1 text-sm text-gray-500">Perbarui status bebas dan berikan penjelasan.</p>

                        <form method="POST" action="{{ route('admin.laporan.proses', $laporan) }}" class="mt-4 space-y-4">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label for="status" class="label">Status</label>
                                <select id="status" name="status" class="input @error('status') input-error @enderror">
                                    @foreach ($statusTersedia as $status)
                                        <option value="{{ $status->value }}" @selected(old('status', $laporan->status->value) === $status->value)>
                                            {{ $status->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="isi" class="label">Tanggapan</label>
                                <textarea id="isi" name="isi" rows="5" class="input @error('isi') input-error @enderror"
                                    placeholder="Tuliskan hasil pengecekan atau tindakan yang dilakukan...">{{ old('isi') }}</textarea>
                                @error('isi')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="btn-primary w-full">
                                <span class="material-symbols-outlined !text-lg">send</span>
                                Simpan Penanganan
                            </button>
                        </form>
                    </div>

                    <div class="card space-y-3">
                        <a href="{{ route('admin.laporan.edit', $laporan) }}" class="btn-secondary w-full">
                            <span class="material-symbols-outlined !text-lg">edit</span>
                            Edit Detail
                        </a>
                        @can('delete', $laporan)
                            <form method="POST" action="{{ route('admin.laporan.destroy', $laporan) }}"
                                onsubmit="return confirm('Arsipkan laporan {{ $laporan->kode }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger w-full">
                                    <span class="material-symbols-outlined !text-lg">archive</span>
                                    Arsipkan Laporan
                                </button>
                            </form>
                        @endcan
                        @if ($laporan->status === StatusLaporan::Selesai)
                            <p class="rounded-xl bg-emerald-50 p-4 text-sm text-emerald-800">
                                Diselesaikan pada {{ $laporan->selesai_at?->translatedFormat('d F Y H:i') ?? '-' }}.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
