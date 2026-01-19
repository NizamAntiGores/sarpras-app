<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Peminjaman extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan
     */
    protected $table = 'peminjaman';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'sarpras_id',
        'petugas_id',
        'jumlah_pinjam',
        'tgl_pinjam',
        'tgl_kembali_rencana',
        'status',
        'qr_code',
        'keterangan',
        'catatan_petugas',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'jumlah_pinjam' => 'integer',
        'tgl_pinjam' => 'date',
        'tgl_kembali_rencana' => 'date',
    ];

    /**
     * Get user peminjam
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias untuk user (peminjam)
     */
    public function peminjam(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get sarpras yang dipinjam
     */
    public function sarpras(): BelongsTo
    {
        return $this->belongsTo(Sarpras::class, 'sarpras_id');
    }

    /**
     * Get petugas yang menyetujui
     */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    /**
     * Get pengembalian untuk peminjaman ini
     */
    public function pengembalian(): HasOne
    {
        return $this->hasOne(Pengembalian::class, 'peminjaman_id');
    }
}
