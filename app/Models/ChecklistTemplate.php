<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistTemplate extends Model
{
    use HasFactory;

    protected $table = 'checklist_templates';

    protected $fillable = [
        'sarpras_id',
        'item_label',
        'urutan',
    ];

    /**
     * Get sarpras (jenis barang) yang memiliki template ini
     */
    public function sarpras(): BelongsTo
    {
        return $this->belongsTo(Sarpras::class, 'sarpras_id');
    }
}
