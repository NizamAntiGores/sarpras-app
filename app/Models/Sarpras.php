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
            get: fn () => $this->activeUnits()->count(),
        );
    }

    /**
     * Accessor untuk stok tersedia (unit yang bisa dipinjam)
     */
    protected function stokTersedia(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Ambil semua unit yang secara fisik tersedia
                $physicallyAvailable = $this->units()
                    ->where('status', SarprasUnit::STATUS_TERSEDIA)
                    ->where('kondisi', '!=', SarprasUnit::KONDISI_RUSAK_BERAT);

                // Hitung ID unit yang sedang dalam pengajuan 'menunggu'
                // Kita harus join ke tabel peminjaman_detail lalu ke peminjaman
                // Di Laravel Eloquent, kita bisa pakai whereHas di level unit
                // Tapi karena kita sudah di level Sarpras, kita ambil unit ID-nya saja.

                // Ambil ID unit milik sarpras ini yang statusnya 'tersedia'
                $availableUnitIds = $physicallyAvailable->pluck('id');

                // Cek dari ID tersebut, mana yang ada di peminjaman dengan status 'menunggu'
                $pendingCount = \App\Models\PeminjamanDetail::whereIn('sarpras_unit_id', $availableUnitIds)
                    ->whereHas('peminjaman', function ($q) {
                        $q->where('status', 'menunggu');
                    })
                    ->count();

                // Stok Tampil = Fisik Tersedia - Sedang Diajukan
                return $physicallyAvailable->count() - $pendingCount;
            }
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
                ->whereNotIn('status', [SarprasUnit::STATUS_DIHAPUSBUKUKAN, SarprasUnit::STATUS_TERPAKAI])
                ->count(),
        );
    }
}
