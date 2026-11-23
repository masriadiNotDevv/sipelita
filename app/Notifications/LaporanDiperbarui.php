<?php

namespace App\Notifications;

use App\Enums\StatusLaporan;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Notifications\Notification;

class LaporanDiperbarui extends Notification
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public string $event,
        public string $judul,
        public string $pesan,
        public string $kode,
        public int $laporanId,
        public StatusLaporan $status,
        public int $actorId,
        public string $actorName,
        public array $metadata = [],
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function databaseType(object $notifiable): string
    {
        return 'laporan-'.$this->event;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event' => $this->event,
            'event_label' => $this->eventLabel(),
            'judul' => $this->judul,
            'kode' => $this->kode,
            'laporan_id' => $this->laporanId,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'status_class' => $this->status->badgeClass(),
            'status_icon' => $this->status->icon(),
            'pesan' => $this->pesan,
            'actor_id' => $this->actorId,
            'actor_name' => $this->actorName,
            'url' => $this->urlUntuk($notifiable),
            'metadata' => $this->metadata,
        ];
    }

    private function urlUntuk(object $notifiable): string
    {
        if (! $notifiable instanceof User) {
            return route('home');
        }

        if ($this->event === 'diarsipkan') {
            return match ($notifiable->userRole()) {
                UserRole::Admin => route('admin.laporan.arsip'),
                UserRole::Staff => route('staff.laporan.index'),
                UserRole::Mahasiswa => route('mahasiswa.laporan.index'),
            };
        }

        return match ($notifiable->userRole()) {
            UserRole::Admin => route('admin.laporan.show', $this->laporanId),
            UserRole::Staff => route('staff.laporan.show', $this->laporanId),
            UserRole::Mahasiswa => route('mahasiswa.laporan.show', $this->laporanId),
        };
    }

    private function eventLabel(): string
    {
        return match ($this->event) {
            'dibuat' => 'Laporan Baru',
            'diklaim' => 'Laporan Diklaim',
            'ditugaskan' => 'Penangani Diubah',
            'diproses' => 'Status Diperbarui',
            'diedit' => 'Detail Diperbarui',
            'diarsipkan' => 'Laporan Diarsipkan',
            'dipulihkan' => 'Laporan Dipulihkan',
            default => 'Pembaruan Laporan',
        };
    }
}
