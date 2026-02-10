<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistPengembalian extends Model
{
    use HasFactory;

    protected $table = 'checklist_pengembalian';

    protected $fillable = [
        'peminjaman_detail_id',
        'checklist_template_id',
        'is_checked',
        'catatan',
    ];

    protected $casts = [
        'is_checked' => 'boolean',
    ];

    /**
     * Get detail peminjaman
     */
    public function peminjamanDetail(): BelongsTo
    {
        return $this->belongsTo(PeminjamanDetail::class, 'peminjaman_detail_id');
    }

    /**
     * Get template checklist
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(ChecklistTemplate::class, 'checklist_template_id');
    }
}
