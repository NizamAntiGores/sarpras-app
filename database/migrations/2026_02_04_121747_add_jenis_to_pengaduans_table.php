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
        Schema::table('pengaduans', function (Blueprint $table) {
            // Jenis pengaduan: 'tempat' atau 'barang'
            $table->enum('jenis', ['tempat', 'barang'])->default('tempat')->after('user_id');
            
            // Lokasi jadi nullable karena kalau jenis 'barang' tidak wajib
            $table->foreignId('lokasi_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaduans', function (Blueprint $table) {
            $table->dropColumn('jenis');
        });
    }
};
