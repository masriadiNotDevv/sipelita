<?php

namespace App\Models;

use App\Enums\PrioritasLaporan;
use App\Enums\StatusLaporan;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'kode',
    'user_id',
    'kategori_id',
    'fasilitas_id',
    'ditangani_oleh',
    'judul',
    'deskripsi',
    'lokasi',
    'prioritas',
    'status',
    'bukti_foto',
    'selesai_at',
])]
class Laporan extends Model
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
            'prioritas' => PrioritasLaporan::class,
            'status' => StatusLaporan::class,
            'selesai_at' => 'datetime',
        ];
    }

    /**
     * Pelapor laporan.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Kategori laporan.
     *
     * @return BelongsTo<Kategori, $this>
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    /**
     * Fasilitas yang dilaporkan.
     *
     * @return BelongsTo<Fasilitas, $this>
     */
    public function fasilitas(): BelongsTo
    {
        return $this->belongsTo(Fasilitas::class);
    }

    /**
     * Staff yang menangani laporan.
     *
     * @return BelongsTo<User, $this>
     */
    public function ditanganiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditangani_oleh');
    }

    /**
     * Riwayat tanggapan atas laporan.
     *
     * @return HasMany<Tanggapan, $this>
     */
    public function tanggapan(): HasMany
    {
        return $this->hasMany(Tanggapan::class);
    }

    /**
     * Batasi kueri berdasarkan status.
     *
     * @param  Builder<Laporan>  $query
     * @return Builder<Laporan>
     */
    public function scopeStatus(Builder $query, StatusLaporan $status): Builder
    {
        return $query->where('status', $status->value);
    }

    /**
     * Batasi kueri hanya untuk status yang belum selesai.
     *
     * @param  Builder<Laporan>  $query
     * @return Builder<Laporan>
     */
    public function scopeBelumSelesai(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            StatusLaporan::Selesai->value,
            StatusLaporan::Ditolak->value,
        ]);
    }

    /**
     * Format tanggal singkat untuk tampilan daftar.
     */
    protected function tanggalPendek(): Attribute
    {
        return Attribute::get(fn (): string => $this->created_at?->format('d M Y') ?? '-');
    }

    /**
     * Warna sidebar status laporan.
     */
    protected function statusClass(): Attribute
    {
        return Attribute::get(fn (): string => $this->status->badgeClass());
    }

    /**
     * Generate kode laporan berikutnya.
     */
    public static function nextKode(): string
    {
        $tahun = now()->format('Y');
        $jumlah = static::query()->where('kode', 'like', "LAP-{$tahun}-%")->count();

        return sprintf('LAP-%s-%04d', $tahun, $jumlah + 1);
    }
}
