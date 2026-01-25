<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PengembalianDetail extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan
     */
    protected $table = 'pengembalian_details';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'pengembalian_id',
        'sarpras_unit_id',
        'kondisi_akhir',
        'foto_kondisi',
        'catatan',
        'denda',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'denda' => 'integer',
    ];

    /**
     * Konstanta untuk kondisi akhir
     */
    const KONDISI_BAIK = 'baik';

    const KONDISI_RUSAK_RINGAN = 'rusak_ringan';

    const KONDISI_RUSAK_BERAT = 'rusak_berat';

    const KONDISI_HILANG = 'hilang';

    /**
     * Get pengembalian header
     */
    public function pengembalian(): BelongsTo
    {
        return $this->belongsTo(Pengembalian::class, 'pengembalian_id');
    }

    /**
     * Get unit yang dikembalikan
     */
    public function sarprasUnit(): BelongsTo
    {
        return $this->belongsTo(SarprasUnit::class, 'sarpras_unit_id');
    }

    /**
     * Get data barang hilang (jika ada)
     */
    public function barangHilang(): HasOne
    {
        return $this->hasOne(BarangHilang::class, 'pengembalian_detail_id');
    }
}
