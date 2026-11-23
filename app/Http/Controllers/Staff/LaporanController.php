<?php

namespace App\Http\Controllers\Staff;

use App\Enums\StatusLaporan;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProsesLaporanRequest;
use App\LaporanWorkflow;
use App\Models\Laporan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $status = StatusLaporan::tryFrom($request->string('status')->toString());

        $laporans = Laporan::query()
            ->with(['user:id,name', 'kategori:id,nama', 'fasilitas:id,nama', 'ditanganiOleh:id,name'])
            ->when($status, fn ($query, StatusLaporan $pilihan) => $query->where('status', $pilihan))
            ->when($request->filled('cari'), function ($query) use ($request): void {
                $cari = '%'.trim((string) $request->string('cari')).'%';

                $query->where(function ($inner) use ($cari): void {
                    $inner->where('judul', 'like', $cari)
                        ->orWhere('kode', 'like', $cari)
                        ->orWhere('lokasi', 'like', $cari)
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', $cari));
                });
            })
            ->latest()
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('staff.laporan.index', [
            'laporans' => $laporans,
            'cari' => $request->string('cari')->toString(),
            'statusTerpilih' => $status?->value ?? '',
            'statusTersedia' => StatusLaporan::cases(),
            'totalLaporan' => Laporan::query()->count(),
            'totalMenunggu' => Laporan::query()->where('status', StatusLaporan::Menunggu->value)->count(),
            'totalDiproses' => Laporan::query()->where('status', StatusLaporan::Diproses->value)->count(),
            'totalSelesai' => Laporan::query()->where('status', StatusLaporan::Selesai->value)->count(),
            'totalDitanganiSaya' => Laporan::query()->where('ditangani_oleh', $request->user()->id)->count(),
        ]);
    }

    public function show(Laporan $laporan): View
    {
        $this->authorize('view', $laporan);

        $laporan->load([
            'user:id,name,email',
            'kategori:id,nama',
            'fasilitas:id,nama',
            'ditanganiOleh:id,name',
            'tanggapan' => fn ($query) => $query->oldest(),
            'tanggapan.user:id,name',
        ]);

        return view('staff.laporan.show', [
            'laporan' => $laporan,
            'statusTersedia' => StatusLaporan::cases(),
        ]);
    }

    public function klaim(Laporan $laporan, LaporanWorkflow $workflow): RedirectResponse
    {
        $this->authorize('klaim', $laporan);

        $workflow->klaim($laporan, request()->user());

        return back()->with('status', "Laporan {$laporan->kode} berhasil diklaim.");
    }

    public function proses(
        ProsesLaporanRequest $request,
        Laporan $laporan,
        LaporanWorkflow $workflow,
    ): RedirectResponse {
        $data = $request->safe()->only(['status', 'isi']);

        $workflow->proses(
            $laporan,
            $request->user(),
            StatusLaporan::from($data['status']),
            $data['isi'],
        );

        return back()->with('status', "Penanganan laporan {$laporan->kode} berhasil disimpan.");
    }
}
