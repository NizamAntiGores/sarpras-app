<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Migrate existing lokasi data to lokasi table
        $existingLokasi = DB::table('sarpras')
            ->select('lokasi')
            ->distinct()
            ->whereNotNull('lokasi')
            ->get();

        foreach ($existingLokasi as $lok) {
            DB::table('lokasi')->insertOrIgnore([
                'nama_lokasi' => $lok->lokasi,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Step 2: Add lokasi_id column
        Schema::table('sarpras', function (Blueprint $table) {
            $table->foreignId('lokasi_id')->nullable()->after('kategori_id')->constrained('lokasi')->nullOnDelete();
        });

        // Step 3: Update lokasi_id based on lokasi text
        $lokasiMap = DB::table('lokasi')->pluck('id', 'nama_lokasi');

        foreach ($lokasiMap as $nama => $id) {
            DB::table('sarpras')
                ->where('lokasi', $nama)
                ->update(['lokasi_id' => $id]);
        }

        // Step 4: Drop old lokasi column
        Schema::table('sarpras', function (Blueprint $table) {
            $table->dropColumn('lokasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Add back lokasi column
        Schema::table('sarpras', function (Blueprint $table) {
            $table->string('lokasi')->nullable()->after('kategori_id');
        });

        // Step 2: Copy data back from lokasi_id
        $sarpras = DB::table('sarpras')
            ->join('lokasi', 'sarpras.lokasi_id', '=', 'lokasi.id')
            ->select('sarpras.id', 'lokasi.nama_lokasi')
            ->get();

        foreach ($sarpras as $item) {
            DB::table('sarpras')
                ->where('id', $item->id)
                ->update(['lokasi' => $item->nama_lokasi]);
        }

        // Step 3: Drop lokasi_id FK
        Schema::table('sarpras', function (Blueprint $table) {
            $table->dropForeign(['lokasi_id']);
            $table->dropColumn('lokasi_id');
        });
    }
};
