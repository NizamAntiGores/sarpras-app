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
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'tgl_pinjam' => 'date',
        'tgl_kembali_rencana' => 'date',
    ];

    /**
     * Konstanta untuk status
     */
    const STATUS_MENUNGGU = 'menunggu';

    const STATUS_DISETUJUI = 'disetujui';

    const STATUS_SELESAI = 'selesai';

    const STATUS_DITOLAK = 'ditolak';

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
     * Scope untuk peminjaman yang aktif (disetujui tapi belum selesai)
     */
    public function scopeAktif($query)
    {
        return $query->where('status', self::STATUS_DISETUJUI);
    }

    /**
     * Scope untuk peminjaman menunggu approval
     */
    public function scopeMenunggu($query)
    {
        return $query->where('status', self::STATUS_MENUNGGU);
    }

    /**
     * Get jumlah unit yang dipinjam
     */
    public function getJumlahUnitAttribute(): int
    {
        return $this->details()->count();
    }
}
