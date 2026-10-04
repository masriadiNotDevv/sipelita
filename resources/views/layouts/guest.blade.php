<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ?? config('app.name', 'SIPELITA') }} - Sistem Pelaporan Fasilitas Kampus</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="flex min-h-screen flex-col bg-gray-50">
        <div class="flex flex-1 flex-col justify-center px-4 py-10 sm:px-6 lg:px-8">
            <div class="mx-auto w-full max-w-md">
                <a href="{{ route('home') }}" class="mb-6 flex items-center justify-center gap-2">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-600 text-white shadow-sm">
                        <span class="material-symbols-outlined !text-xl">report</span>
                    </span>
                    <span class="flex flex-col leading-tight">
                        <span class="text-base font-bold tracking-tight text-gray-900">SIPELITA</span>
                        <span class="text-[10px] font-medium text-gray-500">Sistem Pelaporan Fasilitas Kampus</span>
                    </span>
                </a>

                {{ $slot }}
            </div>
        </div>

        <footer class="border-t border-gray-200 bg-white py-6">
            <p class="text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} SIPELITA - Tim Masriadi, Nurul Aulia, Nadia, Nuri Zilvani, Elsariani, Jona Irwansyah, Irsan
            </p>
        </footer>
    </body>
</html>