<x-app-layout title="Notifikasi">
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <nav class="flex items-center gap-1 text-sm text-gray-500">
                    <a href="{{ route('dashboard') }}" class="link-brand">Dashboard</a>
                    <span class="material-symbols-outlined !text-base">chevron_right</span>
                    <span class="font-medium text-gray-700">Notifikasi</span>
                </nav>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">Notifikasi</h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $unreadCount }} notifikasi belum dibaca dari total {{ $notifications->total() }}.
                </p>
            </div>
            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-secondary">
                        <span class="material-symbols-outlined !text-lg">done_all</span>
                        Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl space-y-3 px-4 sm:px-6 lg:px-8">
            @forelse ($notifications as $notifikasi)
                @php
                    $data = $notifikasi->data;
                @endphp
                <div class="card {{ $notifikasi->unread() ? 'border-l-4 border-l-brand-500' : '' }}">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="badge bg-brand-100 text-brand-800">
                                    <span class="material-symbols-outlined !text-sm">campaign</span>
                                    {{ $data['event_label'] ?? 'Pembaruan' }}
                                </span>
                                @if (isset($data['status_label']))
                                    <span class="badge {{ $data['status_class'] ?? 'bg-gray-100 text-gray-700' }}">
                                        <span class="material-symbols-outlined !text-sm">{{ $data['status_icon'] ?? 'info' }}</span>
                                        {{ $data['status_label'] }}
                                    </span>
                                @endif
                                @if ($notifikasi->unread())
                                    <span class="text-xs font-semibold text-brand-700">Baru</span>
                                @endif
                            </div>

                            <p class="mt-3 font-semibold text-gray-900">{{ $data['judul'] ?? 'Pembaruan laporan' }}</p>
                            <p class="mt-1 text-sm leading-relaxed text-gray-700">{{ $data['pesan'] ?? '' }}</p>
                            <p class="mt-2 text-xs text-gray-400">
                                {{ $data['kode'] ?? '' }} &middot;
                                {{ $notifikasi->created_at?->translatedFormat('d F Y H:i') }}
                                @if (isset($data['actor_name']))
                                    &middot; {{ $data['actor_name'] }}
                                @endif
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            @if (isset($data['url']))
                                <a href="{{ $data['url'] }}" class="btn-secondary">
                                    <span class="material-symbols-outlined !text-lg">open_in_new</span>
                                    Lihat
                                </a>
                            @endif
                            @if ($notifikasi->unread())
                                <form method="POST" action="{{ route('notifications.read', $notifikasi) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-ghost">Tandai dibaca</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="card py-12 text-center">
                    <span class="material-symbols-outlined !text-4xl text-gray-300">notifications_off</span>
                    <p class="mt-2 text-sm text-gray-500">Belum ada notifikasi untuk akun Anda.</p>
                </div>
            @endforelse

            @if ($notifications->hasPages())
                <div class="card py-4">{{ $notifications->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
