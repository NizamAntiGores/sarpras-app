<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Refactor tabel peminjaman: hapus kolom sarpras_id dan jumlah_pinjam
     * karena sudah dipindah ke peminjaman_details
     */
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            // Drop foreign key dulu
            if (Schema::hasColumn('peminjaman', 'sarpras_id')) {
                $table->dropForeign(['sarpras_id']);
                $table->dropColumn('sarpras_id');
            }

            if (Schema::hasColumn('peminjaman', 'jumlah_pinjam')) {
                $table->dropColumn('jumlah_pinjam');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            if (! Schema::hasColumn('peminjaman', 'sarpras_id')) {
                $table->foreignId('sarpras_id')->nullable()->constrained('sarpras')->onDelete('cascade');
            }
            if (! Schema::hasColumn('peminjaman', 'jumlah_pinjam')) {
                $table->integer('jumlah_pinjam')->default(1);
            }
        });
    }
};
