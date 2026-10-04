<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->bindStaffRoute();
    }

    /**
     * Batasi route binding `staff` hanya untuk akun admin dan staff.
     *
     * Akun mahasiswa tidak dapat dikelola melalui halaman kelola staff,
     * sehingga permintaan terhadap akun tersebut berakhir dengan 404.
     */
    private function bindStaffRoute(): void
    {
        Route::bind('staff', function (string $value): User {
            return User::query()
                ->whereIn('role', [UserRole::Admin->value, UserRole::Staff->value])
                ->findOrFail($value);
        });
    }
}
