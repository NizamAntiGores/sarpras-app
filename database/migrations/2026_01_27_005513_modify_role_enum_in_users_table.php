<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'guru' and 'siswa' to the role enum
        // We use raw SQL because doctrine/dbal might not be installed or support enum changes well
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'petugas', 'peminjam', 'guru', 'siswa') NOT NULL DEFAULT 'peminjam'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum
        // WARNING: This might fail if there are users with 'guru' or 'siswa' roles
        // We generally shouldn't lose data in down(), but for structure reversion:
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'petugas', 'peminjam') NOT NULL DEFAULT 'peminjam'");
    }
};
