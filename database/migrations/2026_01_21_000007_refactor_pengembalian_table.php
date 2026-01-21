<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Refactor tabel pengembalian: hapus kolom kondisi_akhir, foto_kondisi, denda
     * karena sudah dipindah ke pengembalian_details per unit
     */
    public function up(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            if (Schema::hasColumn('pengembalian', 'kondisi_akhir')) {
                $table->dropColumn('kondisi_akhir');
            }
            if (Schema::hasColumn('pengembalian', 'foto_kondisi')) {
                $table->dropColumn('foto_kondisi');
            }
            if (Schema::hasColumn('pengembalian', 'denda')) {
                $table->dropColumn('denda');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            if (!Schema::hasColumn('pengembalian', 'kondisi_akhir')) {
                $table->enum('kondisi_akhir', ['baik', 'rusak_ringan', 'rusak_berat', 'hilang'])->default('baik');
            }
            if (!Schema::hasColumn('pengembalian', 'foto_kondisi')) {
                $table->string('foto_kondisi')->nullable();
            }
            if (!Schema::hasColumn('pengembalian', 'denda')) {
                $table->integer('denda')->nullable();
            }
        });
    }
};
