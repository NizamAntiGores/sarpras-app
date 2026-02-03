<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentWhitelist extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_induk',
        'nama',
        'kelas',
        'role',
        'is_registered',
    ];

    protected $casts = [
        'is_registered' => 'boolean',
    ];

    /**
     * Cari berdasarkan nomor induk (NISN/NIP)
     */
    public static function findByNomorInduk(string $nomorInduk): ?self
    {
        return static::where('nomor_induk', $nomorInduk)->first();
    }

    /**
     * Cek apakah nomor induk valid dan belum terdaftar
     */
    public static function isValidAndAvailable(string $nomorInduk): bool
    {
        $whitelist = static::findByNomorInduk($nomorInduk);
        return $whitelist && !$whitelist->is_registered;
    }

    /**
     * Tandai sebagai sudah terdaftar
     */
    public function markAsRegistered(): void
    {
        $this->update(['is_registered' => true]);
    }
}
