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
        Schema::table('sarpras_units', function (Blueprint $table) {
            if (Schema::hasColumn('sarpras_units', 'nilai_perolehan')) {
                $table->dropColumn('nilai_perolehan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sarpras_units', function (Blueprint $table) {
            if (! Schema::hasColumn('sarpras_units', 'nilai_perolehan')) {
                $table->integer('nilai_perolehan')->nullable();
            }
        });
    }
};
