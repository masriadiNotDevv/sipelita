<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * Laporan yang dibuat oleh pengguna ini.
     *
     * @return HasMany<Laporan, $this>
     */
    public function laporans(): HasMany
    {
        return $this->hasMany(Laporan::class);
    }

    /**
     * Tanggapan yang diberikan oleh pengguna ini.
     *
     * @return HasMany<Tanggapan, $this>
     */
    public function tanggapan(): HasMany
    {
        return $this->hasMany(Tanggapan::class);
    }

    /**
     * Peran pengguna sebagai enum.
     */
    public function userRole(): UserRole
    {
        return $this->role instanceof UserRole
            ? $this->role
            : UserRole::from($this->role ?? UserRole::Mahasiswa->value);
    }

    /**
     * Apakah pengguna memiliki peran tertentu.
     */
    public function hasRole(UserRole $role): bool
    {
        return $this->userRole() === $role;
    }

    /**
     * Apakah pengguna memiliki salah satu dari peran yang diberikan.
     *
     * @param  array<int, UserRole>  $roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->userRole(), $roles, true);
    }

    /**
     * Inisial pengguna untuk avatar.
     */
    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: [];

        return strtoupper(collect($parts)->take(2)->map(fn (string $part) => mb_substr($part, 0, 1))->implode(''));
    }
}
