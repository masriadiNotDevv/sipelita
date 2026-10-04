<?php

namespace App\Models;

use App\Enums\StatusLaporan;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['laporan_id', 'user_id', 'isi', 'status_sebelum', 'status_sesudah'])]
class Tanggapan extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status_sebelum' => StatusLaporan::class,
            'status_sesudah' => StatusLaporan::class,
        ];
    }

    /**
     * Laporan yang diberi tanggapan.
     *
     * @return BelongsTo<Laporan, $this>
     */
    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class);
    }

    /**
     * Pemberi tanggapan.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
