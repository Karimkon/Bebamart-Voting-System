<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The competition_settings table still has number_of_parishes /
        // contestants_per_parish from the original migration.
        // Rename them to number_of_counties / contestants_per_county.

        // The table also has a STORED GENERATED column total_contestants
        // that depends on the old column names, so we must drop it first.

        if (Schema::hasColumn('competition_settings', 'total_contestants')) {
            Schema::table('competition_settings', function (Blueprint $table) {
                $table->dropColumn('total_contestants');
            });
        }

        if (Schema::hasColumn('competition_settings', 'number_of_parishes')) {
            Schema::table('competition_settings', function (Blueprint $table) {
                $table->renameColumn('number_of_parishes', 'number_of_counties');
            });
        }

        if (Schema::hasColumn('competition_settings', 'contestants_per_parish')) {
            Schema::table('competition_settings', function (Blueprint $table) {
                $table->renameColumn('contestants_per_parish', 'contestants_per_county');
            });
        }
    }

    public function down(): void
    {
        Schema::table('competition_settings', function (Blueprint $table) {
            if (Schema::hasColumn('competition_settings', 'number_of_counties')) {
                $table->renameColumn('number_of_counties', 'number_of_parishes');
            }
            if (Schema::hasColumn('competition_settings', 'contestants_per_county')) {
                $table->renameColumn('contestants_per_county', 'contestants_per_parish');
            }
        });
    }
};
