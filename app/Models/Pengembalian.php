<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pengembalian extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan
     */
    protected $table = 'pengembalian';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'peminjaman_id',
        'petugas_id',
        'tgl_kembali_aktual',
        'kondisi_akhir',
        'foto_kondisi',
        'denda',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tgl_kembali_aktual' => 'date',
        'denda' => 'integer',
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
     * Get data barang hilang jika ada
     */
    public function barangHilang(): HasOne
    {
        return $this->hasOne(BarangHilang::class, 'pengembalian_id');
    }
}
