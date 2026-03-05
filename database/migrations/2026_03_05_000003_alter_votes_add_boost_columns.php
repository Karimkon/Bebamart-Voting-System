<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->enum('vote_source', ['free', 'premium'])->default('free')->after('status');
            $table->foreignId('vote_order_id')->nullable()->after('vote_source')->constrained('vote_orders')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->dropForeign(['vote_order_id']);
            $table->dropColumn(['vote_source', 'vote_order_id']);
        });
    }
};
