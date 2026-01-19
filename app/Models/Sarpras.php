<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Sarpras extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan
     */
    protected $table = 'sarpras';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'foto',
        'kategori_id',
        'lokasi_id',
        'stok',
        'stok_rusak',
        'kondisi_awal',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'stok' => 'integer',
        'stok_rusak' => 'integer',
    ];

    /**
     * Accessor untuk menghitung total aset (stok tersedia + stok rusak)
     */
    protected function totalAset(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->stok + $this->stok_rusak,
        );
    }

    /**
     * Get kategori dari sarpras ini
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    /**
     * Get lokasi dari sarpras ini
     */
    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    /**
     * Get all peminjaman untuk sarpras ini
     */
    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'sarpras_id');
    }
}

