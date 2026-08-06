<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Check if the admin already exists to avoid duplicates
        if (!User::where('email', 'admin@ktmwdc.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@ktmwdc.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',        // adjust if your role column is different
                'email_verified_at' => now(),
            ]);

            $this->command->info('Admin user created successfully!');
        } else {
            $this->command->info('Admin user already exists.');
        }
    }
}