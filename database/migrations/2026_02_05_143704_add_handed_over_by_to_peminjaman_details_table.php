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
        Schema::table('peminjaman_details', function (Blueprint $table) {
            $table->foreignId('handed_over_by')->nullable()->after('handed_over_at')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman_details', function (Blueprint $table) {
            $table->dropForeign(['handed_over_by']);
            $table->dropColumn('handed_over_by');
        });
    }
};
