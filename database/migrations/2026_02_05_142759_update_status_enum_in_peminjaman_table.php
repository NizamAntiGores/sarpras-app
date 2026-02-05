<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'dipinjam' to the enum
        DB::statement("ALTER TABLE peminjaman MODIFY COLUMN status ENUM('menunggu', 'disetujui', 'selesai', 'ditolak', 'dipinjam') NOT NULL DEFAULT 'menunggu'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back (note: this might fail if there are 'dipinjam' records, but acceptable for down)
        DB::statement("ALTER TABLE peminjaman MODIFY COLUMN status ENUM('menunggu', 'disetujui', 'selesai', 'ditolak') NOT NULL DEFAULT 'menunggu'");
    }
};
