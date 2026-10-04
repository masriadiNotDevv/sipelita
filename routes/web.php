<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', function () {
        return redirect()->route(auth()->user()->userRole()->dashboardRoute());
    })->name('dashboard');

    Route::middleware('role:admin')->group(function (): void {
        Route::get('/admin/dashboard', AdminDashboardController::class)->name('admin.dashboard');
    });

    Route::middleware('role:staff')->group(function (): void {
        Route::get('/staff/dashboard', StaffDashboardController::class)->name('staff.dashboard');
    });

    Route::middleware('role:mahasiswa')->group(function (): void {
        Route::get('/mahasiswa/dashboard', MahasiswaDashboardController::class)->name('mahasiswa.dashboard');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
