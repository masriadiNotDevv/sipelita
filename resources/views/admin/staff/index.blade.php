@php
    use App\Enums\UserRole;
@endphp

<x-app-layout title="Kelola Staff">
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <nav class="flex items-center gap-1 text-sm text-gray-500">
                    <a href="{{ route('admin.dashboard') }}" class="link-brand">Dashboard</a>
                    <span class="material-symbols-outlined !text-base">chevron_right</span>
                    <span class="font-medium text-gray-700">Kelola Staff</span>
                </nav>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">Kelola Staff</h1>
                <p class="mt-1 text-sm text-gray-500">Tambah, perbarui, dan hapus akun admin serta staff SIPELITA.</p>
            </div>
            <a href="{{ route('admin.staff.create') }}" class="btn-primary">
                <span class="material-symbols-outlined !text-lg">person_add</span>
                Tambah Akun
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ UserRole::Admin->label() }}</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $totalAdmin }}</p>
                        </div>
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-100 text-brand-700">
                            <span class="material-symbols-outlined">{{ UserRole::Admin->icon() }}</span>
                        </span>
                    </div>
                </div>
                <div class="card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ UserRole::Staff->label() }}</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $totalStaff }}</p>
                        </div>
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-100 text-sky-700">
                            <span class="material-symbols-outlined">{{ UserRole::Staff->icon() }}</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="card mt-6">
                <form method="GET" action="{{ route('admin.staff.index') }}" class="flex flex-wrap items-end gap-3">
                    <div class="min-w-[16rem] flex-1">
                        <label for="cari" class="label">Cari Akun</label>
                        <input id="cari" name="cari" type="search" value="{{ $cari }}" class="input"
                            placeholder="Cari berdasarkan nama atau email">
                    </div>
                    <button type="submit" class="btn-secondary">
                        <span class="material-symbols-outlined !text-lg">search</span>
                        Cari
                    </button>
                    @if ($cari !== '')
                        <a href="{{ route('admin.staff.index') }}" class="btn-ghost">Reset</a>
                    @endif
                </form>
            </div>

            <div class="card mt-6 overflow-hidden p-0">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left font-semibold text-gray-700">Akun</th>
                                <th scope="col" class="px-6 py-3 text-left font-semibold text-gray-700">Peran</th>
                                <th scope="col" class="px-6 py-3 text-left font-semibold text-gray-700">Bergabung</th>
                                <th scope="col" class="px-6 py-3 text-right font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($daftar as $akun)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-600 text-xs font-semibold text-white">
                                                {{ $akun->initials() }}
                                            </span>
                                            <div class="min-w-0">
                                                <p class="truncate font-semibold text-gray-900">{{ $akun->name }}</p>
                                                <p class="truncate text-xs text-gray-500">{{ $akun->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="badge {{ $akun->userRole()->badgeClass() }}">
                                            <span class="material-symbols-outlined !text-sm">{{ $akun->userRole()->icon() }}</span>
                                            {{ $akun->userRole()->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $akun->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('admin.staff.edit', $akun) }}"
                                                class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-medium text-gray-700 transition hover:bg-gray-100">
                                                <span class="material-symbols-outlined !text-base">edit</span>
                                                Edit
                                            </a>
                                            @unless (auth()->user()->is($akun))
                                                <form method="POST" action="{{ route('admin.staff.destroy', $akun) }}"
                                                    onsubmit="return confirm('Hapus akun {{ $akun->name }}? Tindakan ini tidak dapat dibatalkan.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-medium text-red-700 transition hover:bg-red-50">
                                                        <span class="material-symbols-outlined !text-base">delete</span>
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endunless
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <span class="material-symbols-outlined !text-4xl text-gray-300">group_off</span>
                                        <p class="mt-2 text-gray-500">
                                            @if ($cari !== '')
                                                Tidak ada akun yang cocok dengan kata kunci "{{ $cari }}".
                                            @else
                                                Belum ada akun admin atau staff.
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($daftar->hasPages())
                    <div class="border-t border-gray-200 px-6 py-4">
                        {{ $daftar->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>