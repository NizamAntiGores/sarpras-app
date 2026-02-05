<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeminjamanDetail extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan
     */
    protected $table = 'peminjaman_details';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'peminjaman_id',
        'sarpras_unit_id',
        'sarpras_id',
        'quantity',
        'handed_over_at',
        'handed_over_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'handed_over_at' => 'datetime',
    ];

    /**
     * Get peminjaman header
     */
    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    /**
     * Get petugas yang menyerahkan barang
     */
    public function handedOverBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handed_over_by')->withTrashed();
    }

    /**
     * Get unit yang dipinjam (Nullable if Consumable)
     */
    public function sarprasUnit(): BelongsTo
    {
        return $this->belongsTo(SarprasUnit::class, 'sarpras_unit_id');
    }

    /**
     * Get master barang (Optional/Redundant for Asset, Mandatory for Consumable if not using Unit)
     */
    public function sarpras(): BelongsTo
    {
        return $this->belongsTo(Sarpras::class, 'sarpras_id');
    }
}
