<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard admin.
     */
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'totalLaporan' => Laporan::query()->count(),
            'laporanDiproses' => Laporan::query()->where('status', 'diproses')->count(),
            'laporanSelesai' => Laporan::query()->where('status', 'selesai')->count(),
            'laporanMenunggu' => Laporan::query()->where('status', 'menunggu')->count(),
            'totalUser' => User::query()->count(),
            'totalMahasiswa' => User::query()->where('role', UserRole::Mahasiswa->value)->count(),
            'totalStaff' => User::query()->where('role', UserRole::Staff->value)->count(),
            'totalKategori' => Kategori::query()->count(),
            'laporanTerbaru' => Laporan::query()->with(['user', 'kategori'])->latest()->limit(5)->get(),
        ]);
    }
}
