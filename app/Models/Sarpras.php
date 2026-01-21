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
        'deskripsi',
        'kategori_id',
    ];

    /**
     * Get kategori dari sarpras ini
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    /**
     * Get semua unit untuk sarpras ini
     */
    public function units(): HasMany
    {
        return $this->hasMany(SarprasUnit::class, 'sarpras_id');
    }

    /**
     * Get unit yang aktif (tidak dihapusbukukan)
     */
    public function activeUnits(): HasMany
    {
        return $this->units()->where('status', '!=', SarprasUnit::STATUS_DIHAPUSBUKUKAN);
    }

    /**
     * Accessor untuk total unit aktif
     */
    protected function totalUnit(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->activeUnits()->count(),
        );
    }

    /**
     * Accessor untuk stok tersedia (unit yang bisa dipinjam)
     */
    protected function stokTersedia(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->units()
                ->where('status', SarprasUnit::STATUS_TERSEDIA)
                ->where('kondisi', '!=', SarprasUnit::KONDISI_RUSAK_BERAT)
                ->count(),
        );
    }

    /**
     * Accessor untuk jumlah unit dipinjam
     */
    protected function jumlahDipinjam(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->units()
                ->where('status', SarprasUnit::STATUS_DIPINJAM)
                ->count(),
        );
    }

    /**
     * Accessor untuk jumlah unit maintenance
     */
    protected function jumlahMaintenance(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->units()
                ->where('status', SarprasUnit::STATUS_MAINTENANCE)
                ->count(),
        );
    }

    /**
     * Accessor untuk jumlah unit rusak (semua jenis kerusakan)
     */
    protected function jumlahRusak(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->units()
                ->where('kondisi', '!=', SarprasUnit::KONDISI_BAIK)
                ->where('status', '!=', SarprasUnit::STATUS_DIHAPUSBUKUKAN)
                ->count(),
        );
    }
}
