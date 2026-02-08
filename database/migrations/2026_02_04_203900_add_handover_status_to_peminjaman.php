<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Menambahkan kolom untuk tracking serah terima:
     * - handover_at: waktu saat barang diserahkan ke peminjam
     * - handover_by: petugas yang menyerahkan barang
     */
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->timestamp('handover_at')->nullable()->after('qr_code');
            $table->foreignId('handover_by')->nullable()->after('handover_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropForeign(['handover_by']);
            $table->dropColumn(['handover_at', 'handover_by']);
        });
    }
};
