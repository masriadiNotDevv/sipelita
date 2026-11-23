<?php

namespace App;

use App\Enums\StatusLaporan;
use App\Enums\UserRole;
use App\Models\Laporan;
use App\Models\Tanggapan;
use App\Models\User;
use App\Notifications\LaporanDiperbarui;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class LaporanWorkflow
{
    public function klaim(Laporan $laporan, User $actor): Laporan
    {
        $laporan->forceFill(['ditangani_oleh' => $actor->id])->save();

        $this->notifikasi(
            $laporan,
            $actor,
            'diklaim',
            "{$actor->name} mengklaim laporan {$laporan->kode}.",
        );

        return $laporan;
    }

    public function tugaskan(Laporan $laporan, ?User $penangani, User $actor): Laporan
    {
        $laporan->forceFill(['ditangani_oleh' => $penangani?->id])->save();

        $pesan = $penangani === null
            ? "{$actor->name} melepas penangani laporan {$laporan->kode}."
            : "{$actor->name} menugaskan laporan {$laporan->kode} kepada {$penangani->name}.";

        $this->notifikasi($laporan, $actor, 'ditugaskan', $pesan);

        return $laporan;
    }

    public function proses(Laporan $laporan, User $actor, StatusLaporan $status, string $isi): Tanggapan
    {
        return DB::transaction(function () use ($laporan, $actor, $status, $isi): Tanggapan {
            $statusSebelum = $laporan->status;

            $tanggapan = Tanggapan::create([
                'laporan_id' => $laporan->id,
                'user_id' => $actor->id,
                'isi' => $isi,
                'status_sebelum' => $statusSebelum,
                'status_sesudah' => $status,
            ]);

            $laporan->forceFill([
                'status' => $status,
                'selesai_at' => $status === StatusLaporan::Selesai ? now() : null,
            ])->save();

            $this->notifikasi(
                $laporan,
                $actor,
                'diproses',
                "{$actor->name} memperbarui laporan {$laporan->kode} menjadi {$status->label()}.",
            );

            return $tanggapan;
        });
    }

    public function perbaruiDetail(Laporan $laporan, array $data, User $actor): Laporan
    {
        $laporan->fill($data)->save();

        $this->notifikasi(
            $laporan,
            $actor,
            'diedit',
            "{$actor->name} memperbarui detail laporan {$laporan->kode}.",
        );

        return $laporan;
    }

    public function dibuat(Laporan $laporan, User $actor): void
    {
        $this->notifikasi(
            $laporan,
            $actor,
            'dibuat',
            "Laporan baru {$laporan->kode} telah diajukan oleh {$actor->name}.",
        );
    }

    public function arsipkan(Laporan $laporan, User $actor): Laporan
    {
        $laporan->delete();

        $this->notifikasi(
            $laporan,
            $actor,
            'diarsipkan',
            "{$actor->name} mengarsipkan laporan {$laporan->kode}.",
        );

        return $laporan;
    }

    public function pulihkan(Laporan $laporan, User $actor): Laporan
    {
        $laporan->restore();

        $this->notifikasi(
            $laporan,
            $actor,
            'dipulihkan',
            "{$actor->name} memulihkan laporan {$laporan->kode}.",
        );

        return $laporan;
    }

    private function notifikasi(
        Laporan $laporan,
        User $actor,
        string $event,
        string $pesan,
    ): void {
        $penerima = User::query()
            ->whereIn('role', [UserRole::Admin->value, UserRole::Staff->value])
            ->orWhere('id', $laporan->user_id)
            ->get()
            ->unique('id')
            ->values();

        Notification::send($penerima, new LaporanDiperbarui(
            event: $event,
            judul: $laporan->judul,
            pesan: $pesan,
            kode: $laporan->kode,
            laporanId: $laporan->id,
            status: $laporan->status,
            actorId: $actor->id,
            actorName: $actor->name,
        ));
    }
}
