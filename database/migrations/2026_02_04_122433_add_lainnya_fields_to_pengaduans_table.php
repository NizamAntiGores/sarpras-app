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
            // Field untuk input manual jika pilih "Lainnya"
            $table->string('lokasi_lainnya')->nullable()->after('lokasi_id');
            $table->string('barang_lainnya')->nullable()->after('sarpras_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaduans', function (Blueprint $table) {
            $table->dropColumn(['lokasi_lainnya', 'barang_lainnya']);
        });
    }
};
