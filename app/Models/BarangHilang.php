<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangHilang extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan
     */
    protected $table = 'barang_hilang';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'pengembalian_detail_id',
        'user_id',
        'keterangan',
        'status',
    ];

    /**
     * Konstanta untuk status
     */
    const STATUS_BELUM_DIGANTI = 'belum_diganti';
    const STATUS_SUDAH_DIGANTI = 'sudah_diganti';
    const STATUS_DIPUTIHKAN = 'diputihkan';

    /**
     * Get detail pengembalian terkait
     */
    public function pengembalianDetail(): BelongsTo
    {
        return $this->belongsTo(PengembalianDetail::class, 'pengembalian_detail_id');
    }

    /**
     * Get user yang bertanggung jawab
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get unit yang hilang melalui pengembalian detail
     */
    public function getSarprasUnitAttribute()
    {
        return $this->pengembalianDetail?->sarprasUnit;
    }
}
