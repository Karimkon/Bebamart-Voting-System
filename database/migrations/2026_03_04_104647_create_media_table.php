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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->morphs('mediable'); // Can belong to Competition, Contestant, etc
            $table->string('title')->nullable();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type'); // image, video, document
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size');
            $table->enum('category', ['profile', 'banner', 'gallery', 'promotional', 'event', 'other'])->default('other');
            $table->integer('order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
