<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mengubah struktur sarpras untuk mendukung Quantity-based Maintenance
     * - Hapus kolom status_barang (enum)
     * - Tambah kolom stok_rusak (integer)
     */
    public function up(): void
    {
        Schema::table('sarpras', function (Blueprint $table) {
            // Hapus kolom status_barang
            $table->dropColumn('status_barang');

            // Tambah kolom stok_rusak
            $table->integer('stok_rusak')->default(0)->after('stok');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sarpras', function (Blueprint $table) {
            // Kembalikan kolom status_barang
            $table->enum('status_barang', ['tersedia', 'dipinjam', 'maintenance'])->default('tersedia')->after('kondisi_awal');

            // Hapus kolom stok_rusak
            $table->dropColumn('stok_rusak');
        });
    }
};
