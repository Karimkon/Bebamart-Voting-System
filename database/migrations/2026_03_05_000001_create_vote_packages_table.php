<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vote_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('votes_count');
            $table->decimal('price', 10, 2);
            $table->string('currency', 10)->default('UGX');
            $table->text('description')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed default packages
        DB::table('vote_packages')->insert([
            ['name' => 'Starter', 'votes_count' => 100, 'price' => 110000, 'currency' => 'UGX', 'description' => '100 votes for your favourite contestant', 'is_popular' => false, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Popular', 'votes_count' => 500, 'price' => 550000, 'currency' => 'UGX', 'description' => '500 votes — most popular choice!', 'is_popular' => true, 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Power', 'votes_count' => 1000, 'price' => 1100000, 'currency' => 'UGX', 'description' => '1,000 votes — dominate the leaderboard', 'is_popular' => false, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('vote_packages');
    }
};
