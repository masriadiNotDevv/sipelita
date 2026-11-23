<x-app-layout title="Edit Laporan">
    <x-slot name="header">
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <a href="{{ route('admin.dashboard') }}" class="link-brand">Dashboard</a>
            <span class="material-symbols-outlined !text-base">chevron_right</span>
            <a href="{{ route('admin.laporan.index') }}" class="link-brand">Kelola Laporan</a>
            <span class="material-symbols-outlined !text-base">chevron_right</span>
            <a href="{{ route('admin.laporan.show', $laporan) }}" class="link-brand">{{ $laporan->kode }}</a>
            <span class="material-symbols-outlined !text-base">chevron_right</span>
            <span class="font-medium text-gray-700">Edit</span>
        </nav>
        <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">Edit Detail Laporan</h1>
        <p class="mt-1 text-sm text-gray-500">Perbarui informasi laporan tanpa mengubah status penanganan.</p>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="card">
                <form method="POST" action="{{ route('admin.laporan.update', $laporan) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5">
                        <div>
                            <label for="judul" class="label">Judul Laporan</label>
                            <input id="judul" name="judul" type="text" value="{{ old('judul', $laporan->judul) }}"
                                class="input @error('judul') input-error @enderror">
                            @error('judul')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="deskripsi" class="label">Deskripsi Masalah</label>
                            <textarea id="deskripsi" name="deskripsi" rows="6"
                                class="input @error('deskripsi') input-error @enderror">{{ old('deskripsi', $laporan->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label for="lokasi" class="label">Lokasi</label>
                                <input id="lokasi" name="lokasi" type="text" value="{{ old('lokasi', $laporan->lokasi) }}"
                                    class="input @error('lokasi') input-error @enderror">
                                @error('lokasi')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="prioritas" class="label">Prioritas</label>
                                <select id="prioritas" name="prioritas" class="input @error('prioritas') input-error @enderror">
                                    @foreach ($prioritasTersedia as $prioritas)
                                        <option value="{{ $prioritas->value }}"
                                            @selected(old('prioritas', $laporan->prioritas->value) === $prioritas->value)>
                                            {{ $prioritas->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('prioritas')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="kategori_id" class="label">Kategori</label>
                                <select id="kategori_id" name="kategori_id" class="input @error('kategori_id') input-error @enderror">
                                    @foreach ($kategoris as $kategori)
                                        <option value="{{ $kategori->id }}"
                                            @selected(old('kategori_id', $laporan->kategori_id) == $kategori->id)>
                                            {{ $kategori->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kategori_id')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="fasilitas_id" class="label">Fasilitas</label>
                                <select id="fasilitas_id" name="fasilitas_id" class="input @error('fasilitas_id') input-error @enderror">
                                    <option value="">Tidak ada</option>
                                    @foreach ($fasilitas as $fasilitasItem)
                                        <option value="{{ $fasilitasItem->id }}"
                                            @selected(old('fasilitas_id', $laporan->fasilitas_id) == $fasilitasItem->id)>
                                            {{ $fasilitasItem->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('fasilitas_id')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-wrap items-center justify-end gap-3 border-t border-gray-100 pt-6">
                        <a href="{{ route('admin.laporan.show', $laporan) }}" class="btn-ghost">Batal</a>
                        <button type="submit" class="btn-primary">
                            <span class="material-symbols-outlined !text-lg">save</span>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
