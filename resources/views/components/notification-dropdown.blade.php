@php
    $unreadNotifications = auth()->user()?->unreadNotifications()->latest()->limit(5)->get() ?? collect();
    $unreadCount = auth()->user()?->unreadNotifications()->count() ?? 0;
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <button type="button" @click="open = ! open" aria-label="Notifikasi"
        class="relative inline-flex items-center justify-center rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-600">
        <span class="material-symbols-outlined !text-xl">notifications</span>
        @if ($unreadCount > 0)
            <span class="absolute right-1 top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold text-white">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open" x-cloak x-transition
        class="absolute right-0 z-30 mt-2 w-80 origin-top-right overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg">
        <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50 px-4 py-3">
            <p class="text-sm font-semibold text-gray-900">Notifikasi</p>
            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-xs font-medium text-brand-700 hover:underline">
                        Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>

        <div class="max-h-80 divide-y divide-gray-100 overflow-y-auto">
            @forelse ($unreadNotifications as $notifikasi)
                <div class="px-4 py-3">
                    <p class="text-xs font-semibold text-brand-700">{{ $notifikasi->data['event_label'] ?? 'Pembaruan' }}</p>
                    <p class="mt-0.5 text-sm text-gray-700">{{ $notifikasi->data['pesan'] ?? '' }}</p>
                    <div class="mt-2 flex items-center justify-between gap-2">
                        <a href="{{ $notifikasi->data['url'] ?? route('notifications.index') }}"
                            class="text-xs font-medium text-brand-700 hover:underline">Lihat detail</a>
                        <form method="POST" action="{{ route('notifications.read', $notifikasi) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs text-gray-500 hover:text-gray-700">Tandai dibaca</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="px-4 py-8 text-center text-sm text-gray-500">Tidak ada notifikasi baru.</p>
            @endforelse
        </div>

        <a href="{{ route('notifications.index') }}"
            class="block border-t border-gray-100 px-4 py-3 text-center text-sm font-medium text-brand-700 hover:bg-gray-50">
            Lihat Semua Notifikasi
        </a>
    </div>
</div>
