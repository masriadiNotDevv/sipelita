<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Admin\StaffController as AdminStaffController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboardController;
use App\Http\Controllers\Mahasiswa\LaporanController as MahasiswaLaporanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\LaporanController as StaffLaporanController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', function () {
        return redirect()->route(auth()->user()->userRole()->dashboardRoute());
    })->name('dashboard');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');

    Route::middleware('role:admin')->group(function (): void {
        Route::get('/admin/dashboard', AdminDashboardController::class)->name('admin.dashboard');

        Route::get('/admin/laporan/arsip', [AdminLaporanController::class, 'arsip'])->name('admin.laporan.arsip');
        Route::patch('/admin/laporan/{laporan}/restore', [AdminLaporanController::class, 'restore'])
            ->name('admin.laporan.restore')
            ->withTrashed();
        Route::resource('admin/laporan', AdminLaporanController::class)
            ->parameters(['admin/laporan' => 'laporan'])
            ->except(['create', 'store'])
            ->names('admin.laporan');
        Route::patch('/admin/laporan/{laporan}/penangani', [AdminLaporanController::class, 'tugaskan'])
            ->name('admin.laporan.penangani');
        Route::patch('/admin/laporan/{laporan}/proses', [AdminLaporanController::class, 'proses'])
            ->name('admin.laporan.proses');

        Route::resource('admin/staff', AdminStaffController::class)
            ->parameters(['admin/staff' => 'staff'])
            ->except(['show'])
            ->names('admin.staff');
    });

    Route::middleware('role:staff')->group(function (): void {
        Route::get('/staff/dashboard', StaffDashboardController::class)->name('staff.dashboard');

        Route::resource('staff/laporan', StaffLaporanController::class)
            ->parameters(['staff/laporan' => 'laporan'])
            ->only(['index', 'show'])
            ->names('staff.laporan');
        Route::patch('/staff/laporan/{laporan}/klaim', [StaffLaporanController::class, 'klaim'])
            ->name('staff.laporan.klaim');
        Route::patch('/staff/laporan/{laporan}/proses', [StaffLaporanController::class, 'proses'])
            ->name('staff.laporan.proses');
    });

    Route::middleware('role:mahasiswa')->group(function (): void {
        Route::get('/mahasiswa/dashboard', MahasiswaDashboardController::class)->name('mahasiswa.dashboard');

        Route::resource('mahasiswa/laporan', MahasiswaLaporanController::class)
            ->parameters(['mahasiswa/laporan' => 'laporan'])
            ->only(['index', 'create', 'store', 'show'])
            ->names('mahasiswa.laporan');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
