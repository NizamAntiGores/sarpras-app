<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan
     */
    protected $table = 'maintenances';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sarpras_unit_id',
        'petugas_id',
        'jenis',
        'deskripsi',
        'tanggal_mulai',
        'tanggal_selesai',
        'biaya',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'biaya' => 'integer',
    ];

    /**
     * Konstanta untuk jenis maintenance
     */
    const JENIS_PERBAIKAN = 'perbaikan';

    const JENIS_SERVIS_RUTIN = 'servis_rutin';

    const JENIS_KALIBRASI = 'kalibrasi';

    const JENIS_PENGGANTIAN_KOMPONEN = 'penggantian_komponen';

    /**
     * Konstanta untuk status
     */
    const STATUS_SEDANG_BERLANGSUNG = 'sedang_berlangsung';

    const STATUS_SELESAI = 'selesai';

    const STATUS_DIBATALKAN = 'dibatalkan';

    /**
     * Get unit yang di-maintenance
     */
    public function sarprasUnit(): BelongsTo
    {
        return $this->belongsTo(SarprasUnit::class, 'sarpras_unit_id');
    }

    /**
     * Get petugas yang menangani
     */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    /**
     * Scope untuk maintenance yang sedang berlangsung
     */
    public function scopeSedangBerlangsung($query)
    {
        return $query->where('status', self::STATUS_SEDANG_BERLANGSUNG);
    }

    /**
     * Scope untuk maintenance yang selesai
     */
    public function scopeSelesai($query)
    {
        return $query->where('status', self::STATUS_SELESAI);
    }
}
