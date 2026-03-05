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
        Schema::create('vote_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vote_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('contestant_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('competition_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // vote_cast, vote_flagged, vote_rejected, vote_validated
            $table->ipAddress('ip_address');
            $table->string('device_hash');
            $table->string('user_agent')->nullable();
            $table->text('details')->nullable();
            $table->json('fraud_indicators')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'action']);
            $table->index('ip_address');
            $table->index('device_hash');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vote_logs');
    }
};
