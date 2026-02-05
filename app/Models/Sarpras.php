<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sarpras extends Model
{
    use HasFactory, SoftDeletes;

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
        'tipe',
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
        return $this->units()->whereNotIn('status', [SarprasUnit::STATUS_DIHAPUSBUKUKAN, SarprasUnit::STATUS_TERPAKAI]);
    }

    /**
     * Accessor untuk total unit aktif
     */
    protected function totalUnit(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->activeUnits()->count(),
        );
    }

    /**
     * Get inventory stocks (for consumables)
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(ItemStock::class, 'sarpras_id');
    }

    /**
     * Accessor untuk stok tersedia (unit yang bisa dipinjam)
     * Menggunakan konsep "Storefront" (Hanya barang di lokasi is_storefront=true)
     */
    protected function stokTersedia(): Attribute
    {
        return Attribute::make(
            get: function () {
                // LOGIC 1: ASSET (Type A) - Track by Unit
                // ----------------------------------------
                if ($this->tipe !== 'bahan') { // Assuming 'bahan' is the identifier for Consumable
    
                    // Ambil unit yang fisik di Storefront & Available
                    $physicallyAvailable = $this->units()
                        ->whereHas('lokasi', fn($q) => $q->where('is_storefront', true))
                        ->where('status', SarprasUnit::STATUS_TERSEDIA)
                        ->where('kondisi', '!=', SarprasUnit::KONDISI_RUSAK_BERAT);

                    // Exclude yang sedang di-booking (Pending Peminjaman)
                    $availableUnitIds = $physicallyAvailable->pluck('id');

                    $pendingCount = \App\Models\PeminjamanDetail::whereIn('sarpras_unit_id', $availableUnitIds)
                        ->whereHas('peminjaman', function ($q) {
                            $q->where('status', 'menunggu');
                        })
                        ->count();

                    return $physicallyAvailable->count() - $pendingCount;
                }

                // LOGIC 2: CONSUMABLE (Type B) - Track by Quantity
                // ----------------------------------------
                else {
                    // 1. Total Fisik di Storefront
                    $physicalQty = $this->stocks()
                        ->whereHas('lokasi', fn($q) => $q->where('is_storefront', true))
                        ->sum('quantity');

                    // 2. Total yang sedang diminta (Pending)
                    // 2. Total yang sedang diminta (Pending)
                    $pendingQty = \App\Models\PeminjamanDetail::whereHas('peminjaman', fn($q) => $q->where('status', 'menunggu'))
                        ->where('sarpras_id', $this->id)
                        ->sum('quantity');

                    return max(0, $physicalQty - $pendingQty);
                }
            }
        );
    }

    /**
     * Accessor untuk jumlah unit dipinjam
     */
    protected function jumlahDipinjam(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->units()
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
            get: fn() => $this->units()
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
            get: fn() => $this->units()
                ->where('kondisi', '!=', SarprasUnit::KONDISI_BAIK)
                ->whereNotIn('status', [SarprasUnit::STATUS_DIHAPUSBUKUKAN, SarprasUnit::STATUS_TERPAKAI])
                ->count(),
        );
    }
}
