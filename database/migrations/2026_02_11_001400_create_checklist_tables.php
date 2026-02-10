<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * 3 tabel baru untuk fitur Checklist Kondisi Barang:
     * 
     * 1. checklist_templates: Template item checklist per sarpras (jenis barang)
     *    Contoh: Kipas Angin → "Rotary berfungsi baik", "Body normal", "Kabel normal"
     * 
     * 2. checklist_handover: Hasil checklist saat serah terima (handover)
     *    Dicatat oleh petugas saat menyerahkan barang ke peminjam.
     * 
     * 3. checklist_pengembalian: Hasil checklist saat pengembalian
     *    Dicatat oleh petugas saat menerima barang kembali dari peminjam.
     *    Sistem akan membandingkan dengan checklist_handover.
     */
    public function up(): void
    {
        // 1. Template checklist per jenis barang (sarpras)
        Schema::create('checklist_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sarpras_id')->constrained('sarpras')->cascadeOnDelete();
            $table->string('item_label'); // e.g. "Rotary berfungsi baik"
            $table->integer('urutan')->default(0); // Urutan tampil
            $table->timestamps();
        });

        // 2. Hasil checklist saat serah terima (handover)
        Schema::create('checklist_handover', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_detail_id')->constrained('peminjaman_details')->cascadeOnDelete();
            $table->foreignId('checklist_template_id')->constrained('checklist_templates')->cascadeOnDelete();
            $table->boolean('is_checked')->default(true); // true = OK, false = Ada masalah
            $table->string('catatan')->nullable(); // Catatan opsional per item
            $table->timestamps();
        });

        // 3. Hasil checklist saat pengembalian
        Schema::create('checklist_pengembalian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_detail_id')->constrained('peminjaman_details')->cascadeOnDelete();
            $table->foreignId('checklist_template_id')->constrained('checklist_templates')->cascadeOnDelete();
            $table->boolean('is_checked')->default(true); // true = OK, false = Ada masalah
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_pengembalian');
        Schema::dropIfExists('checklist_handover');
        Schema::dropIfExists('checklist_templates');
    }
};
