<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@ktmwdc.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_admin' => true,
            'user_code' => 'ADM-' . date('Y') . '-0001',
            // 'status' => 'active'  // Remove this line
        ]);

        // Create Client User
        User::create([
            'name' => 'Test Client',
            'email' => 'client@ktmwdc.com',
            'password' => Hash::make('client123'),
            'role' => 'client',
            'is_client' => true,
            'user_code' => 'CLT-' . date('Y') . '-0001',
        ]);

        // Create Driver User
        User::create([
            'name' => 'Test Driver',
            'email' => 'driver@ktmwdc.com',
            'password' => Hash::make('driver123'),
            'role' => 'driver',
            'is_driver' => true,
            'user_code' => 'DRV-' . date('Y') . '-0001',
        ]);

        // Create Equipment Owner User
        User::create([
            'name' => 'Test Equipment Owner',
            'email' => 'equipment@ktmwdc.com',
            'password' => Hash::make('equipment123'),
            'role' => 'equipment_owner',
            'is_equipment_owner' => true,
            'user_code' => 'EQO-' . date('Y') . '-0001',
        ]);

        // Create Property Owner User
        User::create([
            'name' => 'Test Property Owner',
            'email' => 'property@ktmwdc.com',
            'password' => Hash::make('property123'),
            'role' => 'property_owner',
            'is_property_owner' => true,
            'user_code' => 'PRP-' . date('Y') . '-0001',
        ]);
    }
}