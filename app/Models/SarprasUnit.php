<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SarprasUnit extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan
     */
    protected $table = 'sarpras_units';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sarpras_id',
        'kode_unit',
        'lokasi_id',
        'kondisi',
        'status',
        'tanggal_perolehan',
        'nilai_perolehan',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'tanggal_perolehan' => 'date',
        'nilai_perolehan' => 'integer',
    ];

    /**
     * Konstanta untuk kondisi
     */
    const KONDISI_BAIK = 'baik';
    const KONDISI_RUSAK_RINGAN = 'rusak_ringan';
    const KONDISI_RUSAK_BERAT = 'rusak_berat';

    /**
     * Konstanta untuk status
     */
    const STATUS_TERSEDIA = 'tersedia';
    const STATUS_DIPINJAM = 'dipinjam';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_DIHAPUSBUKUKAN = 'dihapusbukukan';
    const STATUS_TERPAKAI = 'terpakai'; // Untuk bahan habis pakai yang sudah dikonsumsi

    /**
     * Get master barang (sarpras)
     */
    public function sarpras(): BelongsTo
    {
        return $this->belongsTo(Sarpras::class, 'sarpras_id')->withTrashed();
    }

    /**
     * Get lokasi penyimpanan
     */
    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    /**
     * Get semua detail peminjaman untuk unit ini
     */
    public function peminjamanDetails(): HasMany
    {
        return $this->hasMany(PeminjamanDetail::class, 'sarpras_unit_id');
    }

    /**
     * Get semua detail pengembalian untuk unit ini
     */
    public function pengembalianDetails(): HasMany
    {
        return $this->hasMany(PengembalianDetail::class, 'sarpras_unit_id');
    }

    /**
     * Get semua maintenance untuk unit ini
     */
    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class, 'sarpras_unit_id');
    }

    /**
     * Scope untuk unit yang tersedia
     */
    public function scopeTersedia($query)
    {
        return $query->where('status', self::STATUS_TERSEDIA);
    }

    /**
     * Scope untuk unit yang aktif (tidak dihapusbukukan)
     */
    public function scopeAktif($query)
    {
        return $query->whereNotIn('status', [self::STATUS_DIHAPUSBUKUKAN, self::STATUS_TERPAKAI]);
    }

    /**
     * Scope untuk unit yang tampil di daftar (alias dari aktif)
     */
    public function scopeVisibleInList($query)
    {
        return $query->aktif();
    }

    /**
     * Scope untuk unit dengan kondisi baik
     */
    public function scopeKondisiBaik($query)
    {
        return $query->where('kondisi', self::KONDISI_BAIK);
    }

    /**
     * Scope untuk unit yang bisa dipinjam (tersedia dan kondisi tidak rusak berat)
     */
    public function scopeBisaDipinjam($query)
    {
        return $query->where('status', self::STATUS_TERSEDIA)
            ->where('kondisi', '!=', self::KONDISI_RUSAK_BERAT)
            ->whereDoesntHave('peminjamanDetails', function($q) {
                $q->whereHas('peminjaman', function($subQ) {
                    $subQ->where('status', 'menunggu');
                });
            });
    }

    /**
     * Cek apakah unit bisa dipinjam
     */
    public function canBeBorrowed(): bool
    {
        return $this->status === self::STATUS_TERSEDIA 
            && $this->kondisi !== self::KONDISI_RUSAK_BERAT;
    }

    /**
     * Cek apakah unit adalah bahan habis pakai
     */
    public function isBahan(): bool
    {
        return $this->sarpras->tipe === 'bahan';
    }

    /**
     * Generate kode unit otomatis
     */
    public static function generateKodeUnit(Sarpras $sarpras): string
    {
        $kodeBarang = $sarpras->kode_barang;
        
        // Hitung jumlah unit yang sudah ada untuk sarpras ini
        $lastUnit = self::where('sarpras_id', $sarpras->id)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastUnit) {
            // Extract nomor dari kode terakhir
            $parts = explode('-', $lastUnit->kode_unit);
            $lastNumber = intval(end($parts));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $kodeBarang . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
