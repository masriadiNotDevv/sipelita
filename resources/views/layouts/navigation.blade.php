@php
    /** @var \App\Models\User|null $authUser */
    $authUser = auth()->user();
    $userRole = $authUser?->userRole();
    $dashboardPath = $userRole?->dashboardPath() ?? route('home');
    $isAdmin = $userRole === \App\Enums\UserRole::Admin;
@endphp

<nav x-data="{ open: false, profileOpen: false }" class="border-b border-gray-200 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/80">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600 text-white shadow-sm">
                        <span class="material-symbols-outlined !text-lg">report</span>
                    </span>
                    <span class="flex flex-col leading-tight">
                        <span class="text-sm font-bold tracking-tight text-gray-900">SIPELITA</span>
                        <span class="hidden text-[10px] font-medium text-gray-500 sm:block">Sistem Pelaporan Fasilitas Kampus</span>
                    </span>
                </a>

                <div class="ml-8 hidden items-center gap-1 md:flex">
                    <a href="{{ route('home') }}"
                        class="nav-link {{ request()->routeIs('home') ? 'nav-link-active' : '' }}">
                        <span class="material-symbols-outlined !text-lg">home</span>
                        Beranda
                    </a>

                    @auth
                        <a href="{{ $dashboardPath }}"
                            class="nav-link {{ request()->is($dashboardPath) ? 'nav-link-active' : '' }}">
                            <span class="material-symbols-outlined !text-lg">{{ $userRole->icon() }}</span>
                            Dashboard {{ $userRole->label() }}
                        </a>

                        @if ($isAdmin)
                            <a href="{{ route('admin.staff.index') }}"
                                class="nav-link {{ request()->routeIs('admin.staff.*') ? 'nav-link-active' : '' }}">
                                <span class="material-symbols-outlined !text-lg">group</span>
                                Kelola Staff
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="hidden items-center gap-3 md:flex">
                @guest
                    <a href="{{ route('login') }}" class="btn-ghost">Masuk</a>
                    <a href="{{ route('register') }}" class="btn-primary">
                        <span class="material-symbols-outlined !text-lg">person_add</span>
                        Daftar
                    </a>
                @else
                    <div class="relative" @click.outside="profileOpen = false">
                        <button type="button" @click="profileOpen = ! profileOpen"
                            class="flex items-center gap-2 rounded-full py-1 pl-1 pr-3 text-sm font-medium text-gray-700 ring-1 ring-gray-200 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-600">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-600 text-xs font-semibold text-white">
                                {{ $authUser->initials() }}
                            </span>
                            <span class="max-w-[10rem] truncate">{{ $authUser->name }}</span>
                            <span class="material-symbols-outlined !text-lg text-gray-400">expand_more</span>
                        </button>

                        <div x-show="profileOpen" x-cloak x-transition
                            class="absolute right-0 z-30 mt-2 w-60 origin-top-right overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg">
                            <div class="border-b border-gray-100 bg-gray-50 px-4 py-3">
                                <p class="truncate text-sm font-semibold text-gray-900">{{ $authUser->name }}</p>
                                <p class="truncate text-xs text-gray-500">{{ $authUser->email }}</p>
                                <span class="badge mt-2 bg-brand-100 text-brand-800">
                                    <span class="material-symbols-outlined !text-sm">{{ $userRole->icon() }}</span>
                                    {{ $userRole->label() }}
                                </span>
                            </div>
                            <div class="p-1.5">
                                <a href="{{ $dashboardPath }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 transition hover:bg-gray-50">
                                    <span class="material-symbols-outlined !text-lg text-gray-500">space_dashboard</span>
                                    Dashboard
                                </a>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 transition hover:bg-gray-50">
                                    <span class="material-symbols-outlined !text-lg text-gray-500">manage_accounts</span>
                                    Pengaturan Profil
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm text-gray-700 transition hover:bg-red-50 hover:text-red-700">
                                        <span class="material-symbols-outlined !text-lg text-gray-500">logout</span>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endguest
            </div>

            <div class="-mr-2 flex items-center md:hidden">
                <button type="button" @click="open = ! open"
                    class="inline-flex items-center justify-center rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-600">
                    <span x-show="! open" class="material-symbols-outlined">menu</span>
                    <span x-show="open" x-cloak class="material-symbols-outlined">close</span>
                </button>
            </div>
        </div>
    </div>

    <div x-show="open" x-cloak x-collapse class="border-t border-gray-200 bg-white md:hidden">
        <div class="space-y-1 px-4 py-3">
            <a href="{{ route('home') }}"
                class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 {{ request()->routeIs('home') ? 'nav-link-active' : '' }}">
                <span class="material-symbols-outlined !text-lg">home</span>
                Beranda
            </a>

            @auth
                <a href="{{ $dashboardPath }}"
                    class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 {{ request()->is($dashboardPath) ? 'nav-link-active' : '' }}">
                    <span class="material-symbols-outlined !text-lg">{{ $userRole->icon() }}</span>
                    Dashboard {{ $userRole->label() }}
                </a>

                @if ($isAdmin)
                    <a href="{{ route('admin.staff.index') }}"
                        class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 {{ request()->routeIs('admin.staff.*') ? 'nav-link-active' : '' }}">
                        <span class="material-symbols-outlined !text-lg">group</span>
                        Kelola Staff
                    </a>
                @endif
            @endauth
        </div>

        <div class="border-t border-gray-200 px-4 py-4">
            @guest
                <div class="space-y-2">
                    <a href="{{ route('login') }}" class="btn-secondary w-full">Masuk</a>
                    <a href="{{ route('register') }}" class="btn-primary w-full">
                        <span class="material-symbols-outlined !text-lg">person_add</span>
                        Daftar
                    </a>
                </div>
            @else
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-600 text-sm font-semibold text-white">
                        {{ $authUser->initials() }}
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-gray-900">{{ $authUser->name }}</p>
                        <p class="text-xs text-gray-500">{{ $userRole->label() }}</p>
                    </div>
                </div>
                <div class="mt-3 space-y-1">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        <span class="material-symbols-outlined !text-lg">manage_accounts</span>
                        Pengaturan Profil
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm font-medium text-gray-700 transition hover:bg-red-50 hover:text-red-700">
                            <span class="material-symbols-outlined !text-lg">logout</span>
                            Keluar
                        </button>
                    </form>
                </div>
            @endguest
        </div>
    </div>
</nav>