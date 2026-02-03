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
        Schema::create('student_whitelists', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_induk', 30)->unique(); // NISN untuk siswa, NIP untuk guru
            $table->string('nama', 255);
            $table->string('kelas', 20)->nullable(); // Hanya untuk siswa
            $table->enum('role', ['siswa', 'guru'])->default('siswa');
            $table->boolean('is_registered')->default(false); // Tandai jika sudah register
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_whitelists');
    }
};
