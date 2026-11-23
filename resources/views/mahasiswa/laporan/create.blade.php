<x-app-layout title="Buat Laporan">
    <x-slot name="header">
        <nav class="flex items-center gap-1 text-sm text-gray-500">
            <a href="{{ route('mahasiswa.dashboard') }}" class="link-brand">Dashboard</a>
            <span class="material-symbols-outlined !text-base">chevron_right</span>
            <a href="{{ route('mahasiswa.laporan.index') }}" class="link-brand">Laporan Saya</a>
            <span class="material-symbols-outlined !text-base">chevron_right</span>
            <span class="font-medium text-gray-700">Buat Laporan</span>
        </nav>
        <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">Buat Laporan</h1>
        <p class="mt-1 text-sm text-gray-500">Sampaikan keluhan atau kerusakan fasilitas yang Anda temukan.</p>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('mahasiswa.laporan.store') }}" enctype="multipart/form-data">
                @csrf

                @include('mahasiswa.laporan._form', ['laporan' => null])
            </form>
        </div>
    </div>
</x-app-layout>