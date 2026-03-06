<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contestants', function (Blueprint $table) {
            $table->unsignedBigInteger('region_id')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('contestants', function (Blueprint $table) {
            $table->unsignedBigInteger('region_id')->nullable(false)->change();
        });
    }
};
