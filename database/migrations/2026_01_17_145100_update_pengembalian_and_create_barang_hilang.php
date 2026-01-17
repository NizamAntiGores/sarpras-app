<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update tabel pengembalian - tambah foto dan ubah enum kondisi
        Schema::table('pengembalian', function (Blueprint $table) {
            $table->string('foto_kondisi')->nullable()->after('kondisi_akhir');
        });

        // Update enum kondisi_akhir menggunakan raw SQL (MySQL)
        DB::statement("ALTER TABLE pengembalian MODIFY COLUMN kondisi_akhir ENUM('baik', 'rusak_ringan', 'rusak_berat', 'hilang') DEFAULT 'baik'");

        // 2. Buat tabel baru untuk tracking barang hilang
        Schema::create('barang_hilang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengembalian_id')->constrained('pengembalian')->onDelete('cascade');
            $table->foreignId('sarpras_id')->constrained('sarpras')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Peminjam yang menghilangkan
            $table->integer('jumlah')->default(1);
            $table->text('keterangan')->nullable();
            $table->enum('status', ['belum_diganti', 'sudah_diganti'])->default('belum_diganti');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_hilang');

        Schema::table('pengembalian', function (Blueprint $table) {
            $table->dropColumn('foto_kondisi');
        });

        DB::statement("ALTER TABLE pengembalian MODIFY COLUMN kondisi_akhir ENUM('baik', 'rusak', 'hilang') DEFAULT 'baik'");
    }
};
