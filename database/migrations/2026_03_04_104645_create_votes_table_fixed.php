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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('contestant_id')->constrained()->onDelete('cascade');
            $table->foreignId('competition_id')->constrained()->onDelete('cascade');
            $table->foreignId('round_id')->nullable()->constrained()->onDelete('set null');
            $table->ipAddress('ip_address');
            $table->string('device_hash');
            $table->string('user_agent')->nullable();
            $table->date('vote_date');
            $table->timestamp('voted_at');
            $table->enum('status', ['valid', 'suspicious', 'flagged', 'rejected'])->default('valid');
            $table->text('fraud_notes')->nullable();
            $table->timestamps();
            
            // Ensure one vote per user per contestant per day
            $table->unique(['user_id', 'contestant_id', 'vote_date'], 'unique_daily_vote');
            $table->index(['contestant_id', 'status']);
            $table->index(['ip_address', 'vote_date']);
            $table->index(['device_hash', 'vote_date']);
            $table->index('voted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
