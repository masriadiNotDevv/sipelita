@php
    use App\Enums\UserRole;

    $staff = new User;
@endphp

<x-app-layout title="Tambah Staff">
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <nav class="flex items-center gap-1 text-sm text-gray-500">
                    <a href="{{ route('admin.dashboard') }}" class="link-brand">Dashboard</a>
                    <span class="material-symbols-outlined !text-base">chevron_right</span>
                    <a href="{{ route('admin.staff.index') }}" class="link-brand">Kelola Staff</a>
                    <span class="material-symbols-outlined !text-base">chevron_right</span>
                    <span class="font-medium text-gray-700">Tambah</span>
                </nav>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">Tambah Akun Staff</h1>
                <p class="mt-1 text-sm text-gray-500">Buat akun staff baru agar dapat menangani laporan fasilitas.</p>
            </div>
            <span class="badge bg-brand-100 text-brand-800">
                <span class="material-symbols-outlined !text-base">{{ UserRole::Staff->icon() }}</span>
                {{ UserRole::Staff->label() }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="card">
                <div class="mb-6 flex items-start gap-3 rounded-xl bg-brand-50 px-4 py-3 text-sm text-brand-800">
                    <span class="material-symbols-outlined !text-lg">info</span>
                    <p>Akun yang dibuat akan langsung aktif dan dapat login tanpa verifikasi email tambahan.</p>
                </div>

                @include('admin.staff._form', ['staff' => $staff])
            </div>
        </div>
    </div>
</x-app-layout>