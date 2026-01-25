<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Refactor tabel barang_hilang: ubah FK dari pengembalian_id ke pengembalian_detail_id
     */
    public function up(): void
    {
        // Hapus data lama karena sudah tidak relevan dengan struktur baru
        Schema::table('barang_hilang', function (Blueprint $table) {
            // Drop foreign keys yang ada
            if (Schema::hasColumn('barang_hilang', 'pengembalian_id')) {
                $table->dropForeign(['pengembalian_id']);
                $table->dropColumn('pengembalian_id');
            }
            if (Schema::hasColumn('barang_hilang', 'sarpras_id')) {
                $table->dropForeign(['sarpras_id']);
                $table->dropColumn('sarpras_id');
            }
            if (Schema::hasColumn('barang_hilang', 'jumlah')) {
                $table->dropColumn('jumlah');
            }
        });

        // Tambah kolom baru jika belum ada
        if (! Schema::hasColumn('barang_hilang', 'pengembalian_detail_id')) {
            Schema::table('barang_hilang', function (Blueprint $table) {
                $table->foreignId('pengembalian_detail_id')
                    ->after('id')
                    ->constrained('pengembalian_details')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_hilang', function (Blueprint $table) {
            $table->dropForeign(['pengembalian_detail_id']);
            $table->dropColumn('pengembalian_detail_id');

            $table->foreignId('pengembalian_id')->nullable()->constrained('pengembalian')->onDelete('cascade');
            $table->foreignId('sarpras_id')->nullable()->constrained('sarpras')->onDelete('cascade');
            $table->integer('jumlah')->default(1);
        });
    }
};
