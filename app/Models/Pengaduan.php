<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengaduan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sarpras_id',
        'lokasi_id',
        'judul',
        'deskripsi',
        'foto',
        'status',
        'catatan_petugas',
        'petugas_id',
    ];

    // Status Constants
    const STATUS_BELUM = 'belum_ditindaklanjuti';
    const STATUS_PROSES = 'sedang_diproses';
    const STATUS_SELESAI = 'selesai';
    const STATUS_TUTUP = 'ditutup';

    /**
     * Pelapor
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Sarpras yang dilaporkan (optional)
     */
    public function sarpras(): BelongsTo
    {
        return $this->belongsTo(Sarpras::class);
    }

    /**
     * Lokasi kejadian (optional)
     */
    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class);
    }

    /**
     * Petugas yang menangani
     */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    /**
     * Scope untuk filter status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
