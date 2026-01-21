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
    ];

    /**
     * Get peminjaman header
     */
    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    /**
     * Get unit yang dipinjam
     */
    public function sarprasUnit(): BelongsTo
    {
        return $this->belongsTo(SarprasUnit::class, 'sarpras_unit_id');
    }
}
