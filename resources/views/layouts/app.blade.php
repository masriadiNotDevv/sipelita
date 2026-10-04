<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'SIPELITA') }} - Sistem Pelaporan Fasilitas Kampus</title>

        <meta name="description" content="SIPELITA - Sistem Pelaporan Fasilitas Kampus. Laporkan, tuntaskan, dan pantau fasilitas kampus secara terstruktur.">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="flex min-h-screen flex-col">
        @include('layouts.navigation')

        @if (session('error'))
            <div class="mx-auto w-full max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
                <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <span class="material-symbols-outlined !text-lg">error</span>
                    <p class="font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if (session('status'))
            <div class="mx-auto w-full max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
                <div class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    <span class="material-symbols-outlined !text-lg">check_circle</span>
                    <p class="font-medium">{{ session('status') }}</p>
                </div>
            </div>
        @endif

        @isset($header)
            <header class="border-b border-gray-200 bg-white">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="flex-1">
            {{ $slot }}
        </main>

        @include('layouts.footer')
    </body>
</html>