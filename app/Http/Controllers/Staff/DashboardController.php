<?php

namespace App\Http\Controllers\Staff;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard staff.
     */
    public function __invoke(): View
    {
        return view('staff.dashboard', [
            'totalDitangani' => Laporan::query()
                ->whereNotNull('ditangani_oleh')
                ->count(),
            'laporanMenunggu' => Laporan::query()->where('status', 'menunggu')->count(),
            'laporanDiproses' => Laporan::query()->where('status', 'diproses')->count(),
            'laporanPerbaikan' => Laporan::query()->where('status', 'dalam_perbaikan')->count(),
            'laporanSelesai' => Laporan::query()->where('status', 'selesai')->count(),
            'antrian' => Laporan::query()
                ->with(['user', 'kategori'])
                ->whereIn('status', ['menunggu', 'diproses'])
                ->latest()
                ->limit(5)
                ->get(),
            'totalStaff' => User::query()->where('role', UserRole::Staff->value)->count(),
        ]);
    }
}
