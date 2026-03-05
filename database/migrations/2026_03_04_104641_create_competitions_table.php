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
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['beauty_pageant', 'awards', 'talent_show', 'tourism', 'other']);
            $table->text('description');
            $table->text('rules')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('logo')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['draft', 'upcoming', 'active', 'completed', 'archived'])->default('draft');
            $table->boolean('voting_enabled')->default(false);
            $table->integer('total_votes')->default(0);
            $table->integer('total_contestants')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'voting_enabled']);
            $table->index('start_date');
            $table->index('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};
