<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'lokasi', 'gedung', 'lantai'])]
class Fasilitas extends Model
{
    use HasFactory;

    /**
     * Laporan yang merujuk pada fasilitas ini.
     *
     * @return HasMany<Laporan, $this>
     */
    public function laporans(): HasMany
    {
        return $this->hasMany(Laporan::class);
    }
}
