@php
    /** @var \App\Enums\PrioritasLaporan $prioritas */
    $selectedKategori = old('kategori_id', $laporan->kategori_id ?? '');
    $selectedFasilitas = old('fasilitas_id', $laporan->fasilitas_id ?? '');
    $selectedPrioritas = old('prioritas', $laporan->prioritas?->value ?? 'sedang');
    $deskripsi = old('deskripsi', $laporan->deskripsi ?? '');
@endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2">
        <div class="card">
            <h2 class="text-base font-semibold text-gray-900">Informasi Laporan</h2>
            <p class="mt-1 text-sm text-gray-500">
                Isi dengan detail agar staff dapat segera memverifikasi dan menangani keluhan Anda.
            </p>

            <div class="mt-6 space-y-5">
                <div>
                    <label for="judul" class="label">Judul Laporan <span class="text-red-500">*</span></label>
                    <input id="judul" name="judul" type="text" value="{{ old('judul', $laporan->judul ?? '') }}"
                        required minlength="10" maxlength="150"
                        class="input @error('judul') border-red-500 @enderror"
                        placeholder="Contoh: Lampu Koridor Lantai 2 Mati">
                    <div class="mt-1.5 flex items-center justify-between">
                        @error('judul')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-500">Ringkas dan spesifik, minimal 10 karakter.</p>
                        @enderror
                        <p class="text-xs text-gray-400">Maks. 150 karakter</p>
                    </div>
                </div>

                <div>
                    <label for="deskripsi" class="label">Deskripsi Masalah <span class="text-red-500">*</span></label>
                    <textarea id="deskripsi" name="deskripsi" rows="6" required minlength="20" maxlength="2000"
                        class="input @error('deskripsi') border-red-500 @enderror"
                        placeholder="Jelaskan kondisi yang Anda temukan, sejak kapan terjadi, dan dampaknya bagi Anda.">{{ $deskripsi }}</textarea>
                    <div class="mt-1.5 flex items-center justify-between">
                        @error('deskripsi')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-500">Minimal 20 karakter agar mudah dipahami.</p>
                        @enderror
                        <p class="text-xs text-gray-400">Maks. 2000 karakter</p>
                    </div>
                </div>

                <div>
                    <label for="lokasi" class="label">Lokasi <span class="text-red-500">*</span></label>
                    <input id="lokasi" name="lokasi" type="text" value="{{ old('lokasi', $laporan->lokasi ?? '') }}"
                        required maxlength="150"
                        class="input @error('lokasi') border-red-500 @enderror"
                        placeholder="Contoh: Gedung B Lantai 2, sisi timur">
                    @error('lokasi')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="card mt-6">
            <h2 class="text-base font-semibold text-gray-900">Kategori &amp; Prioritas</h2>
            <p class="mt-1 text-sm text-gray-500">Menentukan jenis layanan dan kecepatan penanganannya.</p>

            <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="kategori_id" class="label">Kategori <span class="text-red-500">*</span></label>
                    <select id="kategori_id" name="kategori_id" required
                        class="input @error('kategori_id') border-red-500 @enderror">
                        <option value="">Pilih kategori</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" @selected((string) $selectedKategori === (string) $kategori->id)>
                                {{ $kategori->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_id')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @elseif ($kategoris->isEmpty())
                        <p class="mt-1.5 text-sm text-amber-600">Belum ada kategori. Hubungi admin.</p>
                    @endif
                </div>

                <div>
                    <label for="fasilitas_id" class="label">Fasilitas <span class="text-xs text-gray-400">(opsional)</span></label>
                    <select id="fasilitas_id" name="fasilitas_id"
                        class="input @error('fasilitas_id') border-red-500 @enderror">
                        <option value="">Tidak spesifik</option>
                        @foreach ($fasilitas as $item)
                            <option value="{{ $item->id }}" @selected((string) $selectedFasilitas === (string) $item->id)>
                                {{ $item->nama }}{{ $item->lokasi ? ' — '.$item->lokasi : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('fasilitas_id')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @elseif ($fasilitas->isEmpty())
                        <p class="mt-1.5 text-xs text-gray-400">Data fasilitas belum tersedia.</p>
                    @endif
                </div>
            </div>

            <fieldset class="mt-6">
                <legend class="label">Prioritas <span class="text-red-500">*</span></legend>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @foreach ($prioritas as $pilihan)
                        @php
                            $keteranganPrioritas = match ($pilihan->value) {
                                'rendah' => 'Tidak mendesak, masih bisa ditunggu.',
                                'sedang' => 'Perlu perbaikan dalam waktu dekat.',
                                'tinggi' => 'Sanggu aktivitas belajar dan mengajar.',
                                default => 'Menganggu keselamatan penghuni kampus.',
                            };
                        @endphp
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-4 transition hover:border-brand-400 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                            <input type="radio" name="prioritas" value="{{ $pilihan->value }}"
                                @checked($selectedPrioritas === $pilihan->value)
                                class="mt-0.5 h-4 w-4 border-gray-300 text-brand-600 focus:ring-brand-600">
                            <span class="min-w-0">
                                <span class="flex items-center gap-1.5 text-sm font-semibold text-gray-900">
                                    <span class="material-symbols-outlined !text-base">{{ $pilihan->icon() }}</span>
                                    {{ $pilihan->label() }}
                                </span>
                                <span class="mt-0.5 block text-xs text-gray-500">{{ $keteranganPrioritas }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('prioritas')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </fieldset>
        </div>

        <div class="card mt-6">
            <h2 class="text-base font-semibold text-gray-900">Bukti Foto <span class="text-xs font-normal text-gray-400">(opsional)</span></h2>
            <p class="mt-1 text-sm text-gray-500">Maksimal 2 MB dengan format JPG, PNG, atau WEBP.</p>

            <div class="mt-6">
                <label for="bukti_foto"
                    class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center transition hover:border-brand-400 hover:bg-brand-50">
                    <span class="material-symbols-outlined !text-3xl text-gray-400">add_a_photo</span>
                    <span class="mt-3 text-sm font-medium text-gray-700">Klik untuk pilih foto</span>
                    <span class="mt-1 text-xs text-gray-500">atau letakkan berkas di sini</span>
                    <input id="bukti_foto" name="bukti_foto" type="file" accept="image/jpeg,image/png,image/webp"
                        class="sr-only">
                </label>
                @error('bukti_foto')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @endif
                <p id="bukti_foto-nama" class="mt-2 hidden text-sm text-gray-600"></p>
            </div>
        </div>
    </div>

    <div>
        <div class="card lg:sticky lg:top-24">
            <h2 class="text-base font-semibold text-gray-900">Ringkasan</h2>

            <dl class="mt-5 space-y-4 text-sm">
                <div>
                    <dt class="text-gray-500">Pelapor</dt>
                    <dd class="mt-0.5 font-medium text-gray-900">{{ auth()->user()->name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">NIM</dt>
                    <dd class="mt-0.5 font-medium text-gray-900">{{ auth()->user()->nim ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Program Studi</dt>
                    <dd class="mt-0.5 font-medium text-gray-900">{{ auth()->user()->program_studi ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Status Awal</dt>
                    <dd class="mt-1">
                        <span class="badge bg-amber-100 text-amber-800">
                            <span class="material-symbols-outlined !text-sm">hourglass_empty</span>
                            Menunggu
                        </span>
                    </dd>
                </div>
            </dl>

            <div class="mt-6 rounded-xl bg-sky-50 p-4">
                <p class="flex items-center gap-1.5 text-sm font-semibold text-sky-900">
                    <span class="material-symbols-outlined !text-lg">info</span>
                    Setelah dikirim
                </p>
                <p class="mt-1.5 text-xs leading-relaxed text-sky-800">
                    Laporan Anda akan masuk antrean staff. Anda dapat memantau perkembangannya
                    melalui halaman Laporan Saya.
                </p>
            </div>

            <div class="mt-6 space-y-3">
                <button type="submit" class="btn-primary w-full">
                    <span class="material-symbols-outlined !text-lg">send</span>
                    Kirim Laporan
                </button>
                <a href="{{ route('mahasiswa.laporan.index') }}" class="btn-secondary w-full">Batal</a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.getElementById('bukti_foto')?.addEventListener('change', function (event) {
            const target = document.getElementById('bukti_foto-nama');
            const file = event.target.files?.[0];

            if (!file) {
                if (target) {
                    target.classList.add('hidden');
                }

                return;
            }

            if (target) {
                target.textContent = file.name + ' (' + Math.round(file.size / 1024) + ' KB)';
                target.classList.remove('hidden');
            }
        });
    </script>
@endpush