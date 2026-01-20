<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel pengembalian_details untuk tracking kondisi unit saat dikembalikan
     */
    public function up(): void
    {
        Schema::create('pengembalian_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengembalian_id')->constrained('pengembalian')->onDelete('cascade');
            $table->foreignId('sarpras_unit_id')->constrained('sarpras_units')->onDelete('cascade');
            $table->enum('kondisi_akhir', ['baik', 'rusak_ringan', 'rusak_berat', 'hilang'])->default('baik');
            $table->string('foto_kondisi')->nullable();
            $table->text('catatan')->nullable();
            $table->integer('denda')->nullable();
            $table->timestamps();

            // Unique constraint: satu unit hanya bisa satu kali per pengembalian
            $table->unique(['pengembalian_id', 'sarpras_unit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian_details');
    }
};
