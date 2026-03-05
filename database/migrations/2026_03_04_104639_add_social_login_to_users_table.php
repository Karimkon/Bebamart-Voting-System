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
        Schema::table('users', function (Blueprint $table) {
            $table->string('provider')->nullable(); // google, facebook, apple, twitter
            $table->string('provider_id')->nullable();
            $table->string('provider_token')->nullable();
            $table->string('avatar')->nullable();
            $table->enum('role', ['user', 'admin', 'super_admin'])->default('user');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->ipAddress('last_login_ip')->nullable();
            $table->softDeletes();
            
            $table->index(['provider', 'provider_id']);
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['provider', 'provider_id', 'provider_token', 'avatar', 'role', 'is_active', 'last_login_at', 'last_login_ip']);
            $table->dropSoftDeletes();
        });
    }
};
