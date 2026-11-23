<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Enums\PrioritasLaporan;
use App\Enums\StatusLaporan;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLaporanRequest;
use App\LaporanWorkflow;
use App\Models\Fasilitas;
use App\Models\Kategori;
use App\Models\Laporan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Tampilkan daftar laporan milik mahasiswa yang sedang masuk.
     */
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $laporans = auth()->user()->laporans()
            ->with('kategori')
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($request->filled('cari'), function ($query) use ($request): void {
                $cari = '%'.trim((string) $request->string('cari')).'%';

                $query->where(function ($inner) use ($cari): void {
                    $inner->where('judul', 'like', $cari)
                        ->orWhere('kode', 'like', $cari)
                        ->orWhere('lokasi', 'like', $cari);
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('mahasiswa.laporan.index', [
            'laporans' => $laporans,
            'cari' => $request->string('cari')->toString(),
            'statusTerpilih' => $status,
            'statusTersedia' => StatusLaporan::cases(),
            'totalLaporan' => auth()->user()->laporans()->count(),
            'totalMenunggu' => auth()->user()->laporans()->where('status', StatusLaporan::Menunggu->value)->count(),
            'totalDiproses' => auth()->user()->laporans()->where('status', StatusLaporan::Diproses->value)->count(),
            'totalSelesai' => auth()->user()->laporans()->where('status', StatusLaporan::Selesai->value)->count(),
        ]);
    }

    /**
     * Tampilkan formulir pelaporan baru.
     */
    public function create(): View
    {
        return view('mahasiswa.laporan.create', [
            'kategoris' => Kategori::query()->orderBy('nama')->get(),
            'fasilitas' => Fasilitas::query()->orderBy('nama')->get(),
            'prioritas' => PrioritasLaporan::cases(),
        ]);
    }

    /**
     * Simpan laporan baru dari mahasiswa.
     */
    public function store(StoreLaporanRequest $request, LaporanWorkflow $workflow): RedirectResponse
    {
        $data = $request->safe()->except('bukti_foto');

        $data['prioritas'] = PrioritasLaporan::from($data['prioritas']);
        $data['user_id'] = $request->user()->id;
        $data['status'] = StatusLaporan::Menunggu;

        if ($request->hasFile('bukti_foto')) {
            $data['bukti_foto'] = $request->file('bukti_foto')->store('bukti-foto', 'public');
        }

        $laporan = auth()->user()->laporans()->create($data);
        $laporan->isiKode();

        $workflow->dibuat($laporan, $request->user());

        return redirect()
            ->route('mahasiswa.laporan.show', $laporan)
            ->with('status', "Laporan {$laporan->kode} berhasil dikirim dan menunggu ditindaklanjuti.");
    }

    /**
     * Tampilkan detail laporan milik mahasiswa.
     */
    public function show(Laporan $laporan): View
    {
        $this->authorize('view', $laporan);

        return view('mahasiswa.laporan.show', [
            'laporan' => $laporan->load(['kategori', 'fasilitas', 'ditanganiOleh', 'tanggapan.user']),
        ]);
    }
}
