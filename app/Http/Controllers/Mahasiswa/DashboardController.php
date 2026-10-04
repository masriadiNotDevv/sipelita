<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard mahasiswa.
     */
    public function __invoke(): View
    {
        $laporans = auth()->user()->laporans();

        return view('mahasiswa.dashboard', [
            'totalLaporan' => (clone $laporans)->count(),
            'laporanMenunggu' => (clone $laporans)->where('status', 'menunggu')->count(),
            'laporanDiproses' => (clone $laporans)->where('status', 'diproses')->count(),
            'laporanPerbaikan' => (clone $laporans)->where('status', 'dalam_perbaikan')->count(),
            'laporanSelesai' => (clone $laporans)->where('status', 'selesai')->count(),
            'laporanTerbaru' => (clone $laporans)
                ->with('kategori')
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }
}
