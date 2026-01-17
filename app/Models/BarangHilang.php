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
        'pengembalian_id',
        'sarpras_id',
        'user_id',
        'jumlah',
        'keterangan',
        'status',
    ];

    /**
     * Get pengembalian terkait
     */
    public function pengembalian(): BelongsTo
    {
        return $this->belongsTo(Pengembalian::class, 'pengembalian_id');
    }

    /**
     * Get sarpras yang hilang
     */
    public function sarpras(): BelongsTo
    {
        return $this->belongsTo(Sarpras::class, 'sarpras_id');
    }

    /**
     * Get user yang menghilangkan
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
