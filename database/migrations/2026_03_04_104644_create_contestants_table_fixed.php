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
        Schema::create('contestants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->onDelete('cascade');
            $table->foreignId('parish_id')->constrained()->onDelete('cascade');
            $table->foreignId('region_id')->constrained()->onDelete('cascade');
            $table->foreignId('current_round_id')->nullable()->constrained('rounds')->onDelete('set null');
            $table->string('contestant_number')->unique();
            $table->string('full_name');
            $table->integer('age');
            $table->string('profile_photo')->nullable();
            $table->text('biography')->nullable();
            $table->text('talent_description')->nullable();
            $table->json('social_media_links')->nullable(); // {facebook, instagram, twitter, tiktok}
            $table->integer('total_votes')->default(0);
            $table->integer('current_round_votes')->default(0);
            $table->integer('ranking_position')->nullable();
            $table->enum('status', ['active', 'eliminated', 'qualified', 'winner'])->default('active');
            $table->boolean('is_promoted')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['competition_id', 'status']);
            $table->index(['parish_id', 'total_votes']);
            $table->index(['ranking_position']);
            $table->index('total_votes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contestants');
    }
};
