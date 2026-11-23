@php
    use App\Enums\StatusLaporan;
@endphp

<x-app-layout :title="'Detail '.$laporan->kode">
    <x-slot name="header">
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <a href="{{ route('staff.dashboard') }}" class="link-brand">Dashboard</a>
            <span class="material-symbols-outlined !text-base">chevron_right</span>
            <a href="{{ route('staff.laporan.index') }}" class="link-brand">Laporan</a>
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
                    @can('klaim', $laporan)
                        <div class="card">
                            <h2 class="text-base font-semibold text-gray-900">Klaim Laporan</h2>
                            <p class="mt-1 text-sm text-gray-500">Ambil laporan ini untuk Anda tangani.</p>
                            <form method="POST" action="{{ route('staff.laporan.klaim', $laporan) }}" class="mt-4">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn-primary w-full">
                                    <span class="material-symbols-outlined !text-lg">front_hand</span>
                                    Klaim Sekarang
                                </button>
                            </form>
                        </div>
                    @endcan

                    @can('proses', $laporan)
                        <div class="card">
                            <h2 class="text-base font-semibold text-gray-900">Tanggapi Laporan</h2>
                            <p class="mt-1 text-sm text-gray-500">Perbarui status bebas dan berikan penjelasan.</p>

                            <form method="POST" action="{{ route('staff.laporan.proses', $laporan) }}" class="mt-4 space-y-4">
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
                    @endcan

                    @unless ($laporan->ditanganiOleh)
                        @cannot('klaim', $laporan)
                            <p class="rounded-xl bg-gray-50 p-4 text-sm text-gray-500">
                                Laporan ini belum diklaim. Hubungi admin bila perlu penugasan khusus.
                            </p>
                        @endcannot
                    @endunless

                    @if ($laporan->status === StatusLaporan::Selesai)
                        <p class="rounded-xl bg-emerald-50 p-4 text-sm text-emerald-800">
                            Diselesaikan pada {{ $laporan->selesai_at?->translatedFormat('d F Y H:i') ?? '-' }}.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
