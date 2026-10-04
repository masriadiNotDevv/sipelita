@php
    $form = [
        'action' => $staff->exists ? route('admin.staff.update', $staff) : route('admin.staff.store'),
        'method' => $staff->exists ? 'PATCH' : 'POST',
        'staff' => $staff->exists ? $staff : null,
        'submit' => $staff->exists ? 'Simpan Perubahan' : 'Tambah Akun',
    ];
@endphp

<form method="POST" action="{{ $form['action'] }}" class="space-y-6">
    @csrf
    @if ($form['method'] === 'PATCH')
        @method('PATCH')
    @endif

    <div>
        <label for="name" class="label">Nama Lengkap</label>
        <input id="name" name="name" type="text" value="{{ old('name', $form['staff']?->name) }}" required
            autofocus autocomplete="name" class="input" placeholder="Contoh: Siti Rahma">
        @error('name')
            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email" class="label">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $form['staff']?->email) }}" required
            autocomplete="username" class="input" placeholder="Contoh: siti@kampus.test">
        @error('email')
            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <label for="password" class="label">
                Password
                @if ($form['staff'])
                    <span class="font-normal text-gray-500">(kosongkan bila tidak diubah)</span>
                @endif
            </label>
            <input id="password" name="password" type="password" @if (! $form['staff']) required @endif
                autocomplete="new-password" class="input" placeholder="Minimal 8 karakter">
            @error('password')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="label">Konfirmasi Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" @if (! $form['staff']) required @endif
                autocomplete="new-password" class="input" placeholder="Ulangi password">
            @error('password_confirmation')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="role" class="label">Peran</label>
        <select id="role" name="role" required class="input">
            @foreach (\App\Enums\UserRole::cases() as $pilihan)
                @continue($pilihan === \App\Enums\UserRole::Mahasiswa)
                <option value="{{ $pilihan->value }}" @selected(old('role', $form['staff']?->role->value ?? \App\Enums\UserRole::Staff->value) === $pilihan->value)>
                    {{ $pilihan->label() }} - {{ $pilihan->description() }}
                </option>
            @endforeach
        </select>
        @error('role')
            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-1.5 text-xs text-gray-500">
            Akun mahasiswa dibuat otomatis melalui halaman pendaftaran.
        </p>
    </div>

    <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
        <button type="submit" class="btn-primary">
            <span class="material-symbols-outlined !text-lg">save</span>
            {{ $form['submit'] }}
        </button>
        <a href="{{ route('admin.staff.index') }}" class="btn-secondary">Batal</a>
    </div>
</form>