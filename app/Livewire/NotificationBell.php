<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationBell extends Component
{
    public $unreadCount = 0;

    public function mount()
    {
        $this->updateCount();
    }

    public function updateCount()
    {
        $user = auth()->user();

        if (! $user) {
            $this->unreadCount = 0;

            return;
        }

        if ($user->role === 'admin' || $user->role === 'petugas') {
            // Admin/Petugas: Hitung pengajuan yang 'menunggu'
            $this->unreadCount = \App\Models\Peminjaman::where('status', 'menunggu')->count();
        } else {
            // User Biasa: Hitung Notifikasi (Disetujui/Ditolak/Selesai dalam 24 jam terakhir)
            $this->unreadCount = \App\Models\Peminjaman::where('user_id', $user->id)
                ->whereIn('status', ['disetujui', 'ditolak', 'selesai'])
                ->where('updated_at', '>=', now()->subDay())
                ->count();
        }
    }

    public function render()
    {
        // Auto-refresh logic handled by poll in view calling updateCount indirectly or just rely on render re-query
        // Actually, updateCount is called here to ensure fresh data on every poll request
        $this->updateCount();

        return view('livewire.notification-bell');
    }
}
