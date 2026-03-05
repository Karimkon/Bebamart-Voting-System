<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vote_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contestant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->nullable()->constrained('vote_packages')->nullOnDelete();
            $table->enum('order_type', ['vote_boost', 'premium_subscription']);
            $table->integer('votes_count');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('UGX');
            $table->decimal('price_per_vote', 10, 4)->nullable();
            $table->string('merchant_reference', 100)->unique();
            $table->string('pesapal_tracking_id', 100)->nullable();
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->boolean('votes_applied')->default(false);
            $table->timestamp('subscription_starts_at')->nullable();
            $table->timestamp('subscription_expires_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vote_orders');
    }
};
