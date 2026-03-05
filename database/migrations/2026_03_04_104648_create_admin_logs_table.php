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
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action'); // created, updated, deleted, etc
            $table->string('module'); // competition, contestant, votes, settings
            $table->morphs('loggable'); // The model that was acted upon
            $table->text('description');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->ipAddress('ip_address');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['module', 'action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_logs');
    }
};
