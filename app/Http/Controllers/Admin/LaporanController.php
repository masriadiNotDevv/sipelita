<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PrioritasLaporan;
use App\Enums\StatusLaporan;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProsesLaporanRequest;
use App\Http\Requests\TugaskanLaporanRequest;
use App\Http\Requests\UpdateLaporanAdminRequest;
use App\LaporanWorkflow;
use App\Models\Fasilitas;
use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $status = StatusLaporan::tryFrom($request->string('status')->toString());
        $prioritas = PrioritasLaporan::tryFrom($request->string('prioritas')->toString());

        $laporans = Laporan::query()
            ->with(['user:id,name', 'kategori:id,nama', 'ditanganiOleh:id,name'])
            ->when($status, fn ($query, StatusLaporan $pilihan) => $query->where('status', $pilihan->value))
            ->when($prioritas, fn ($query, PrioritasLaporan $pilihan) => $query->where('prioritas', $pilihan->value))
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

        return view('admin.laporan.index', [
            'laporans' => $laporans,
            'cari' => $request->string('cari')->toString(),
            'statusTerpilih' => $status?->value ?? '',
            'prioritasTerpilih' => $prioritas?->value ?? '',
            'statusTersedia' => StatusLaporan::cases(),
            'prioritasTersedia' => PrioritasLaporan::cases(),
            'totalLaporan' => Laporan::query()->count(),
            'totalMenunggu' => Laporan::query()->where('status', StatusLaporan::Menunggu->value)->count(),
            'totalDiproses' => Laporan::query()->where('status', StatusLaporan::Diproses->value)->count(),
            'totalSelesai' => Laporan::query()->where('status', StatusLaporan::Selesai->value)->count(),
            'totalArsip' => Laporan::onlyTrashed()->count(),
        ]);
    }

    public function arsip(Request $request): View
    {
        $laporans = Laporan::onlyTrashed()
            ->with(['user:id,name', 'ditanganiOleh:id,name'])
            ->when($request->filled('cari'), function ($query) use ($request): void {
                $cari = '%'.trim((string) $request->string('cari')).'%';

                $query->where(function ($inner) use ($cari): void {
                    $inner->where('judul', 'like', $cari)->orWhere('kode', 'like', $cari);
                });
            })
            ->latest('deleted_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.laporan.arsip', [
            'laporans' => $laporans,
            'cari' => $request->string('cari')->toString(),
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

        return view('admin.laporan.show', [
            'laporan' => $laporan,
            'statusTersedia' => StatusLaporan::cases(),
            'staffTersedia' => User::query()
                ->where('role', UserRole::Staff)
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function edit(Laporan $laporan): View
    {
        $this->authorize('update', $laporan);

        return view('admin.laporan.edit', [
            'laporan' => $laporan,
            'kategoris' => Kategori::query()->orderBy('nama')->get(),
            'fasilitas' => Fasilitas::query()->orderBy('nama')->get(),
            'prioritasTersedia' => PrioritasLaporan::cases(),
        ]);
    }

    public function update(
        UpdateLaporanAdminRequest $request,
        Laporan $laporan,
        LaporanWorkflow $workflow,
    ): RedirectResponse {
        $workflow->perbaruiDetail($laporan, $request->safe()->only([
            'judul',
            'deskripsi',
            'lokasi',
            'kategori_id',
            'fasilitas_id',
            'prioritas',
        ]), $request->user());

        return redirect()
            ->route('admin.laporan.show', $laporan)
            ->with('status', "Detail laporan {$laporan->kode} berhasil diperbarui.");
    }

    public function tugaskan(
        TugaskanLaporanRequest $request,
        Laporan $laporan,
        LaporanWorkflow $workflow,
    ): RedirectResponse {
        $penanganiId = $request->integer('penangani_id') ?: null;

        $penangani = $penanganiId === null
            ? null
            : User::query()->where('role', UserRole::Staff)->findOrFail($penanganiId);

        $workflow->tugaskan($laporan, $penangani, $request->user());

        return back()->with('status', "Penangani laporan {$laporan->kode} berhasil diperbarui.");
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

    public function destroy(Laporan $laporan, LaporanWorkflow $workflow): RedirectResponse
    {
        $this->authorize('delete', $laporan);

        $workflow->arsipkan($laporan, request()->user());

        return redirect()
            ->route('admin.laporan.index')
            ->with('status', "Laporan {$laporan->kode} berhasil diarsipkan.");
    }

    public function restore(Laporan $laporan, LaporanWorkflow $workflow): RedirectResponse
    {
        $this->authorize('restore', $laporan);

        $workflow->pulihkan($laporan, request()->user());

        return redirect()
            ->route('admin.laporan.arsip')
            ->with('status', "Laporan {$laporan->kode} berhasil dipulihkan.");
    }
}
