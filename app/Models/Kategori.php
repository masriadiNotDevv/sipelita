<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'deskripsi'])]
class Kategori extends Model
{
    use HasFactory;

    /**
     * Laporan yang menggunakan kategori ini.
     *
     * @return HasMany<Laporan, $this>
     */
    public function laporans(): HasMany
    {
        return $this->hasMany(Laporan::class);
    }
}
