<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'link',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    // Notification Types
    const TYPE_PEMINJAMAN_APPROVED = 'peminjaman_approved';

    const TYPE_PEMINJAMAN_REJECTED = 'peminjaman_rejected';

    const TYPE_PENGADUAN_UPDATED = 'pengaduan_updated';

    const TYPE_REMINDER_JATUH_TEMPO = 'reminder_jatuh_tempo';

    /**
     * User penerima notifikasi
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk notifikasi yang belum dibaca
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Mark as read
     */
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Helper untuk membuat notifikasi
     */
    public static function send($userId, $type, $title, $message, $link = null)
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
        ]);
    }
}
