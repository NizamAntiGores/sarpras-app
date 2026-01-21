<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Pengembalian extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan
     */
    protected $table = 'pengembalian';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'peminjaman_id',
        'petugas_id',
        'tgl_kembali_aktual',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'tgl_kembali_aktual' => 'date',
    ];

    /**
     * Get peminjaman terkait
     */
    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    /**
     * Get petugas yang menerima pengembalian
     */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    /**
     * Get detail pengembalian (kondisi setiap unit)
     */
    public function details(): HasMany
    {
        return $this->hasMany(PengembalianDetail::class, 'pengembalian_id');
    }

    /**
     * Get total denda dari semua detail
     */
    public function getTotalDendaAttribute(): int
    {
        return $this->details()->sum('denda') ?? 0;
    }

    /**
     * Get jumlah unit yang rusak saat dikembalikan
     */
    public function getJumlahRusakAttribute(): int
    {
        return $this->details()
            ->whereIn('kondisi_akhir', [
                PengembalianDetail::KONDISI_RUSAK_RINGAN,
                PengembalianDetail::KONDISI_RUSAK_BERAT,
            ])
            ->count();
    }

    /**
     * Get jumlah unit yang hilang
     */
    public function getJumlahHilangAttribute(): int
    {
        return $this->details()
            ->where('kondisi_akhir', PengembalianDetail::KONDISI_HILANG)
            ->count();
    }
}
