<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'nomor_induk',
        'email',
        'password',
        'role',
        'kontak',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is petugas
     */
    public function isPetugas(): bool
    {
        return $this->role === 'petugas';
    }

    /**
     * Check if user is peminjam
     */
    public function isPeminjam(): bool
    {
        return $this->role === 'peminjam';
    }

    /**
     * Peminjaman yang dilakukan oleh user ini (sebagai peminjam)
     */
    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'user_id');
    }

    /**
     * Peminjaman yang disetujui oleh user ini (sebagai petugas)
     */
    public function peminjamanDiproses(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'petugas_id');
    }

    /**
     * Pengembalian yang diterima oleh user ini (sebagai petugas)
     */
    public function pengembalian(): HasMany
    {
        return $this->hasMany(Pengembalian::class, 'petugas_id');
    }
}
