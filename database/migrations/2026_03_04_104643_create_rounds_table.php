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
        Schema::create('rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Parish Stage, Regional Stage, National Stage, Final Stage
            $table->text('description')->nullable();
            $table->integer('round_number');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['pending', 'active', 'completed'])->default('pending');
            $table->integer('total_votes')->default(0);
            $table->integer('qualified_contestants')->default(0);
            $table->json('promotion_criteria')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['competition_id', 'round_number']);
            $table->index(['status', 'start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rounds');
    }
};
