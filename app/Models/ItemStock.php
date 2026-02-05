<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemStock extends Model
{
    use HasFactory;

    protected $table = 'item_stocks';

    protected $fillable = [
        'sarpras_id',
        'lokasi_id',
        'quantity',
    ];

    /**
     * Get the sarpras that owns the stock.
     */
    public function sarpras(): BelongsTo
    {
        return $this->belongsTo(Sarpras::class, 'sarpras_id');
    }

    /**
     * Get the lokasi for this stock.
     */
    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }
}
