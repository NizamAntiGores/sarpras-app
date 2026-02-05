<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('peminjaman_details', function (Blueprint $table) {
            // Make unit_id nullable for Consumables
            $table->unsignedBigInteger('sarpras_unit_id')->nullable()->change();

            // Add sarpras_id to link Consumables directly (or Assets redundantly)
            $table->foreignId('sarpras_id')->nullable()->after('peminjaman_id')->constrained('sarpras')->onDelete('cascade');

            // Add quantity for Consumables
            $table->integer('quantity')->default(1)->after('sarpras_unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman_details', function (Blueprint $table) {
            $table->dropForeign(['sarpras_id']);
            $table->dropColumn(['sarpras_id', 'quantity']);
            $table->unsignedBigInteger('sarpras_unit_id')->nullable(false)->change();
        });
    }
};
