<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel pivot peminjaman_details untuk relasi peminjaman ke unit
     */
    public function up(): void
    {
        Schema::create('peminjaman_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjaman')->onDelete('cascade');
            $table->foreignId('sarpras_unit_id')->constrained('sarpras_units')->onDelete('cascade');
            $table->timestamps();

            // Unique constraint: satu unit hanya bisa satu kali per peminjaman
            $table->unique(['peminjaman_id', 'sarpras_unit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman_details');
    }
};
