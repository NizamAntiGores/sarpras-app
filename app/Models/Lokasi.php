<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lokasi extends Model
{
    use HasFactory;

    protected $table = 'lokasi';

    protected $fillable = [
        'nama_lokasi',
        'keterangan',
    ];

    /**
     * Get all sarpras di lokasi ini
     */
    public function sarpras(): HasMany
    {
        return $this->hasMany(Sarpras::class, 'lokasi_id');
    }

    /**
     * Get all units di lokasi ini
     */
    public function units(): HasMany
    {
        return $this->hasMany(SarprasUnit::class, 'lokasi_id');
    }
}
