<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel sarpras_units untuk tracking unit individu
     */
    public function up(): void
    {
        Schema::create('sarpras_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sarpras_id')->constrained('sarpras')->onDelete('cascade');
            $table->string('kode_unit', 100)->unique();
            $table->foreignId('lokasi_id')->nullable()->constrained('lokasi')->onDelete('set null');
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->enum('status', ['tersedia', 'dipinjam', 'maintenance', 'dihapusbukukan'])->default('tersedia');
            $table->date('tanggal_perolehan')->nullable();
            $table->integer('nilai_perolehan')->nullable();
            $table->timestamps();

            // Index untuk query yang sering digunakan
            $table->index(['sarpras_id', 'status']);
            $table->index('kondisi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sarpras_units');
    }
};
