<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Peminjaman extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel yang digunakan
     */
    protected $table = 'peminjaman';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'petugas_id',
        'tgl_pinjam',
        'tgl_kembali_rencana',
        'status',
        'qr_code',
        'keterangan',
        'catatan_petugas',
        'handover_at',
        'handover_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'tgl_pinjam' => 'date',
        'tgl_kembali_rencana' => 'date',
        'handover_at' => 'datetime',
    ];

    /**
     * Konstanta untuk status
     */
    const STATUS_MENUNGGU = 'menunggu';

    const STATUS_DISETUJUI = 'disetujui';

    const STATUS_SELESAI = 'selesai';

    const STATUS_DITOLAK = 'ditolak';

    const STATUS_DIPINJAM = 'dipinjam';

    /**
     * Get user peminjam
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    /**
     * Alias untuk user (peminjam)
     */
    public function peminjam(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    /**
     * Get petugas yang menyetujui
     */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id')->withTrashed();
    }

    /**
     * Get detail peminjaman (unit-unit yang dipinjam)
     */
    public function details(): HasMany
    {
        return $this->hasMany(PeminjamanDetail::class, 'peminjaman_id');
    }

    /**
     * Get semua unit yang dipinjam melalui details
     */
    public function units(): HasManyThrough
    {
        return $this->hasManyThrough(
            SarprasUnit::class,
            PeminjamanDetail::class,
            'peminjaman_id', // FK di peminjaman_details
            'id',            // PK di sarpras_units
            'id',            // PK di peminjaman
            'sarpras_unit_id' // FK di peminjaman_details ke sarpras_units
        );
    }

    /**
     * Get pengembalian untuk peminjaman ini
     */
    public function pengembalian(): HasOne
    {
        return $this->hasOne(Pengembalian::class, 'peminjaman_id');
    }

    /**
     * Get petugas yang menyerahkan barang (handover)
     */
    public function handoverPetugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handover_by')->withTrashed();
    }

    /**
     * Cek apakah barang sudah diserahkan (pickup sudah dilakukan - minimal 1 item)
     */
    public function isHandedOver(): bool
    {
        return $this->handover_at !== null;
    }

    /**
     * Check if there are any items that haven't been handed over yet.
     */
    public function hasPendingHandoverItems(): bool
    {
        // We need to load details to check this.
        // Using exists() on the relation is more efficient if details not loaded,
        // but if loaded, allow using collection.
        if ($this->relationLoaded('details')) {
            return $this->details->whereNull('handed_over_at')->isNotEmpty();
        }
        return $this->details()->whereNull('handed_over_at')->exists();
    }

    /**
     * Cek apakah peminjaman siap untuk pickup.
     * True jika Disetujui (belum diambil sama sekali)
     * OR Dipinjam tapi masih ada item sisa (partial pickup).
     */
    public function isReadyForPickup(): bool
    {
        if ($this->status === self::STATUS_DISETUJUI && !$this->isHandedOver()) {
            return true;
        }
        if ($this->status === self::STATUS_DIPINJAM && $this->hasPendingHandoverItems()) {
            return true;
        }
        return false;
    }

    /**
     * Cek apakah peminjaman sedang berjalan (Status Dipinjam).
     */
    public function isOngoing(): bool
    {
        return $this->status === self::STATUS_DIPINJAM || 
               ($this->status === self::STATUS_DISETUJUI && $this->isHandedOver());
    }

    /**
     * Scope untuk peminjaman yang aktif (disetujui atau dipinjam)
     */
    public function scopeAktif($query)
    {
        return $query->whereIn('status', [self::STATUS_DISETUJUI, self::STATUS_DIPINJAM]);
    }

    /**
     * Scope untuk peminjaman menunggu approval
     */
    public function scopeMenunggu($query)
    {
        return $query->where('status', self::STATUS_MENUNGGU);
    }

    /**
     * Scope untuk peminjaman yang sudah disetujui tapi belum diambil
     */
    public function scopeReadyForPickup($query)
    {
        return $query->where('status', self::STATUS_DISETUJUI)
            ->whereNull('handover_at');
    }

    /**
     * Scope untuk peminjaman yang sedang berjalan (sudah diambil)
     */
    public function scopeOngoing($query)
    {
        return $query->where('status', self::STATUS_DIPINJAM)
            ->orWhere(function($q) {
                $q->where('status', self::STATUS_DISETUJUI)
                  ->whereNotNull('handover_at');
            });
    }

    /**
     * Get jumlah unit yang dipinjam
     */
    public function getJumlahUnitAttribute(): int
    {
        return $this->details()->count();
    }
}
