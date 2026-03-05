<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Rename parishes → counties (idempotent)
        if (Schema::hasTable('parishes')) {
            Schema::rename('parishes', 'counties');
        }

        // 2. Rename parish_id → county_id on contestants (idempotent)
        if (Schema::hasColumn('contestants', 'parish_id')) {
            // Drop the old FK if it still exists
            $parishFkExists = DB::select("
                SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'contestants'
                  AND COLUMN_NAME = 'parish_id'
                  AND REFERENCED_TABLE_NAME IS NOT NULL
                LIMIT 1
            ");

            Schema::table('contestants', function (Blueprint $table) use ($parishFkExists) {
                if (!empty($parishFkExists)) {
                    $table->dropForeign(['parish_id']);
                }
                $table->renameColumn('parish_id', 'county_id');
            });
        }

        // 3. Ensure county_id is nullable, then add FK (drop first if partially added)
        $countyFkExists = DB::select("
            SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'contestants'
              AND COLUMN_NAME = 'county_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
            LIMIT 1
        ");

        if (empty($countyFkExists)) {
            Schema::table('contestants', function (Blueprint $table) {
                $table->unsignedBigInteger('county_id')->nullable()->change();
                $table->foreign('county_id')->references('id')->on('counties')->nullOnDelete();
            });
        }
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
