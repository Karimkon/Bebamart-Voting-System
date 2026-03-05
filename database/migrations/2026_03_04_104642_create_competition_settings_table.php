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
        Schema::create('competition_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->onDelete('cascade');
            $table->integer('number_of_parishes')->default(53);
            $table->integer('contestants_per_parish')->default(3);
            $table->integer('total_contestants')->storedAs('number_of_parishes * contestants_per_parish');
            $table->integer('number_of_rounds')->default(4);
            $table->integer('votes_per_user_per_day')->default(1);
            $table->integer('votes_per_contestant_per_day')->default(1);
            $table->json('promotion_rules')->nullable();
            $table->json('voting_rules')->nullable();
            $table->boolean('require_social_login')->default(true);
            $table->json('allowed_social_providers')->nullable(); // google, facebook, apple, twitter
            $table->timestamps();
            
            $table->unique('competition_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_settings');
    }
};
