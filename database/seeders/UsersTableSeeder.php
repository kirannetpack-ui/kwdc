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
            'email' => 'admin.demo@kwdc.test',
            'password' => Hash::make(env('KWDC_DEMO_PASSWORD', 'local-demo-password')),
            'role' => 'admin',
            'is_admin' => true,
            'user_code' => 'ADM-' . date('Y') . '-0001',
            // 'status' => 'active'  // Remove this line
        ]);

        // Create Client User
        User::create([
            'name' => 'Test Client',
            'email' => 'client.demo@kwdc.test',
            'password' => Hash::make(env('KWDC_DEMO_PASSWORD', 'local-demo-password')),
            'role' => 'client',
            'is_client' => true,
            'user_code' => 'CLT-' . date('Y') . '-0001',
        ]);

        // Create Driver User
        User::create([
            'name' => 'Test Driver',
            'email' => 'driver.demo@kwdc.test',
            'password' => Hash::make(env('KWDC_DEMO_PASSWORD', 'local-demo-password')),
            'role' => 'driver',
            'is_driver' => true,
            'user_code' => 'DRV-' . date('Y') . '-0001',
        ]);

        // Create Equipment Owner User
        User::create([
            'name' => 'Test Equipment Owner',
            'email' => 'equipment.demo@kwdc.test',
            'password' => Hash::make(env('KWDC_DEMO_PASSWORD', 'local-demo-password')),
            'role' => 'equipment_owner',
            'is_equipment_owner' => true,
            'user_code' => 'EQO-' . date('Y') . '-0001',
        ]);

        // Create Property Owner User
        User::create([
            'name' => 'Test Property Owner',
            'email' => 'property.demo@kwdc.test',
            'password' => Hash::make(env('KWDC_DEMO_PASSWORD', 'local-demo-password')),
            'role' => 'property_owner',
            'is_property_owner' => true,
            'user_code' => 'PRP-' . date('Y') . '-0001',
        ]);
    }
}