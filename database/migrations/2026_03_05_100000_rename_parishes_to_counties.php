<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Rename parishes → counties
        Schema::rename('parishes', 'counties');

        // 2. Rename parish_id → county_id on contestants
        Schema::table('contestants', function (Blueprint $table) {
            $table->dropForeign(['parish_id']);
            $table->renameColumn('parish_id', 'county_id');
        });

        // 3. Re-add FK with new name
        Schema::table('contestants', function (Blueprint $table) {
            $table->foreign('county_id')->references('id')->on('counties')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contestants', function (Blueprint $table) {
            $table->dropForeign(['county_id']);
            $table->renameColumn('county_id', 'parish_id');
        });

        Schema::table('contestants', function (Blueprint $table) {
            $table->foreign('parish_id')->references('id')->on('counties')->nullOnDelete();
        });

        Schema::rename('counties', 'parishes');
    }
};
