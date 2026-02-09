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
        Schema::create('unit_condition_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sarpras_unit_id')->constrained('sarpras_units')->onDelete('cascade');
            $table->string('kondisi_lama');
            $table->string('kondisi_baru');
            $table->text('keterangan')->nullable(); // Alasan perubahan
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Siapa yang mengubah
            
            // Allow linking to other models (e.g. Peminjaman/Pengembalian) if needed for context
            $table->nullableMorphs('related_model'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_condition_logs');
    }
};
