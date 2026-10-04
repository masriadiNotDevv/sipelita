@php
    use App\Enums\UserRole;
@endphp

<x-app-layout title="Edit Staff">
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <nav class="flex items-center gap-1 text-sm text-gray-500">
                    <a href="{{ route('admin.dashboard') }}" class="link-brand">Dashboard</a>
                    <span class="material-symbols-outlined !text-base">chevron_right</span>
                    <a href="{{ route('admin.staff.index') }}" class="link-brand">Kelola Staff</a>
                    <span class="material-symbols-outlined !text-base">chevron_right</span>
                    <span class="font-medium text-gray-700">Edit</span>
                </nav>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">Edit {{ $staff->userRole()->label() }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $staff->email }}</p>
            </div>
            <span class="badge {{ $staff->userRole()->badgeClass() }}">
                <span class="material-symbols-outlined !text-base">{{ $staff->userRole()->icon() }}</span>
                {{ $staff->userRole()->label() }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="card">
                @include('admin.staff._form', ['staff' => $staff])
            </div>
        </div>
    </div>
</x-app-layout>