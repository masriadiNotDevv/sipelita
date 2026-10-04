<nav x-data="{ open: false }" class="border-b border-gray-200 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/80">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <div class="flex">
                <div class="flex shrink-0 items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600 text-white shadow-sm">
                            <span class="material-symbols-outlined text-base">report</span>
                        </div>
                        <div class="flex flex-col leading-tight">
                            <span class="text-sm font-bold tracking-tight text-gray-900">SIPELITA</span>
                            <span class="text-[10px] font-medium text-gray-500">Sistem Pelaporan Fasilitas Kampus</span>
                        </div>
                    </a>
                </div>

                <div class="hidden sm:ml-10 sm:flex sm:items-center sm:space-x-1">
                    <a href="{{ route('home') }}" class="btn-ghost {{ request()->routeIs('home') ? 'bg-gray-100' : '' }}">
                        <span class="material-symbols-outlined text-lg">home</span>
                        Beranda
                    </a>

                    @auth
                        @if (auth()->user()->role === 'admin')
                            <a href="{{ url('/admin/dashboard') }}" class="btn-ghost">
                                <span class="material-symbols-outlined text-lg">dashboard</span>
                                Dashboard Admin
                            </a>
                        @elseif (auth()->user()->role === 'staff')
                            <a href="{{ url('/staff/dashboard') }}" class="btn-ghost">
                                <span class="material-symbols-outlined text-lg">assignment</span>
                                Dashboard Staff
                            </a>
                        @elseif (auth()->user()->role === 'mahasiswa')
                            <a href="{{ url('/mahasiswa/dashboard') }}" class="btn-ghost">
                                <span class="material-symbols-outlined text-lg">description</span>
                                Dashboard Mahasiswa
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="hidden sm:ml-6 sm:flex sm:items-center sm:gap-3">
                @guest
                    <a href="{{ route('login') }}" class="btn-ghost">Masuk</a>
                    <a href="{{ route('register') }}" class="btn-primary">
                        <span class="material-symbols-outlined text-lg">person_add</span>
                        Daftar
                    </a>
                @else
                    <div class="flex items-center gap-3">
                        <div class="hidden text-right sm:block">
                            <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs capitalize text-gray-500">{{ Auth::user()->role }}</p>
                        </div>
                        <div class="relative" x-data="{ profileOpen: false }" @click.outside="profileOpen = false">
                            <button @click="profileOpen = !profileOpen" class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-600/10 text-brand-700 ring-1 ring-brand-200 transition hover:bg-brand-600/15">
                                <span class="material-symbols-outlined">person</span>
                            </button>

                            <div x-show="profileOpen" x-transition class="absolute right-0 z-20 mt-2 w-56 origin-top-right rounded-xl border border-gray-200 bg-white shadow-lg" style="display: none;">
                                <div class="border-b border-gray-100 px-4 py-3">
                                    <p class="truncate text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                                    <p class="truncate text-xs text-gray-500">{{ Auth::user()->email }}</p>
                                </div>
                                <div class="py-1">
                                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 transition hover:bg-gray-50">
                                        <span class="material-symbols-outlined text-lg">settings</span>
                                        Pengaturan Profil
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-gray-700 transition hover:bg-gray-50">
                                            <span class="material-symbols-outlined text-lg">logout</span>
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endguest
            </div>

            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-600 focus:ring-offset-2">
                    <span x-show="!open" class="material-symbols-outlined">menu</span>
                    <span x-show="open" class="material-symbols-outlined">close</span>
                </button>
            </div>
        </div>
    </div>

    <div x-show="open" class="sm:hidden" style="display: none;">
        <div class="space-y-1 border-t border-gray-200 bg-white px-2 pb-3 pt-2">
            <a href="{{ route('home') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-base font-medium text-gray-700 transition hover:bg-gray-50 {{ request()->routeIs('home') ? 'bg-brand-50 text-brand-700' : '' }}">
                <span class="material-symbols-outlined">home</span>
                Beranda
            </a>

            @auth
                @if (auth()->user()->role === 'admin')
                    <a href="{{ url('/admin/dashboard') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-base font-medium text-gray-700 transition hover:bg-gray-50">
                        <span class="material-symbols-outlined">dashboard</span>
                        Dashboard Admin
                    </a>
                @elseif (auth()->user()->role === 'staff')
                    <a href="{{ url('/staff/dashboard') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-base font-medium text-gray-700 transition hover:bg-gray-50">
                        <span class="material-symbols-outlined">assignment</span>
                        Dashboard Staff
                    </a>
                @elseif (auth()->user()->role === 'mahasiswa')
                    <a href="{{ url('/mahasiswa/dashboard') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-base font-medium text-gray-700 transition hover:bg-gray-50">
                        <span class="material-symbols-outlined">description</span>
                        Dashboard Mahasiswa
                    </a>
                @endif
            @endauth
        </div>

        <div class="border-t border-gray-200 bg-white pb-3 pt-4">
            @guest
                <div class="space-y-2 px-4">
                    <a href="{{ route('login') }}" class="btn-secondary w-full justify-center">Masuk</a>
                    <a href="{{ route('register') }}" class="btn-primary w-full justify-center">
                        <span class="material-symbols-outlined text-lg">person_add</span>
                        Daftar
                    </a>
                </div>
            @else
                <div class="px-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-base font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-sm capitalize text-gray-500">{{ Auth::user()->role }}</p>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-base font-medium text-gray-700 transition hover:bg-gray-50">
                            <span class="material-symbols-outlined">settings</span>
                            Pengaturan Profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-base font-medium text-gray-700 transition hover:bg-gray-50">
                                <span class="material-symbols-outlined">logout</span>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            @endguest
        </div>
    </div>
</nav>
