<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Refactor tabel sarpras: hapus kolom stok, kondisi karena sudah dipindah ke unit
     */
    public function up(): void
    {
        Schema::table('sarpras', function (Blueprint $table) {
            // Tambah kolom deskripsi jika belum ada
            if (!Schema::hasColumn('sarpras', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('foto');
            }
        });

        // Hapus kolom yang tidak diperlukan lagi setelah migrasi data selesai
        // Ini dilakukan terpisah untuk keamanan
        Schema::table('sarpras', function (Blueprint $table) {
            // Drop kolom stok-based jika ada
            if (Schema::hasColumn('sarpras', 'stok')) {
                $table->dropColumn('stok');
            }
            if (Schema::hasColumn('sarpras', 'stok_rusak')) {
                $table->dropColumn('stok_rusak');
            }
            if (Schema::hasColumn('sarpras', 'kondisi_awal')) {
                $table->dropColumn('kondisi_awal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sarpras', function (Blueprint $table) {
            // Kembalikan kolom stok-based
            if (!Schema::hasColumn('sarpras', 'stok')) {
                $table->integer('stok')->default(0);
            }
            if (!Schema::hasColumn('sarpras', 'stok_rusak')) {
                $table->integer('stok_rusak')->default(0);
            }
            if (!Schema::hasColumn('sarpras', 'kondisi_awal')) {
                $table->enum('kondisi_awal', ['baik', 'rusak'])->default('baik');
            }
            
            // Drop kolom deskripsi
            if (Schema::hasColumn('sarpras', 'deskripsi')) {
                $table->dropColumn('deskripsi');
            }
        });
    }
};
