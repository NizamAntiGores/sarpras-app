<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel maintenances untuk tracking maintenance per unit
     */
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sarpras_unit_id')->constrained('sarpras_units')->onDelete('cascade');
            $table->foreignId('petugas_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('jenis', ['perbaikan', 'servis_rutin', 'kalibrasi', 'penggantian_komponen'])->default('perbaikan');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->integer('biaya')->nullable();
            $table->enum('status', ['sedang_berlangsung', 'selesai', 'dibatalkan'])->default('sedang_berlangsung');
            $table->timestamps();

            // Index untuk query yang sering digunakan
            $table->index(['sarpras_unit_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
