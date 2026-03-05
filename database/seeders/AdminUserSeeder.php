<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@bebevotes.com'],
            [
                'name' => 'BebeVotes Admin',
                'email' => 'admin@bebevotes.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'provider' => 'local',
                'provider_id' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin user: admin@bebevotes.com / admin123');
    }
}
