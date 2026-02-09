<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitConditionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'sarpras_unit_id',
        'kondisi_lama',
        'kondisi_baru',
        'keterangan',
        'user_id',
        'related_model_type',
        'related_model_id',
    ];

    /**
     * Get the unit associated with the log.
     */
    public function sarprasUnit(): BelongsTo
    {
        return $this->belongsTo(SarprasUnit::class)->withTrashed();
    }

    /**
     * Get the user who made the change.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent related model (e.g. Peminjaman, Pengembalian).
     */
    public function relatedModel()
    {
        return $this->morphTo();
    }
}
